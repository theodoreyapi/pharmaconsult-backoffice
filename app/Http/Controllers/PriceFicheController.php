<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PriceFicheController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!session('api_token')) {
            return redirect()->intended('logout');
        }

        $page = $request->get('page', 0);
        $size = 20;

        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get(env('API_BASE_URL_PHARMA') . "/pharma/medicaments/search?page=$page&size=$size");

        if ($response->successful()) {
            $data = $response->json();

            $medicaments = $data['content'] ?? []; // ou selon la structure de ton JSON
            $totalItems = $data['totalElements'] ?? count($medicaments);
            $currentPage = $page;
            $lastPage = ceil($totalItems / $size);

            return view('pharmacies.medicament', compact('medicaments', 'currentPage', 'lastPage'));
        } else {
            return abort(500, 'Erreur lors du chargement des données.');
        }
    }

    public function search(Request $request)
    {
        if (!session('api_token')) {
            return redirect()->intended('logout');
        }

        $name = $request->get('name', '');
        $page = $request->get('page', 0);
        $size = $request->get('size', 20);

        if (strlen($name) < 3) {
            return response()->json(['html' => '']);
        }

        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get(env('API_BASE_URL_PHARMA') . "/pharma/medicaments/search?name=$name&page=$page&size=$size");

        if ($response->successful()) {
            $data = $response->json();
            $medicaments = $data['content'] ?? [];

            $html = view('pharmacies.partials.medicament-list', compact('medicaments'))->render();

            return response()->json(['html' => $html]);
        }

        return response()->json(['html' => '<p>Erreur lors de la recherche.</p>']);
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
            'name' => 'required',
            'code' => '',
            'price' => 'required',
            'principe' => 'required',
            'notice' => '',
        ];
        $customMessages = [
            'name.required' => "Veuillez saisir le nom du médicament.",
            'price.required' => "Veuillez saisir le prix du médicament.",
            'principe.required' => "Veuillez saisir principe du médicament.",
        ];

        $request->validate($roles, $customMessages);

        if ($request->file('photo') == null) {
            $response = Http::withOptions([
                'verify' => false
            ])->withHeaders([
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                'Accept' => 'application/json',
            ])->post(env('API_BASE_URL') . '/medicament/create', [
                'medicamentCmd' => json_encode([
                    "name" => $request->name,
                    "principeActif" => $request->principe,
                    "codeCip" => $request->code,
                    "substitutesIds" => [],
                    "price" => $request->price,
                    "notice" => $request->notice
                ])
            ]);

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
                    'medicamentPicture',
                    file_get_contents($request->file('photo')->getRealPath()),
                    $request->file('photo')->getClientOriginalName()
                )->post(env('API_BASE_URL') . '/medicament/create', [
                    'medicamentCmd' => json_encode([
                        "name" => $request->name,
                        "principeActif" => $request->principe,
                        "codeCip" => $request->code,
                        "substitutesIds" => [],
                        "price" => $request->price,
                        "notice" => $request->notice
                    ])
                ]);

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
            'name' => 'required',
            'price' => 'required',
            'principe' => 'required',
            'notice' => '',
        ];
        $customMessages = [
            'name.required' => "Veuillez saisir le nom du médicament.",
            'price.required' => "Veuillez saisir le prix du médicament.",
            'principe.required' => "Veuillez saisir principe du médicament.",
        ];

        $request->validate($roles, $customMessages);

        if ($request->file('photo') == null) {
            $response = Http::withOptions([
                'verify' => false
            ])->asMultipart()
                ->withHeaders([
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                    'Accept' => 'application/json',
                ])->put(env('API_BASE_URL') . '/medicament/update/' . $id, [
                    'medicamentCmd' => json_encode([
                        "name" => $request->name,
                        "principeActif" => $request->principe,
                        "substitutesIds" => [],
                        "price" => $request->price,
                        "notice" => $request->notice
                    ])
                ]);

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
                    'medicamentPicture',
                    file_get_contents($request->file('photo')->getRealPath()),
                    $request->file('photo')->getClientOriginalName()
                )->put(env('API_BASE_URL') . '/medicament/update/' . $id, [
                    'medicamentCmd' => json_encode([
                        "name" => $request->name,
                        "principeActif" => $request->principe,
                        "substitutesIds" => [],
                        "price" => $request->price,
                        "notice" => $request->notice
                    ])
                ]);
            //  dd($response->status());
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
        ])->delete(env('API_BASE_URL') . '/medicament/delete/' . $id);

        if ($response->status() == 200) {
            return back()->with('succes',  "Suppression éffectuée ");
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
        $medicaments = json_decode(urldecode($data), true);

        return view('pharmacies.view-medicament', compact('medicaments'));
    }
}
