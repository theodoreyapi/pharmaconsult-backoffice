<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscriptions;
use App\Models\UsersPharma;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ApiUsersPharmaController extends Controller
{
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
