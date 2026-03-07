<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GardeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!session('api_token')) {
            return redirect()->intended('logout');
        }
        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get(env('API_BASE_URL_PHARMA') . '/pharma/pharmacies/communeGardePharmacyNumber');

        if ($response->status() == 200) {
            $gardes = $response->json();

            $dateUpdate = Http::withOptions([
                'verify' => false
            ])->withHeaders([
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->get(env('API_BASE_URL_PHARMA') . '/periodes-garde');

            $periodes = $dateUpdate->json();

            $premier = $periodes[0] ?? null;

            return view('pharmacies.garde', compact('gardes', 'premier'));
        } else {
            // Gérer l'erreur
            return abort(500, 'Erreur lors du chargement des données.');
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
        if (!session('api_token')) {
            return redirect()->intended('logout');
        }
        $roles = [
            'commune' => 'required',
            'debut' => 'required',
            'fin' => 'required',
        ];
        $customMessages = [
            'commune.required' => "Veuillez sélectionner la commune.",
            'debut.required' => "Veuillez sélectionner la date début de la période.",
            'fin.required' => "Veuillez sélectionner la date de fin de la periode.",
        ];

        $request->validate($roles, $customMessages);

        $debut = Carbon::parse($request->debut)->getTimestampMs();
        $fin = Carbon::parse($request->fin)->getTimestampMs();

        $pharmacy = (array) $request->pharmacys;

        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->put(env('API_BASE_URL') . '/pharma/addGarde', [
            'startGardeDate' => (int) $debut,
            'endGardeDate' => (int) $fin,
            'pharmaciesIds' => array_map('intval', $pharmacy)
        ]);
        // dd($response->status() . ' </br>' . $response->body() . ' </br>' . ' ' . $debut . ' ' . $fin);
        if ($response->status() == 200) {
            return back()->with('succes',  "Les pharmacies ont été ajoutées a la garde.");
        } else {
            return back()->withErrors(["Impossible d'ajouter la garde. Veuillez réessayer!!"]);
        }
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

    public function getCommune()
    {
        if (!session('api_token')) {
            return redirect()->intended('logout');
        }
        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get(env('API_BASE_URL_PHARMA') . '/pharma/communes/search?page=0&size=1000');

        if ($response->status() == 200) {
            $communes = $response->json();

            return view('pharmacies.add-garde', compact('communes'));
        } else {
            // Gérer l'erreur
            return abort(500, 'Erreur lors du chargement des données.');
        }
    }

    public function storeGarde(Request $request)
    {
        if (!session('api_token')) {
            return redirect()->intended('logout');
        }
        $roles = [
            'debut' => 'required',
            'fin' => 'required',
        ];
        $customMessages = [
            'debut.required' => "Veuillez sélectionner la date début de la période.",
            'fin.required' => "Veuillez sélectionner la date de fin de la periode.",
        ];

        $request->validate($roles, $customMessages);

        $debut = Carbon::parse($request->debut)
            ->setTimezone('UTC')
            ->format('Y-m-d\TH:i:s.v\Z');
        $fin = Carbon::parse($request->fin)
            ->setTimezone('UTC')
            ->format('Y-m-d\TH:i:s.v\Z');

        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->put(env('API_BASE_URL_PHARMA') . '/periodes-garde/update', [
            'dateDebut' => $debut,
            'dateFin' => $fin,
        ]);
        //dd($response->status());
        if ($response->status() == 200) {
            return back()->with('succes',  "La période de garde a été mise à jour");
        } else {
            return back()->withErrors(["Impossible de mettre à jour la période de garde. Veuillez réessayer!!"]);
        }
    }
}
