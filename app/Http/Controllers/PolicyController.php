<?php

namespace App\Http\Controllers;

use App\Models\ParametresGeneraux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PolicyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $abouts = ParametresGeneraux::where('type', '=', 'POLITIQUE CONFIDENTIALITES')->first();

        return view('termes.policy.list-policy', compact('abouts'));
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

        $request->validate([
            'contenu' => 'required'
        ], [
            'contenu.required' => "Veuillez saisir au moins un mot."
        ]);

        $data = [
            'contenu' => $request->contenu,
            'type' => 'POLITIQUE CONFIDENTIALITES',
            'date_create' => now(),
        ];

        // 🔥 UPDATE si existe, sinon INSERT
        ParametresGeneraux::updateOrCreate(
            ['libelle' => 'POLITIQUE CONFIDENTIALITES'], // condition
            $data                      // valeurs
        );

        return back()->with('succes', "Le contenu POLITIQUE CONFIDENTIALITES a été enregistré avec succès.");
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

        $request->validate([
            'contenu' => 'required'
        ], [
            'contenu.required' => "Veuillez saisir au moins un mot."
        ]);

        $data = [
            'contenu' => $request->contenu,
            'type' => 'POLITIQUE CONFIDENTIALITES',
        ];

        // 🔥 UPDATE si existe, sinon INSERT
        ParametresGeneraux::updateOrCreate(
            ['libelle' => 'POLITIQUE CONFIDENTIALITES'], // condition
            $data                      // valeurs
        );

        return back()->with('succes', "Le contenu POLITIQUE CONFIDENTIALITES a été mis à jour avec succès.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
