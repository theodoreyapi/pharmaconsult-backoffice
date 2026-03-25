<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Models\Rechargements;
use App\Models\RequestMedicament;
use App\Models\ReservationMedicament;
use App\Models\Review;
use App\Models\Subscriptions;
use App\Models\Transfert;
use App\Models\UsersPharma;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApiUsersPharmaController extends Controller
{

    /**
     * POST /api/save
     * Body JSON : { "username", "email", "phoneNumber", "firstName", "lastName", "typeUser", "password" }
     * Crée le compte avec active = INACTIVE, envoie un OTP par WhatsApp
     */
    public function store(Request $request)
    {
        $request->validate([
            'username'    => 'required|string',
            'phoneNumber' => 'required|string',
            'email'       => 'nullable|email',
            'firstName'   => 'required|string',
            'lastName'    => 'required|string',
            'typeUser'    => 'required|string',
            'password'    => 'required|string|min:6',
        ]);

        $username    = $request->input('username');
        $phoneNumber = $request->input('phoneNumber');

        // Vérifier si le compte existe déjà
        $exists = DB::table('users_pharma')
            ->where('username', $username)
            ->orWhere('phone_number', $phoneNumber)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Ce compte existe déjà.'], 409);
        }

        // Générer OTP à 4 chiffres valable 2 minutes
        $otpCode  = rand(1000, 9999);
        $expireAt = Carbon::now()->addMinutes(2);

        // Créer le compte avec active = INACTIVE
        DB::table('users_pharma')->insert([
            'username'      => $username,
            'phone_number'  => $phoneNumber,
            'email'         => $request->input('email'),
            'first_name'    => $request->input('firstName'),
            'last_name'     => $request->input('lastName'),
            'role'          => $request->input('typeUser'),
            'password'      => password_hash($request->input('password'), PASSWORD_BCRYPT, ['cost' => 10]),
            'active'        => 'INACTIVE',
            'amount'        => 0,
            'last_amount'   => 0,
            'otp_code'      => $otpCode,
            'otp_expire_at' => $expireAt,
            'otp_verified'  => false,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // Envoyer OTP par WhatsApp
        $this->sendWhatsApp($phoneNumber, $otpCode, $request->input('firstName'));

        return response()->json([
            'message'  => 'Compte créé avec succès. Veuillez valider votre numéro via le code OTP envoyé.',
            'expireAt' => $expireAt->toDateTimeString(),
        ], 201);
    }

    /**
     * POST /api/reinitialiser/password
     * Body JSON : { "username": "002250585831647", "newPassword": "..." }
     * L'OTP doit avoir été validé (otp_verified = true) avant d'appeler cette route
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'username'    => 'required|string',
            'newPassword' => 'required|string|min:6',
        ]);

        $username = $request->input('username');
        $user     = DB::table('users_pharma')->where('username', $username)->first();

        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable.'], 404);
        }

        // Vérifier que l'OTP a bien été validé avant de réinitialiser
        if (!$user->otp_verified) {
            return response()->json([
                'message' => 'Veuillez valider votre code OTP avant de réinitialiser votre mot de passe.',
            ], 403);
        }

        DB::table('users_pharma')
            ->where('username', $username)
            ->update([
                'password'     => password_hash($request->input('newPassword'), PASSWORD_BCRYPT, ['cost' => 10]),
                'otp_verified' => false,
                'updated_at'   => now(),
            ]);

        return response()->json(['message' => 'Mot de passe réinitialisé avec succès.'], 200);
    }

// =========================================================================
    // OTP
    // =========================================================================

    /**
     * POST /api/otp/generate
     * Body JSON : { "username": "002250585831647", "channel": "whatsapp" | "email" }
     * Génère un OTP à 4 chiffres valable 2 minutes
     */
    public function generateOtp(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'channel'  => 'required|in:whatsapp,email',
        ]);

        $username = $request->input('username');
        $channel  = $request->input('channel');

        $user = UsersPharma::where('phone_number', $username)
            ->orWhere('email', $username)
            ->first();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable.'], 404);
        }

        $otpCode  = rand(1000, 9999);
        $expireAt = Carbon::now()->addMinutes(2);

        UsersPharma::where('phone_number', $username)
            ->orWhere('email', $username)
            ->update([
                'otp_code'      => $otpCode,
                'otp_expire_at' => $expireAt,
                'otp_verified'  => false,
                'updated_at'    => now(),
            ]);

        if ($channel === 'whatsapp') {
            $this->sendWhatsApp($user->phone_number, $otpCode, $user->first_name);
        } else {
            $this->sendEmail($user->email, $otpCode, $user->first_name);
        }

        return response()->json([
            'message'  => "Code OTP envoyé par $channel avec succès.",
            'expireAt' => $expireAt->toDateTimeString(),
        ], 200);
    }

    /**
     * POST /api/otp/validate
     * Body JSON : { "username": "002250585831647", "otpCode": 1234 }
     */
    public function validateOtp(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'otpCode'  => 'required|integer',
        ]);

        $username = $request->input('username');
        $otpCode  = $request->input('otpCode');

        $user = UsersPharma::where('phone_number', $username)
            ->orWhere('email', $username)
            ->first();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable.'], 404);
        }

        // Vérifier expiration
        if (!$user->otp_expire_at || Carbon::now()->isAfter(Carbon::parse($user->otp_expire_at))) {
            return response()->json(['message' => 'Le code OTP a expiré. Veuillez en générer un nouveau.'], 410);
        }

        // Vérifier le code
        if ((int) $user->otp_code !== (int) $otpCode) {
            return response()->json(['message' => 'Code OTP incorrect.'], 401);
        }

        // Marquer comme vérifié et effacer le code
        UsersPharma::where('phone_number', $username)
            ->orWhere('email', $username)
            ->update([
                'otp_verified'  => true,
                'otp_code'      => null,
                'otp_expire_at' => null,
                'updated_at'    => now(),
            ]);

        return response()->json(['message' => 'Code OTP validé avec succès.'], 200);
    }


    /**
     * Envoie le code OTP par WhatsApp via Meta Cloud API
     */
    private function sendWhatsApp(string $phoneNumber, int $otpCode, string $firstName): void
    {
        $baseUrl    = "https://graph.facebook.com/v22.0/";
        $token      = "EAAUTxrzGDCYBOzf68oo1UXpw1a7PmoFn6n9GCeAZAW2gPA8L3cyI3XNuh9yZCYkZAUJ0GHQRcGr6RscWDyHZC5T0nD8VaeJvfIBYB2WZBVnvODIWugASxJN2aW2UuA6k2YxJvncrQrz5QkBeZA9PNNORHpbigGmOAS5rZAbOvEToeqc3o4ZBoRLlGDcuM3DdpfKWPZBMxNdCYeQbgQTeTUetS5Hz9SwEZD";
        $expediteur = "666101623244809";

        // Nettoyer le numéro : retirer les espaces, +, et le préfixe 00
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        if (str_starts_with($phone, '00')) {
            $phone = substr($phone, 2); // 0022507... → 2250585831647
        }

        Http::withToken($token)->post("{$baseUrl}{$expediteur}/messages", [
            'messaging_product' => 'whatsapp',
            'to'                => $phone,
            'type'              => 'text',
            'text'              => [
                'body' => "Bonjour $firstName,\n\nVotre code de vérification PharmaConsults est : *$otpCode*\n\nCe code est valable 2 minutes.\nNe le partagez avec personne.",
            ],
        ]);
    }

    /**
     * Envoie le code OTP par Email via SMTP Gmail
     */
    private function sendEmail(string $email, int $otpCode, string $firstName): void
    {
        Mail::send([], [], function ($message) use ($email, $otpCode, $firstName) {
            $message
                ->to($email)
                ->from(
                    env('MAIL_FROM_ADDRESS', 'contact.pharmaconsults@gmail.com'),
                    env('MAIL_FROM_NAME', 'PharmaConsults')
                )
                ->subject('Votre code de vérification PharmaConsults')
                ->html("
                    <div style='font-family: Arial, sans-serif; max-width: 500px; margin: auto;'>
                        <h2 style='color: #115010;'>Code de vérification</h2>
                        <p>Bonjour <strong>$firstName</strong>,</p>
                        <p>Votre code de vérification PharmaConso est :</p>
                        <div style='font-size: 36px; font-weight: bold; letter-spacing: 10px;
                                    background: #41BA3E; padding: 20px; text-align: center;
                                    border-radius: 8px; color: #115010;'>
                            $otpCode
                        </div>
                        <p style='margin-top: 20px;'>Ce code est valable <strong>2 minutes</strong>.</p>
                        <p style='color: #999; font-size: 12px;'>Ne partagez ce code avec personne.</p>
                    </div>
                ");
        });
    }


    /**
     * DELETE /api/delete/{username}
     * Si l'utilisateur a des transactions ou rechargements → désactive (status = DELETE)
     * Sinon → supprime définitivement
     */
    public function deleteAccount(string $username)
    {
        // Vérifier que l'utilisateur existe
        $user = UsersPharma::where('phone_number', $username)->first();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable.'], 404);
        }

        // Vérifier s'il a des transactions (envoyées ou reçues)
        $hasTransferts = Transfert::where('sender_username', $username)
            ->orWhere('receiver_username', $username)
            ->exists();

        // Vérifier s'il a des rechargements
        $hasRechargements = Rechargements::where('username', $username)
            ->exists();

        if ($hasTransferts || $hasRechargements) {
            // Désactiver le compte
            UsersPharma::where('phone_number', $username)
                ->update([
                    'active'     => 'DELETE',
                    'updated_at' => now(),
                ]);

            return response()->json([
                'message' => 'Votre compte a été désactivé.',
                'deleted' => false,
            ], 200);
        }

        // Supprimer définitivement le compte et ses données liées
        DB::transaction(function () use ($username, $user) {
            // Supprimer la photo de profil si locale
            if ($user->profile_picture) {
                $oldFileName = basename(parse_url($user->profile_picture, PHP_URL_PATH));
                $oldFilePath = public_path('users/' . $oldFileName);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            Subscriptions::where('username', $username)->delete();
            FcmToken::where('username', $username)->delete();
            RequestMedicament::where('username', $username)->delete();
            ReservationMedicament::where('user_name', $username)->delete();
            Review::where('username', $username)->delete();
            UsersPharma::where('phone_number', $username)->delete();
        });

        return response()->json([
            'message' => 'Votre compte a été supprimé définitivement.',
            'deleted' => true,
        ], 200);
    }


    /**
     * POST /api/changePassword
     * Body JSON : { "username": "...", "lastPassword": "...", "newPassword": "..." }
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'username'     => 'required|string',
            'lastPassword' => 'required|string',
            'newPassword'  => 'required|string|min:6',
        ]);

        $username = $request->input('username');

        // Vérifier que l'utilisateur existe
        $user = UsersPharma::where('phone_number', $username)->first();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable.'], 404);
        }

        // Vérifier l'ancien mot de passe
        if (!password_verify($request->input('lastPassword'), $user->password)) {
            return response()->json(['message' => 'Mot de passe actuel incorrect.'], 401);
        }

        // Mettre à jour avec le nouveau mot de passe hashé en BCrypt
        UsersPharma::where('phone_number', $username)
            ->update([
                'password'   => password_hash($request->input('newPassword'), PASSWORD_BCRYPT, ['cost' => 10]),
                'updated_at' => now(),
            ]);

        return response()->json(['message' => 'Mot de passe modifié avec succès.'], 200);
    }


    /**
     * PUT /api/update
     * Body JSON : { "username": "...", "email": "...", "firstName": "...", "lastName": "..." }
     */
    public function update(Request $request)
    {
        $request->validate([
            'username'  => 'required|string',
            'email'     => 'nullable|email',
            'firstName' => 'nullable|string',
            'lastName'  => 'nullable|string',
        ]);

        $username = $request->input('username');

        // Vérifier que l'utilisateur existe
        $user = UsersPharma::where('phone_number', $username)->first();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable.'], 404);
        }

        // Vérifier que le nouvel email n'est pas déjà utilisé par un autre utilisateur
        $email = $request->input('email');
        if ($email && $email !== $user->email) {
            $emailExists = UsersPharma::where('email', $email)
                ->where('phone_number', '!=', $username)
                ->exists();

            if ($emailExists) {
                return response()->json(['message' => 'Cet email est déjà utilisé.'], 409);
            }
        }

        // Mettre à jour les informations
        UsersPharma::where('username', $username)
            ->update([
                'email'      => $email ?? $user->email,
                'first_name' => $request->input('firstName') ?? $user->first_name,
                'last_name'  => $request->input('lastName') ?? $user->last_name,
                'updated_at' => now(),
            ]);

        return response()->json(['message' => 'Informations mises à jour avec succès.'], 200);
    }


    /**
     * PUT /api/updateProfilePicture
     * Multipart form-data : userName (string) + userPicture (file)
     * Retourne l'URL de la nouvelle photo en plain text
     */
    public function updatePicture(Request $request)
    {
        $request->validate([
            'userName'    => 'required|string',
            'userPicture' => 'required|file|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $username = $request->input('userName');

        // Vérifier que l'utilisateur existe
        $user = UsersPharma::where('phone_number', $username)->first();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable.'], 404);
        }

        // Supprimer l'ancienne photo si elle existe (hors URL externe)
        if ($user->profile_picture) {
            // Extraire le nom du fichier depuis l'URL : admin/public/users/fichier.jpg
            $oldFileName = basename(parse_url($user->profile_picture, PHP_URL_PATH));
            $oldFilePath = public_path('users/' . $oldFileName);
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        // Sauvegarder la nouvelle photo
        $file = $request->file('userPicture');
        $fileName = uniqid($username . 'users_profile_') . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('users'), $fileName);
        $imageUrl = url('pharma/public/users/' . $fileName);

        // Mettre à jour en base
        UsersPharma::where('phone_number', $username)
            ->update([
                'profile_picture' => $imageUrl,
                'updated_at'      => now(),
            ]);

        // Retourne l'URL en plain text (comme attendu par Flutter : response.body.trim())
        return response($imageUrl, 200)->header('Content-Type', 'text/plain');
    }


    /**
     * POST /api/login
     */
    public function login(Request $request)
    {
        $rules = [
            'username' => 'required|string',
            'password' => 'required|string'
        ];

        $messages = [
            'username.required' => 'Veuillez saisir votre telephone ou email.',
            'password.required' => 'Veuillez saisir votre mot de passe.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => collect($validator->errors()->all()),
            ], 422);
        }

        // Récupérer l'utilisateur par username ou phone_number
        $user = UsersPharma::where('phone_number', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user || !password_verify($request->password, $user->password)) {
            return response()->json([
                'message' => "Identifiants incorrects.",
            ], 401);
        }

        // Générer un token JWT simple (vous pouvez utiliser tymon/jwt-auth ou Laravel Sanctum)
        // $token = $this->generateToken($user);

        // Récupérer les souscriptions actives uniquement (valid_until > now)
        $subscriptions = Subscriptions::join('modules', 'subscriptions.module_id', '=', 'modules.id_module')
            ->where('subscriptions.username', $user->username)
            ->where('subscriptions.valid_until', '>', Carbon::now())
            ->where('subscriptions.status', 'active')
            ->select(
                'subscriptions.id_subscription as id',
                'subscriptions.description',
                'subscriptions.duree',
                'subscriptions.status',
                'subscriptions.username as userName',
                'subscriptions.valid_until as validUntil',
                'subscriptions.created_at as dateCreate',
                'subscriptions.updated_at as dateUpdate',
                'modules.id_module',
                'modules.libelle as module_libelle',
                'modules.description as module_description',
                'modules.created_at as module_dateCreate',
                'modules.updated_at as module_dateUpdate'
            )
            ->get();

        // Formatter les souscriptions
        $formattedSubscriptions = $subscriptions->map(function ($sub) {
            return [
                'id'          => $sub->id,
                'moduleDto'   => [
                    'id'          => $sub->id_module,
                    'libelle'     => $sub->module_libelle,
                    'description' => $sub->module_description,
                    'dateCreate'  => $sub->module_dateCreate,
                    'dateUpdate'  => $sub->module_dateUpdate,
                ],
                'duree'       => $sub->duree,
                'dateCreate'  => $sub->dateCreate,
                'dateUpdate'  => $sub->dateUpdate,
                'validUntil'  => $sub->validUntil,
                'description' => $sub->description,
                'status'      => $sub->status,
                'userName'    => $sub->userName,
            ];
        });

        return response()->json([
            'user' => [
                'id'          => $user->id_user,
                'username'    => $user->username,
                'email'       => $user->email,
                'firstName'   => $user->first_name,
                'lastName'    => $user->last_name,
                'phoneNumber' => $user->phone_number,
                'role'        => $user->role,
                'active'      => $user->active,
                'amount'     => $user->amount,
                'lastAmount' => $user->last_amount,
                'profilePicture' => $user->profile_picture,
                'subscriptions' => $formattedSubscriptions,
            ],
        ], 200);
    }

    /**
     * Génère un token JWT simple avec tymon/jwt-auth
     * Si vous utilisez Sanctum, remplacez par : $user->createToken('auth_token')->plainTextToken
     */
    private function generateToken($user): string
    {
        // Option 1 : tymon/jwt-auth
        // return auth('api')->login($user);

        // Option 2 : Laravel Sanctum
        // $userModel = \App\Models\UserPharma::find($user->id_user);
        // return $userModel->createToken('auth_token')->plainTextToken;

        // Option 3 : Token JWT manuel (pour demo, à remplacer en prod)
        $header  = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'sub' => $user->username,
            'iss' => strtoupper($user->role ?? 'USER'),
            'iat' => time(),
            'exp' => time() + 3600,
        ]));
        $signature = hash_hmac('sha256', "$header.$payload", config('app.key'));
        return "$header.$payload.$signature";
    }
}
