<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CommuneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $communes = Commune::orderBy('name', 'ASC')->get();

        return view('pharmacies.commune', compact('communes'));
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
        ];
        $customMessages = [
            'description.required' => "Veuillez saisir la description de la commune.",
            'libelle.required' => "Veuillez saisir le libelle de la commune.",
        ];

        $request->validate($roles, $customMessages);

        $assurance = new Commune();
        $assurance->description = $request->description;
        $assurance->name = $request->libelle;
        if ($assurance->save()) {
            return back()->with('succes',  "Vous avez ajouter " . $request->libelle);
        } else {
            return back()->withErrors(["Impossible d'ajouter " . $request->libelle . ". Veuillez réessayer!!"]);
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
        ];
        $customMessages = [
            'description.required' => "Veuillez saisir la description de la commune.",
            'libelle.required' => "Veuillez saisir le libelle de la commune.",
        ];

        $request->validate($roles, $customMessages);

        $assurance = Commune::findOrFail($id);

        // Update champs
        $assurance->description = $request->description;
        $assurance->name = $request->libelle;

        if ($assurance->save()) {
            return back()->with('succes',  "Modification éffectuée ");
        } else {
            return back()->withErrors(["Impossible de modifier. Veuillez réessayer!!"]);
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

        Commune::findOrFail($id)->delete();

        return back()->with('succes',  "Suppression éffectuée ");
    }
}
