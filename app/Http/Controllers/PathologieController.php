<?php

namespace App\Http\Controllers;

use App\Models\Pathologies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PathologieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $pathologie = Pathologies::orderBy('created_at', 'desc')->get();

        return view('sante.pathologies', compact('pathologie'));
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
            'code' => 'required|string|max:100',
            'name' => 'required|string|max:150',
        ]);

        Pathologies::create([
            'code' => $request->code,
            'name' => $request->name,
        ]);

        return redirect()->back()->with('succes', 'Pathologie ajoutée avec succès.');
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
            'code' => 'required|string|max:100',
            'name' => 'required|string|max:150',
        ]);

        // Recherche par la clé primaire id_pathologie
        $pathologie = Pathologies::findOrFail($id);

        $pathologie->update([
            'code' => $request->code,
            'name' => $request->name,
        ]);

        return redirect()->back()->with('succes', 'Pathologie modifiée avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pathologie = Pathologies::findOrFail($id);
        $pathologie->delete();

        return redirect()->back()->with('succes', 'Pathologie supprimée avec succès.');
    }
}
