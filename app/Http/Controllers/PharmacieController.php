<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PharmacieController extends Controller
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
        ])->get(env('API_BASE_URL') . '/pharma/all');

        if ($response->status() == 200) {
            $pharmacys = $response->json();

            return view('pharmacies.pharmacy', compact('pharmacys'));
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
            'photo' => '',
            'name' => 'required',
            'adresse' => 'required',
            'responsable' => 'required',
            'commune' => 'required',
            'phone' => '',
            'whatsapp' => '',
            'longitude' => '',
        ];
        $customMessages = [
            'name.required' => "Veuillez saisir le nom d ela pharmacie.",
            'responsable.required' => "Veuillez saisir le nom du pharmacie.",
            'commune.required' => "Veuillez sélectionner la commune.",
        ];

        $request->validate($roles, $customMessages);

        if ($request->file('photo') == null) {
            //dd($request->all());
            $response = Http::withOptions([
                'verify' => false
            ])->withHeaders([
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(env('API_BASE_URL') . '/pharma/add', [
                'pharmacyCmd' => json_encode([
                    'name' => $request->name,
                    'address' => $request->adresse,
                    'phoneNumber' => $request->phone ?? '',
                    'whatsAppPhoneNumber' => $request->whatsapp ?? '',
                    'ownerName' => $request->responsable,
                    'gpsCoordinates' => $request->longitude,
                    'startGardeDate' => 0,
                    'endGardeDate' => 0,
                    'communeId' => $request->commune,
                ])
            ]);
            // dd($response->status() . ' </br>' . $response->body());
            if ($response->status() == 201) {
                return back()->with('succes',  "Vous avez ajouter " . $request->name);
            } else {
                return back()->withErrors(["Impossible d'ajouter " . $request->name . ". Veuillez réessayer!!"]);
            }
        } else {
            $response = Http::withOptions([
                'verify' => false
            ])->asMultipart()
                ->withHeaders([
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                    'Accept' => 'application/json',
                ])->attach(
                    'facadeImage',
                    file_get_contents($request->file('photo')->getRealPath()),
                    $request->file('photo')->getClientOriginalName()
                )->post(env('API_BASE_URL') . '/pharma/add', [
                    'pharmacyCmd' => json_encode([
                        'name' => $request->name,
                        'address' => $request->adresse,
                        'phoneNumber' => $request->phone ?? '',
                        'whatsAppPhoneNumber' => $request->whatsapp ?? '',
                        'ownerName' => $request->responsable,
                        'gpsCoordinates' => $request->longitude,
                        'startGardeDate' => 0,
                        'endGardeDate' => 0,
                        'communeId' => $request->commune,
                    ])
                ]);
            //dd($response->status() . ' </br>' . $response->body(). ' </br>' . $response->json(). ' </br>' . $request->file('photo') . ' </br>' . $request->commune);
            if ($response->status() == 201) {
                return back()->with('succes',  "Vous avez ajouter " . $request->name);
            } else {
                return back()->withErrors(["Impossible d'ajouter " . $request->name . ". Veuillez réessayer!!"]);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
        ])->get(env('API_BASE_URL_PHARMA') . '/pharma/assurances/getAll');

        if ($response->status() == 200) {
            $assurances = $response->json();

            return view('pharmacies.asso-assurance', compact('assurances', 'id'));
        } else {
            return back()->withErrors(["Impossible de charger les assurances. Veuillez réessayer!!"]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
        ])->get(env('API_BASE_URL_PHARMA') . '/pharma/payment-methods/all');

        if ($response->status() == 200) {
            $paiements = $response->json();

            return view('pharmacies.asso-paiement', compact('paiements', 'id'));
        } else {
            return back()->withErrors(["Impossible de charger les moyens de paiement. Veuillez réessayer!!"]);
        }
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
            'photo' => '',
            'name' => 'required',
            'adresse' => 'required',
            'responsable' => 'required',
            'commune' => 'required',
            'phone' => '',
            'whatsapp' => '',
            'longitude' => '',
        ];
        $customMessages = [
            'name.required' => "Veuillez saisir le nom d ela pharmacie.",
            'responsable.required' => "Veuillez saisir le nom du pharmacie.",
            'commune.required' => "Veuillez sélectionner la commune.",
        ];

        $request->validate($roles, $customMessages);

        if ($request->file('photo') == null) {
            //dd($request->all());
            $response = Http::withOptions([
                'verify' => false
            ])->asMultipart()
                ->withHeaders([
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                    'Accept' => 'application/json',
                ])->post(env('API_BASE_URL') . '/pharma/update/' . $id, [
                    'pharmacyCmd' => json_encode([
                        'name' => $request->name,
                        'address' => $request->adresse,
                        'phoneNumber' => $request->phone ?? '',
                        'whatsAppPhoneNumber' => $request->whatsapp ?? '',
                        'ownerName' => $request->responsable,
                        'gpsCoordinates' => $request->longitude,
                        'startGardeDate' => 0,
                        'endGardeDate' => 0,
                        'communeId' => $request->commune,
                    ])
                ]);
            // dd($response->status() . ' </br>' . $response->body());
            if ($response->status() == 200) {
                return back()->with('succes',  "Mise à jour effectuée");
            } else {
                return back()->withErrors(["Impossible de mettre à jour. Veuillez réessayer!!"]);
            }
        } else {
            $response = Http::withOptions([
                'verify' => false
            ])->asMultipart()
                ->withHeaders([
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                    'Accept' => 'application/json',
                ])->attach(
                    'facadeImage',
                    file_get_contents($request->file('photo')->getRealPath()),
                    $request->file('photo')->getClientOriginalName()
                )->post(env('API_BASE_URL') . '/pharma/update/' . $id, [
                    'pharmacyCmd' => json_encode([
                        'name' => $request->name,
                        'address' => $request->adresse,
                        'phoneNumber' => $request->phone ?? '',
                        'whatsAppPhoneNumber' => $request->whatsapp ?? '',
                        'ownerName' => $request->responsable,
                        'gpsCoordinates' => $request->longitude,
                        'startGardeDate' => 0,
                        'endGardeDate' => 0,
                        'communeId' => $request->commune,
                    ])
                ]);
            // dd($response->status() . ' </br>' . $response->body(). ' </br>' . $request->file('photo') . ' </br>' . $request->commune);
            if ($response->status() == 200) {
                return back()->with('succes',  "Mise à jour effectuée");
            } else {
                return back()->withErrors(["Impossible de mettre à jour. Veuillez réessayer!!"]);
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
        ])->delete(env('API_BASE_URL') . '/pharma/delete/' . $id);

        if ($response->status() == 200) {
            return back()->with('succes',  "Suppression éffectuée");
        } else {
            return back()->withErrors(["Impossible de supprimer. Veuillez réessayer!!"]);
        }
    }

    public function showAllGet(Request $request)
    {
        if (!session('api_token')) {
            return redirect()->intended('logout');
        }
        $data = $request->query('data');

        // Décodage des données JSON
        $pharmacys = json_decode(urldecode($data), true);

        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get(env('API_BASE_URL_PHARMA') . '/pharma/communes/search?page=0&size=1000');

        $communes = $response->json();

        return view('pharmacies.view-pharmacy', compact('pharmacys', 'communes'));
    }

    public function assoAssurance(Request $request, string $id)
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
        ])->post(env('API_BASE_URL') . '/pharma/associate/assurance', [
            'pharmacyId' => (int) $id,
            'assuranceIds' => array_map('intval', $request->assurances)
        ]);
        // dd($response->status() . ' </br>' . $response->body() . ' </br>' . json_encode(array_map('intval', $request->assurances)));
        if ($response->status() == 201) {
            return back()->with('succes',  "Les assurances ont été associés a la pharmacie ");
        } else {
            return back()->withErrors(["Impossible d'associer les assurances. Veuillez réessayer!!"]);
        }
    }

    public function assoPaiement(Request $request, string $id)
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
        ])->post(env('API_BASE_URL') . '/pharma/associate/payment-methods', [
            'pharmacyId' => (int) $id,
            'paymentMethodIds' => array_map('intval', $request->paiements)
        ]);
        // dd($response->status() . ' </br>' . $response->body() . ' ' . json_encode(array_map('intval', $request->paiements)));
        if ($response->status() == 201) {
            return back()->with('succes',  "Les mayens de paiement ont été associés a la pharmacie ");
        } else {
            return back()->withErrors(["Impossible d'associer les moyens de paiement. Veuillez réessayer!!"]);
        }
    }
}
