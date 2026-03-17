<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rechargements;
use App\Models\Subscriptions;
use App\Models\Transfert;
use App\Models\UsersPharma;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiTransfertController extends Controller
{
    /**
     * POST /api/process
     *
     * Body JSON :
     * {
     *   "senderUsername" : "002250585831647",
     *   "receiverUsername": "002250700150855",
     *   "amount"         : 5000,
     *   "type"           : "user_to_user" | "user_to_pharmacy" | "pharmacy_to_user",
     *   "executeBy"      : "002250585831647"  (optionnel)
     * }
     *
     * Types supportés :
     *  - user_to_user      → users_pharma → users_pharma
     *  - user_to_pharmacy  → users_pharma → pharmacien
     *  - pharmacy_to_user  → pharmacien   → users_pharma
     */
    public function processTransfer(Request $request)
    {
        $request->validate([
            'senderUsername'   => 'required|string',
            'receiverUsername' => 'required|string',
            'amount'           => 'required|numeric|min:1',
            'type'             => 'required|in:user_to_user,user_to_pharmacy,pharmacy_to_user',
            'executeBy'        => 'nullable|string',
        ]);

        $senderUsername   = $request->input('senderUsername');
        $receiverUsername = $request->input('receiverUsername');
        $amount           = $request->input('amount');
        $type             = $request->input('type');
        $executeBy        = $request->input('executeBy', $senderUsername);

        // ── Récupérer expéditeur ──────────────────────────────────────────
        [$senderTable, $sender] = $this->findUser($senderUsername, $type, 'sender');
        if (!$sender) {
            return response()->json(['message' => "Expéditeur introuvable ($senderUsername)."], 404);
        }

        // ── Récupérer destinataire — créer le compte s'il n'existe pas ────
        [$receiverTable, $receiver] = $this->findUser($receiverUsername, $type, 'receiver');
        $receiverIsNew = false;
        $tempPassword  = null;

        if (!$receiver && $type === 'user_to_user') {
            // Générer un mot de passe temporaire
            $tempPassword  = rand(10000000, 99999999); // 8 chiffres
            $receiverIsNew = true;

            UsersPharma::insert([
                'username'     => $receiverUsername,
                'phone_number' => $receiverUsername,
                'first_name'   => 'Nouveau',
                'last_name'    => 'Utilisateur',
                'password'     => password_hash((string) $tempPassword, PASSWORD_BCRYPT, ['cost' => 10]),
                'role'         => 'PATIENT',
                'active'       => 'ACTIVE',
                'amount'       => 0,
                'last_amount'  => 0,
                'otp_verified' => false,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            $receiver      = UsersPharma::where('phone_number', $receiverUsername)->first();
            $receiverTable = 'users_pharma';
        }

        if (!$receiver) {
            return response()->json(['message' => "Destinataire introuvable ($receiverUsername)."], 404);
        }

        // ── Vérifier le solde de l'expéditeur ────────────────────────────
        if ($sender->amount < $amount) {
            return response()->json(['message' => 'Solde insuffisant pour effectuer ce transfert.'], 402);
        }

        // ── Transaction ───────────────────────────────────────────────────
        DB::transaction(function () use (
            $senderTable,
            $sender,
            $receiverTable,
            $receiver,
            $senderUsername,
            $receiverUsername,
            $amount,
            $type,
            $executeBy
        ) {
            $now = now();

            // Débiter l'expéditeur
            DB::table($senderTable)
                ->where('phone_number', $senderUsername)
                ->update([
                    'last_amount' => $sender->amount,
                    'amount'      => $sender->amount - $amount,
                    'updated_at'  => $now,
                ]);

            // Créditer le destinataire
            DB::table($receiverTable)
                ->where('phone_number', $receiverUsername)
                ->update([
                    'last_amount' => $receiver->amount,
                    'amount'      => $receiver->amount + $amount,
                    'updated_at'  => $now,
                ]);

            // Enregistrer le transfert (ligne DEBIT pour l'expéditeur)
            Transfert::insert([
                'type_operation'   => 'DEBIT',
                'amount'           => $amount,
                'raison'           => 'Monnaie envoyée',
                'sender_username'  => $senderUsername,
                'receiver_username' => $receiverUsername,
                'type'             => $type,
                'execute_by'       => $executeBy,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            // Enregistrer le transfert (ligne CREDIT pour le destinataire)
            Transfert::insert([
                'type_operation'   => 'CREDIT',
                'amount'           => $amount,
                'raison'           => 'Monnaie reçue',
                'sender_username'  => $senderUsername,
                'receiver_username' => $receiverUsername,
                'type'             => $type,
                'execute_by'       => $executeBy,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        });

        // ── Envoyer notification Firebase au destinataire ─────────────────
        $senderName = trim(($sender->first_name ?? '') . ' ' . ($sender->last_name ?? ''));
        (new FcmService())->sendToUser(
            $receiverUsername,
            '💰 Transfert reçu',
            "$senderName vous a envoyé " . number_format($amount, 0, ',', ' ') . " FCFA",
            ['type' => 'TRANSFERT', 'amount' => (string) $amount, 'sender' => $senderUsername]
        );

        // ── Si nouveau compte créé → envoyer identifiants par WhatsApp ────
        if ($receiverIsNew && $tempPassword) {
            $this->sendNewAccountWhatsApp($receiverUsername, $tempPassword, $amount, $senderName);
        }

        return response()->json(['message' => 'Transfert effectué avec succès.'], 201);
    }

    /**
     * Envoie les identifiants du nouveau compte par WhatsApp
     */
    private function sendNewAccountWhatsApp(string $phoneNumber, int $tempPassword, float $amount, string $senderName): void
    {
        $baseUrl    = "https://graph.facebook.com/v22.0/";
        $token      = env('WHATSAPP_TOKEN');
        $expediteur = env('WHATSAPP_EXPEDITEUR');

        // Nettoyer le numéro
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (str_starts_with($phone, '00')) {
            $phone = substr($phone, 2);
        }

        $montantFormate = number_format($amount, 0, ',', ' ');

        $body = "🎉 Bienvenue sur PharmaConsults !\n\n"
            . "$senderName vous a envoyé *{$montantFormate} FCFA*.\n\n"
            . "Un compte a été créé automatiquement pour vous :\n\n"
            . "📱 *Identifiant :* $phoneNumber\n"
            . "🔑 *Mot de passe temporaire :* $tempPassword\n\n"
            . "👉 Connectez-vous et changez votre mot de passe dès que possible.\n\n"
            . "Téléchargez l'application PharmaConsults pour accéder à votre wallet.";

        $response = Http::withToken($token)
            ->post("{$baseUrl}{$expediteur}/messages", [
                'messaging_product' => 'whatsapp',
                'to'                => $phone,
                'type'              => 'text',
                'text'              => ['body' => $body],
            ]);

        Log::info('WhatsApp nouveau compte', [
            'phone'  => $phone,
            'status' => $response->status(),
            'body'   => $response->json(),
        ]);
    }


    /**
     * GET /api/transactions/{username}
     * Retourne toutes les transactions d'un utilisateur :
     * - Transferts (DEBIT / CREDIT)
     * - Souscriptions (ABONNEMENT)
     * - Rechargements (RECHARGEMENT)
     * Triées par date décroissante
     */
    public function getTransactionsByUser(string $username)
    {
        $items = collect();

        // ── 1. Transferts ─────────────────────────────────────────────────
        $transferts = Transfert::where('sender_username', $username)
            ->orWhere('receiver_username', $username)
            ->get();

        foreach ($transferts as $t) {
            $isDebit           = $t->sender_username === $username;
            $operation         = $isDebit ? 'DEBIT' : 'CREDIT';
            $interlocuteur     = $isDebit ? $t->receiver_username : $t->sender_username;
            $interlocuteurNom  = $this->resolveDisplayName($interlocuteur, $t->type, $isDebit);

            $items->push([
                'id'               => $t->id_transfert,
                'category'         => 'TRANSFERT',
                'typeOperation'    => $operation,
                'amount'           => $t->amount,
                'raison'           => $t->raison,
                'label'            => $isDebit
                    ? "Envoi à $interlocuteurNom"
                    : "Reçu de $interlocuteurNom",
                'interlocuteurNom' => $interlocuteurNom,
                'type'             => $t->type,
                'senderUsername'   => $t->sender_username,
                'receiverUsername' => $t->receiver_username,
                'executeBy'        => $t->execute_by,
                'date'             => $t->created_at,
            ]);
        }

        // ── 2. Souscriptions ──────────────────────────────────────────────
        $subscriptions = Subscriptions::join('modules', 'subscriptions.module_id', '=', 'modules.id_module')
            ->leftJoin('services', function ($join) {
                $join->on('services.module_id', '=', 'subscriptions.module_id')
                    ->on('services.duration', '=', 'subscriptions.duree');
            })
            ->where('subscriptions.username', $username)
            ->select(
                'subscriptions.id_subscription',
                'subscriptions.description',
                'subscriptions.duree',
                'subscriptions.status',
                'subscriptions.valid_until',
                'subscriptions.created_at',
                'modules.libelle as module_libelle',
                'services.price as service_price'
            )
            ->get();

        foreach ($subscriptions as $s) {
            $items->push([
                'id'               => $s->id_subscription,
                'category'         => 'ABONNEMENT',
                'typeOperation'    => 'DEBIT',
                'amount'           => $s->service_price,
                'label'            => "Abonnement : {$s->module_libelle}",
                'interlocuteurNom' => 'PharmaConso',
                'type'             => 'subscription',
                'senderUsername'   => $username,
                'receiverUsername' => null,
                'executeBy'        => $username,
                'description'      => $s->description,
                'duree'            => $s->duree,
                'validUntil'       => $s->valid_until,
                'status'           => $s->status,
                'date'             => $s->created_at,
            ]);
        }

        // ── 3. Rechargements ──────────────────────────────────────────────
        $rechargements = Rechargements::where('username', $username)
            ->get();

        foreach ($rechargements as $r) {
            $items->push([
                'id'               => $r->id_rechargement,
                'category'         => 'RECHARGEMENT',
                'typeOperation'    => 'CREDIT',
                'amount'           => $r->montant,
                'label'            => "Rechargement via " . strtoupper($r->payment_method),
                'interlocuteurNom' => strtoupper($r->payment_method),
                'type'             => 'recharge',
                'senderUsername'   => null,
                'receiverUsername' => $username,
                'executeBy'        => $username,
                'transactionId'    => $r->transaction_id,
                'paymentMethod'    => $r->payment_method,
                'currency'         => $r->currency,
                'status'           => $r->status,
                'date'             => $r->created_at,
            ]);
        }

        // ── Trier tout par date décroissante ──────────────────────────────
        $sorted = $items->sortByDesc('date')->values();

        return response()->json($sorted, 200);
    }


    /**
     * Résout le nom affiché de l'interlocuteur
     * Si c'est un pharmacien (lié à une pharmacie), on retourne le nom de la pharmacie
     * Sinon on retourne prénom + nom du user
     */
    private function resolveDisplayName(string $username, string $type, bool $currentUserIsSender): string
    {
        // Déterminer si l'interlocuteur vient de la table pharmacien
        $isPharmacien =
            ($currentUserIsSender  && $type === 'user_to_pharmacy') ||
            (!$currentUserIsSender && $type === 'pharmacy_to_user');

        if ($isPharmacien) {
            // Récupérer le nom de la pharmacie liée au pharmacien
            $pharmacien = DB::table('pharmacien')
                ->leftJoin('pharmacy', 'pharmacien.pharmacy_id', '=', 'pharmacy.id_pharmacy')
                ->where('pharmacien.username', $username)
                ->select('pharmacy.name as pharmacy_name', 'pharmacien.first_name', 'pharmacien.last_name')
                ->first();

            if ($pharmacien) {
                return $pharmacien->pharmacy_name
                    ?? trim($pharmacien->first_name . ' ' . $pharmacien->last_name);
            }
            return $username;
        }

        // Sinon c'est un user normal
        $user = DB::table('users_pharma')
            ->where('username', $username)
            ->select('first_name', 'last_name')
            ->first();

        if ($user) {
            return trim($user->first_name . ' ' . $user->last_name);
        }

        return $username;
    }


    /**
     * Retrouve un utilisateur dans la bonne table selon le type et le rôle
     * Retourne [$tableName, $userObject]
     */
    private function findUser(string $username, string $type, string $role): array
    {
        // Déterminer la table selon le type et le rôle
        $usePharmacien =
            ($role === 'sender'   && $type === 'pharmacy_to_user') ||
            ($role === 'receiver' && $type === 'user_to_pharmacy');

        $table = $usePharmacien ? 'pharmacien' : 'users_pharma';

        $user = DB::table($table)->where('username', $username)->first();

        return [$table, $user];
    }
}
