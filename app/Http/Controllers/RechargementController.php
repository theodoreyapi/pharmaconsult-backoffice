<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RechargementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $responses = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get(env('API_BASE_URL') . '/pharma/' . session('user_data')['wallet']['pharmacyId'] . '/wallet-balance');

        $wallets = $responses->json();

        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get(env('API_BASE_URL_PHARMA') . '/pharma/operations/byPharmacy/' . session('user_data')['wallet']['pharmacyId']);

        if ($response->status() == 200 || $response->status() == 204) {
            $requetes = collect($response->json())->where('designation', '==', 'RECHARGEMENT')->values();

            return view('pharmacies.rechargement', compact('requetes', 'wallets'));
        } else {
            // Gérer l'erreur
            return abort(500, 'Erreur lors du chargement des données.');
        }
    }

    public function init(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        try {
            //Génération du transactionId équivalent à Flutter
            $now = Carbon::now(); // équivaut à DateTime.now()
            $random = random_int(0, 9999); // équivaut à Random().nextInt(9999)
            $transactionId = $now->format('dmYHisv') . $random;
            //(jour/mois/année/heure/minute/seconde/millisecondes + random)


            $response = Http::withOptions([
                'verify' => false
            ])->withHeaders([
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(env('API_BASE_URL_PHARMA') . '/pharma/cinetpay/payment', [
                'username' => session('user_data')['email'],
                'amount' => $request->money,
                'channels' => 'ALL',
                'description' => 'Rechargement de compte Pharmacie',
                'transaction_id' => $transactionId,
                'currency' => 'XOF',
            ]);

            if ($response->status() == 200 || $response->status() == 201) {
                return response()->json([
                    'status' => true,
                    'message' => 'Rechargement initialisé avec succès',
                    'url' => $response->body() // ou json_decode($response->body()) si c’est du JSON
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de l’initialisation du rechargement',
                'error' => $response->body()
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur serveur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
