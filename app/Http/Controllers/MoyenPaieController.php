<?php

namespace App\Http\Controllers;

use App\Models\MoyensPaiment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MoyenPaieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

            $paiements = MoyensPaiment::all();

            return view('pharmacies.paiement', compact('paiements'));
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
            'description.required' => "Veuillez saisir la description du moyen de paiement.",
            'libelle.required' => "Veuillez saisir le libelle du moyen de paiement.",
        ];

        $request->validate($roles, $customMessages);

        if ($request->file('photo') == null) {
            $response = Http::withOptions([
                'verify' => false
            ])->withHeaders([
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(env('API_BASE_URL') . '/payment-methods/add', [
                'paymentMethodCmd' => json_encode([
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
                    'paymentMethodPicture',
                    file_get_contents($request->file('photo')->getRealPath()),
                    $request->file('photo')->getClientOriginalName()
                )->post(env('API_BASE_URL') . '/payment-methods/add', [
                    'paymentMethodCmd' => json_encode([
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
            'description.required' => "Veuillez saisir la description du moyen de paiement.",
            'libelle.required' => "Veuillez saisir le libelle du moyen de paiement.",
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
                ])->post(env('API_BASE_URL') . '/payment-methods/update/' . $id, [
                    'paymentMethodCmd' => json_encode([
                        'name' => $request->libelle,
                        'description' => $request->description,
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
                    'paymentMethodPicture',
                    file_get_contents($request->file('photo')->getRealPath()),
                    $request->file('photo')->getClientOriginalName()
                )->post(env('API_BASE_URL') . '/payment-methods/update/' . $id, [
                    'paymentMethodCmd' => json_encode([
                        'name' => $request->libelle,
                        'description' => $request->description,
                    ])
                ]);

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
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }
        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->delete(env('API_BASE_URL') . '/payment-methods/delete/' . $id);

        if ($response->status() == 200) {
            return back()->with('succes',  "Suppression éffectuée ");
        } else {
            return back()->withErrors(["Impossible de supprimer. Veuillez réessayer!!"]);
        }
    }
}
