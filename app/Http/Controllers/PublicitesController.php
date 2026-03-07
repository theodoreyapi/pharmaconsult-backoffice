<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PublicitesController extends Controller
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
        ])->get(env('API_BASE_URL_PHARMA') . '/publicites/get/all');

        if ($response->status() == 200) {
            $publicites = $response->json();

            return view('publicities.publicites', compact('publicites'));
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
            'image' => 'required',
            'libelle' => 'required',
            'lien' => 'required',
            'debut' => 'required',
            'fin' => 'required',
        ];
        $customMessages = [
            'image.required' => "Veuillez selectionner la photo de la publicite.",
            'libelle.required' => "Veuillez saisir le nom de la publicite.",
            'lien.required' => "Veuillez saisir le lien de la publicite.",
            'debut.required' => "Veuillez sélectionner la date de debut de la publicite.",
            'fin.required' => "Veuillez sélectionner la date de fin de la publicite.",
        ];

        $request->validate($roles, $customMessages);

        //dd($request->debut);

        $debut = Carbon::parse($request->debut)->format('Y-m-d\TH:i:s');
        //dd($debut);
        $fin = Carbon::parse($request->fin)->format('Y-m-d\TH:i:s');

        $response = Http::withOptions([
            'verify' => false
        ])->asMultipart()
            ->withHeaders([
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                'Accept' => 'application/json',
            ])->attach(
                'medicamentPicture',
                file_get_contents($request->file('image')->getRealPath()),
                $request->file('image')->getClientOriginalName()
            )->post(env('API_BASE_URL_PHARMA') . '/publicites/create', [
                'publiciteCmd' => json_encode([
                    'name' => $request->libelle,
                    'lien' => $request->lien,
                    'startDate' => $debut,
                    'endDate' => $fin,
                ])
            ]);
        //dd($response->status() . ' </br>' . $response->body(). ' </br>' . $response->json(). ' </br>' . $request->file('image') . ' </br>' . $request->commune);
        if ($response->status() == 201) {
            return back()->with('succes',  "La publicité " . $request->name . " a été ajoutée avec succès.");
        } else {
            return back()->withErrors(["Impossible d'ajouter " . $request->name . ". Veuillez réessayer!!"]);
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
            'image' => '',
            'libelle' => 'required',
            'lien' => 'required',
            'debut' => 'required',
            'fin' => 'required',
            'statut' => 'required',
        ];
        $customMessages = [
            'libelle.required' => "Veuillez saisir le nom de la publicite.",
            'lien.required' => "Veuillez saisir le lien de la publicite.",
            'debut.required' => "Veuillez sélectionner la date de debut de la publicite.",
            'fin.required' => "Veuillez sélectionner la date de fin de la publicite.",
            'statut.required' => "Veuillez sélectionner la date de fin de la publicite.",
        ];

        $request->validate($roles, $customMessages);

        $debut = Carbon::parse($request->debut)->format('Y-m-d\TH:i:s');
        //dd($debut);
        $fin = Carbon::parse($request->fin)->format('Y-m-d\TH:i:s');

        $publiciteCmd = [
            'name' => $request->libelle,
            'lien' => $request->lien,
            'startDate' => $debut,
            'endDate' => $fin,
            'status' => $request->statut,
        ];

        if ($request->file('image') == null) {

            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                    'Accept' => 'application/json',
                ])
                ->attach(
                    'publiciteCmd',
                    json_encode($publiciteCmd),
                    'publiciteCmd.json' // nom temporaire
                )
                ->put(env('API_BASE_URL_PHARMA') . '/publicites/update/' . $id);


            dd($response->status() . ' </br>' . $response->body() . ' </br>' . $response->json() . ' </br>' . json_encode([
                'name' => $request->libelle,
                'lien' => $request->lien,
                'startDate' => $debut,
                'endDate' => $fin,
                'status' => $request->statut,
                'id' => $id,
            ]));

            if ($response->status() == 200) {
                return back()->with('succes',  "Modification éffectuée ");
            } else {
                return back()->withErrors(["Impossible de modifier. Veuillez réessayer!!"]);
            }
        } else {
            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                    'Accept' => 'application/json',
                ])
                ->attach(
                    'publiciteCmd',
                    json_encode($publiciteCmd),
                    'publiciteCmd.json'
                )
                ->attach(
                    'pubicitePicture',
                    fopen($request->file('image')->getPathname(), 'r'),
                    $request->file('image')->getClientOriginalName()
                )
                ->put(env('API_BASE_URL_PHARMA') . '/publicites/update/' . $id);

            //dd($response->status() . ' </br>' . $response->body() . ' </br>' . $response->json());

            if ($response->status() == 200) {
                return back()->with('succes',  "Modification éffectuée ");
            } else {
                return back()->withErrors(["Impossible de modifier. Veuillez réessayer!!"]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
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
        ])->delete(env('API_BASE_URL_PHARMA') . '/publicites/delete/' . $id);

        if ($response->status() == 200) {
            return back()->with('succes',  "Suppression éffectuée");
        } else {
            return back()->withErrors(["Impossible de supprimer. Veuillez réessayer!!"]);
        }
    }
}
