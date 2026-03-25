<?php

namespace App\Http\Controllers;

use App\Models\Assurances;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AssuranceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $assurances = Assurances::orderBy('name', 'ASC')->get();

        return view('pharmacies.assurance', compact('assurances'));
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
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }
        $roles = [
            'description' => 'required',
            'libelle' => 'required',
            'photo' => '',
        ];
        $customMessages = [
            'description.required' => "Veuillez saisir la description de l'assurance.",
            'libelle.required' => "Veuillez saisir le libelle de l'assurance.",
        ];

        $request->validate($roles, $customMessages);

        if ($request->file('photo') == null) {

            $response = Http::withOptions([
                'verify' => false
            ])->withHeaders([
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(env('API_BASE_URL') . '/assurances/add', [
                'assuranceCmd' => json_encode([
                    'name' => $request->libelle,
                    'description' => $request->description,
                ])
            ]);

            if ($response->status() == 201) {
                return back()->with('succes',  "Vous avez ajouter " . $request->libelle);
            } else {
                return back()->withErrors(["Impossible d'ajouter " . $request->libelle . ". Veuillez réessayer!!"]);
            }
        } else {

            $response = Http::withOptions([
                'verify' => false
            ])->asMultipart()
                ->withHeaders([
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                    'Accept' => 'application/json',
                ])->attach(
                    'assurancePicture',
                    file_get_contents($request->file('photo')->getRealPath()),
                    $request->file('photo')->getClientOriginalName()
                )->post(env('API_BASE_URL') . '/assurances/add', [
                    'assuranceCmd' => json_encode([
                        'name' => $request->libelle,
                        'description' => $request->description,
                    ])
                ]);

            if ($response->status() == 201) {
                return back()->with('succes',  "Vous avez ajouter " . $request->libelle);
            } else {
                return back()->withErrors(["Impossible d'ajouter " . $request->libelle . ". Veuillez réessayer!!"]);
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
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }
        $roles = [
            'description' => 'required',
            'libelle' => 'required',
            'photo' => '',
        ];
        $customMessages = [
            'description.required' => "Veuillez saisir la description de l'assurance.",
            'libelle.required' => "Veuillez saisir le libelle de l'assurance.",
        ];

        $request->validate($roles, $customMessages);

        if ($request->file('photo') == null) {

            $response = Http::withOptions([
                'verify' => false
            ])->asMultipart()
                ->withHeaders([
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->post(env('API_BASE_URL') . '/assurances/update/' . $id, [
                    'assuranceCmd' => json_encode([
                        'name' => $request->libelle,
                        'description' => $request->description,
                    ])
                ]);

            if ($response->status() == 200) {
                return back()->with('succes',  "Modification effectuée ");
            } else {
                return back()->withErrors(["Impossible de modifier. Veuillez réessayer!!"]);
            }
        } else {

            $response = Http::withOptions([
                'verify' => false
            ])->asMultipart()
                ->withHeaders([
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                    'Accept' => 'application/json',
                ])->attach(
                    'assurancePicture',
                    file_get_contents($request->file('photo')->getRealPath()),
                    $request->file('photo')->getClientOriginalName()
                )->put(env('API_BASE_URL') . '/assurances/update/' . $id, [
                    'assuranceCmd' => json_encode([
                        'name' => $request->libelle,
                        'description' => $request->description,
                    ])
                ]);

            if ($response->status() == 200) {
                return back()->with('succes',  "Modification effectuée ");
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
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }
        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->delete(env('API_BASE_URL') . '/assurances/delete/' . $id);

        if ($response->status() == 200) {
            return back()->with('succes',  "Suppression éffectuée ");
        } else {
            return back()->withErrors(["Impossible de supprimer. Veuillez réessayer!!"]);
        }
    }
}
