<?php

namespace App\Http\Controllers;

use App\Models\MoyensPaiment;
use Carbon\Carbon;
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

        $timestamp = Carbon::now()->format('Ymd_His');

        $imagePath = null;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            $name = 'paiement_' . $timestamp . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('paiements'), $name);

            $imagePath = url('pharma/public/paiements/' . $name);
        }

        $assurance = new MoyensPaiment();
        $assurance->description = $request->description;
        $assurance->name = $request->libelle;
        $assurance->payment_method_picture = $imagePath;
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
            'description.required' => "Veuillez saisir la description du moyen de paiement.",
            'libelle.required' => "Veuillez saisir le libelle du moyen de paiement.",
        ];

        $request->validate($roles, $customMessages);

        $timestamp = Carbon::now()->format('Ymd_His');

        $assurance = MoyensPaiment::findOrFail($id);

        // Gestion image
        if ($request->file('photo')) {

            // 🔥 Supprimer ancienne image
            if ($assurance->payment_method_picture) {

                // récupérer le chemin du fichier depuis l'URL
                $oldPath = public_path(parse_url($assurance->payment_method_picture, PHP_URL_PATH));

                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('photo');
            $name = 'paiement_' . $timestamp . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('paiements'), $name);
            $diplomePath = url('pharma/public/paiements/' . $name);

            $assurance->payment_method_picture = $diplomePath;
        }

        // Update champs
        $assurance->description = $request->description;
        $assurance->name = $request->libelle;

        if ($assurance->save()) {
            return back()->with('succes',  "Mise à jour effectuée");
        } else {
            return back()->withErrors(["Impossible de mettre à jour. Veuillez réessayer!!"]);
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

        MoyensPaiment::findOrFail($id)->delete();

        return back()->with('succes',  "Suppression éffectuée ");
    }
}
