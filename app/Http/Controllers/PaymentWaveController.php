<?php

namespace App\Http\Controllers;

use App\Models\Rechargements;
use App\Models\UsersPharma;
use App\Services\FcmService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentWaveController extends Controller
{
    /**
     * ✅ PAGE SUCCESS
     */
    public function success($id)
    {

        // ✅ Récupère l'objet complet, puis accède à l'attribut
        $payment = Rechargements::where('id_rechargement', $id)->first();

        if (!$payment) {
            return view('payment.error', ['message' => 'Rechargement introuvable']);
        }

        $checkoutId = $payment->checkout_session_id; // ✅ string correcte

        // Vérification réelle chez Wave (source de vérité)
        $response = Http::withHeaders([
            'Authorization' => 'Bearer wave_ci_prod_rn721NIiORE7q7DvV924gboV_AoatbI6b-3NdfYjLr9RHOXeut3zmM0Cb-I643im7sENfaZBiho1eTkwbf5od5FTKymxOvgnCA',
            'Content-Type'  => 'application/json',
        ])->get("https://api.wave.com/v1/checkout/sessions/$checkoutId");

        if (!$response->successful()) {
            return view('payment.error', [
                'message' => 'Impossible de vérifier le rechargement',
            ]);
        }

        $session = $response->json();

        if ($session['payment_status'] !== 'succeeded') {
            return view('payment.error', [
                'message' => 'Rechargement non confirmé',
            ]);
        }

        // ── Traitement idempotent (eviter double crédit) ──────────────────
        if ($payment->status !== 'success') {
            DB::transaction(function () use ($payment, $session) {

                // CORRECTION : récupérer le user DANS la transaction avec un lock
                $user = UsersPharma::where('phone_number', $payment->username)
                    ->lockForUpdate()
                    ->first();

                if (!$user) {
                    throw new \Exception("Utilisateur introuvable pour le rechargement {$payment->id_rechargement}");
                }

                // Mettre à jour le rechargement
                $payment->update([
                    'status'         => 'success',
                    'transaction_id' => $session['transaction_id'] ?? null,
                    'updated_at'     => Carbon::now(),
                ]);

                // CORRECTION : utiliser le montant frais depuis la DB (lockForUpdate)
                $user->update([
                    'last_amount' => $user->amount,
                    'amount'      => $user->amount + $payment->montant,
                    'updated_at'  => Carbon::now(),
                ]);
            });

            // ── Notifier l'utilisateur via FCM ────────────────────────────
            (new FcmService())->sendToUser(
                $payment->username,
                '✅ Rechargement réussi',
                number_format($payment->montant, 0, ',', ' ') . ' FCFA ont été ajoutés à votre wallet.',
                ['type' => 'RECHARGEMENT', 'amount' => (string) $payment->montant]
            );
        }

        return view('payment.success', [
            'amount'    => $session['amount'],
            'reference' => $session['transaction_id'] ?? 'N/A',
            'business'  => $session['business_name'] ?? 'PharmaConsults',
        ]);
    }

    /**
     * ❌ PAGE ERROR
     */
    public function error($id)
    {
        return view('payment.error', [
            'amount' => 0,
            'message' => 'Rechargement annulé ou échoué',
            'rechargement_id' => $id,
        ]);
    }
}
