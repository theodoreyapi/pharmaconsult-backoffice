<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ConditionController extends Controller
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
        ])->get(env('API_BASE_URL_PHARMA') . '/pharma/parametres-generaux/getbyType/CONDITIONS GENERALES');

        if ($response->status() == 200) {
            $abouts = $response->json();

            return view('termes.terms.list-terms', compact('abouts'));
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
            'contenu' => 'required',
        ];
        $customMessages = [
            'contenu.required' => "Veuillez saisir au moins un mot.",
        ];

        $request->validate($roles, $customMessages);

        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post(env('API_BASE_URL_PHARMA') . '/pharma/parametres-generaux/create', [
            'libelle' => 'CONDITIONS GENERALES',
            'contenu' => $request->contenu,
            'type' => 'CONDITIONS GENERALES',
        ]);


        if ($response->status() == 201 || $response->status() == 200) {
            return back()->with('succes',  "Ajout avec succès.");
        } else {
            return back()->withErrors(["Impossible d'ajouter. Veuillez réessayer!!"]);
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
        if (!session('api_token')) {
            return redirect()->intended('logout');
        }

        $roles = [
            'contenu' => 'required',
        ];
        $customMessages = [
            'contenu.required' => "Veuillez saisir au moins un mot.",
        ];

        $request->validate($roles, $customMessages);

        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->put(env('API_BASE_URL_PHARMA') . '/pharma/parametres-generaux/update/' . $id, [
            'libelle' => 'CONDITIONS GENERALES',
            'contenu' => $request->contenu,
            'type' => 'CONDITIONS GENERALES',
        ]);

        /* dd(
            $response->status() . ' </br>' .
                $response->body() . ' </br>' .
                json_encode($response->json(), JSON_PRETTY_PRINT)
        ); */

        if ($response->status() == 201 || $response->status() == 200) {
            return back()->with('succes',  "Mise a jour avec succès.");
        } else {
            return back()->withErrors(["Impossible de mettre a jour. Veuillez réessayer!!"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
