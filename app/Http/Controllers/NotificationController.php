<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function storeToken(Request $request)
    {
        $rules = [
            'userName' => 'required|string',
            'token' => 'required|string'
        ];

        $messages = [
            'userName.required' => "Votre session a expiré. Veuillez vous reconnecter",
            'token.required' => "Impossible d'avoir votre token",
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => collect($validator->errors()->all()),
            ], 422);
        }

        // Vérifier si un enregistrement existe déjà pour cet username
        $existing = FcmToken::where('username', $request->userName)
            ->first();

        if ($existing) {
            // Mettre à jour le token si différent
            if ($existing->token !== $request->token) {
                FcmToken::where('username', $request->userName)
                    ->update([
                        'token'      => $request->token,
                        'updated_at' => now(),
                    ]);
            }
        } else {
            // Insérer un nouveau enregistrement
            FcmToken::insert([
                'username'   => $request->userName,
                'token'      => $request->token,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token FCM enregistré avec succès.',
        ], 200);
    }
}
