<?php

namespace App\Http\Controllers;

use App\Models\Assurances;
use Carbon\Carbon;
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

        $timestamp = Carbon::now()->format('Ymd_His');

        $imagePath = null;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            $name = 'assurance_' . $timestamp . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assurances'), $name);

            $imagePath = url('pharma/public/assurances/' . $name);
        }

        $assurance = new Assurances();
        $assurance->description = $request->description;
        $assurance->name = $request->libelle;
        $assurance->assurance_picture = $imagePath;
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
            'photo' => '',
        ];
        $customMessages = [
            'description.required' => "Veuillez saisir la description de l'assurance.",
            'libelle.required' => "Veuillez saisir le libelle de l'assurance.",
        ];

        $request->validate($roles, $customMessages);

        $timestamp = Carbon::now()->format('Ymd_His');

        $assurance = Assurances::findOrFail($id);

        // Gestion image
        if ($request->file('photo')) {

            // 🔥 Supprimer ancienne image
            if ($assurance->assurance_picture) {

                // récupérer le chemin du fichier depuis l'URL
                $oldPath = public_path(parse_url($assurance->assurance_picture, PHP_URL_PATH));

                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('photo');
            $name = 'assurance_' . $timestamp . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assurances'), $name);
            $diplomePath = url('pharma/public/assurances/' . $name);

            $assurance->assurance_picture = $diplomePath;
        }

        // Update champs
        $assurance->description = $request->description;
        $assurance->name = $request->libelle;

        if ($assurance->save()) {
            return back()->with('succes',  "Modification effectuée ");
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

        Assurances::findOrFail($id)->delete();

        return back()->with('succes',  "Suppression éffectuée ");
    }
}
