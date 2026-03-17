<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rechargements;
use App\Models\UsersPharma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

    // ▶️ INITIATE PAYMENT
    public function initiatePayment(Request $request)
    {
        $rules = [
            'username' => 'required',
            'deposit_amount' => 'required|numeric|min:100',
            'payment_method' => 'required|string',
        ];

        $messages = [
            'username.required' => "Votre session a expiré. Veuillez vous reconnecter",
            'deposit_amount.required' => "Impossible d'avoir votre token",
            'payment_method.required' => "Impossible d'avoir votre token",
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => collect($validator->errors()->all()),
            ], 422);
        }

        $user = UsersPharma::where('phone_number', $request->username)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur introuvable.',
            ], 404);
        }

        // ── Vérifier la limite du wallet (100 000 XOF max) ───────────────
        // CORRECTION : la logique max() était incorrecte
        $newAmount = $user->amount + $request->deposit_amount;
        if ($newAmount > 100000) {
            return response()->json([
                'message' => "Le total de votre wallet après rechargement ({$newAmount} XOF) dépasserait la limite de 100 000 XOF. Veuillez saisir un montant inférieur.",
            ], 422);
        }

        try {

            $rechargement = Rechargements::create([
                'username' => $user->phone_number,
                'montant' => $request->deposit_amount,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'currency' => 'XOF',
                'checkout_session_id' => null, // cos-xxxx
            ]);

            $payload = [
                'amount' => (string) $request->deposit_amount,
                'currency' => 'XOF',
                'success_url' => 'https://new-version.sodalite-consulting.com/payment/wave/success/' . $rechargement->id_rechargement,
                'error_url'   => 'https://new-version.sodalite-consulting.com/payment/wave/error/' . $rechargement->id_rechargement,
                'client_reference' => (string) $user->phone_number,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer wave_ci_prod_rn721NIiORE7q7DvV924gboV_AoatbI6b-3NdfYjLr9RHOXeut3zmM0Cb-I643im7sENfaZBiho1eTkwbf5od5FTKymxOvgnCA',
                'Content-Type'  => 'application/json',
            ])->post('https://api.wave.com/v1/checkout/sessions', $payload);

            if (!$response->successful()) {
                Log::error('Wave error', $response->json());

                return response()->json([
                    'success' => false,
                    'message' => 'Erreur Wave',
                    'details' => $response->json(),
                ], 500);
            }

            $data = $response->json();

            $rechargement->update([
                'checkout_session_id' => $data['id'],
            ]);

            return response()->json([
                'success' => true,
                'rechargement_url' => $data['wave_launch_url'],
                'rechargement_id' => $rechargement->id_rechargement,
            ]);
        } catch (\Throwable $e) {
            Log::error('Wave Exception', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur',
            ], 500);
        }
    }

    // 🔍 GET PAYMENT
    public function getPayment($id)
    {
        return response()->json([
            'success' => true,
            'data' => Rechargements::find($id)
        ]);
    }
}
