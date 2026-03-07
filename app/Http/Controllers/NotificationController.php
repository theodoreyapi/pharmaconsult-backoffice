<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    public function storeToken(Request $request)
    {
        $request->validate([
            'userName' => 'required|string',
            'token' => 'required|string',
        ]);

        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post(env('API_BASE_URL_PHARMA') . '/notifications/register', [
            'userName' => $request->userName,
            'token' => $request->token,
        ]);

        if ($response->status() == 200 || $response->status() == 201) {
            return response()->json(['message' => 'Token FCM enregistré avec succès']);
        }

        return response()->json(['message' => 'Utilisateur non trouvé'], 404);
    }
}
