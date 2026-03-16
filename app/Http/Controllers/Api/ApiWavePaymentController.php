<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UsersPharma;
use Illuminate\Http\Request;

class ApiWavePaymentController extends Controller
{
    /**
     * GET /api/getWalletByUserName/{username}
     * Retourne le montant du wallet de l'utilisateur
     */
    public function getByUsername(string $username)
    {
        $user = UsersPharma::where('phone_number', $username)
            ->select('amount')
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur introuvable.',
            ], 404);
        }

        return response()->json([
            'amount'     => $user->amount,
        ], 200);
    }
}
