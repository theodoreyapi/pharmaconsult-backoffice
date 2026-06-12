<?php

namespace App\Http\Controllers;

use App\Models\Conseils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConseilController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        // Récupérer tous les conseils par ordre de création récent
        $conseil = Conseils::orderBy('created_at', 'desc')->get();

        return view('sante.conseils', compact('conseil'));
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
        $request->validate([
            'type' => 'required|string|max:100',
            'categorie' => 'required|string|max:150',
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Conseils::create([
            'type' => $request->type,
            'categorie' => $request->categorie,
            'titre' => $request->titre,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('succes', 'Conseil ajouté avec succès.');
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
        $request->validate([
            'type' => 'required|string|max:100',
            'categorie' => 'required|string|max:150',
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Recherche par la clé primaire id_conseil
        $conseil = Conseils::findOrFail($id);

        $conseil->update([
            'type' => $request->type,
            'categorie' => $request->categorie,
            'titre' => $request->titre,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('succes', 'Conseil modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $conseil = Conseils::findOrFail($id);
        $conseil->delete();

        return redirect()->back()->with('succes', 'Conseil supprimé avec succès.');
    }
}
