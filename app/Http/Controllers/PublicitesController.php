<?php

namespace App\Http\Controllers;

use App\Models\Publicite;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PublicitesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $publicites = Publicite::all();

        return view('publicities.publicites', compact('publicites'));
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
            'image' => 'required',
            'libelle' => 'required',
            'lien' => 'required',
            'price' => 'required',
            'debut' => 'required',
            'fin' => 'required',
        ];
        $customMessages = [
            'image.required' => "Veuillez selectionner la photo de la publicite.",
            'libelle.required' => "Veuillez saisir le nom de la publicite.",
            'lien.required' => "Veuillez saisir le lien de la publicite.",
            'price.required' => "Veuillez saisir le coût de la publicite.",
            'debut.required' => "Veuillez sélectionner la date de debut de la publicite.",
            'fin.required' => "Veuillez sélectionner la date de fin de la publicite.",
        ];

        $request->validate($roles, $customMessages);

        $timestamp = Carbon::now()->format('Ymd_His');

        $imagePath = null;

        if ($request->file('image')) {
            $file = $request->file('image');
            $name = 'publicite_' . $timestamp . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('publicites'), $name);

            $imagePath = url('pharma/public/publicites/' . $name);
        }

        $publicite = new Publicite();
        $publicite->end_date = $request->fin;
        $publicite->lien = $request->lien;
        $publicite->price = $request->price ?? 0;
        $publicite->start_date = $request->debut;
        $publicite->name = $request->libelle;
        $publicite->image = $imagePath;
        if ($publicite->save()) {
            return back()->with('succes',  "La publicité " . $request->libelle . " a été ajoutée avec succès.");
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
            'image' => '',
            'libelle' => 'required',
            'lien' => 'required',
            'price' => 'required',
            'debut' => 'required',
            'fin' => 'required',
            'statut' => 'required',
        ];
        $customMessages = [
            'libelle.required' => "Veuillez saisir le nom de la publicite.",
            'lien.required' => "Veuillez saisir le lien de la publicite.",
            'price.required' => "Veuillez saisir le coût de la publicite.",
            'debut.required' => "Veuillez sélectionner la date de debut de la publicite.",
            'fin.required' => "Veuillez sélectionner la date de fin de la publicite.",
            'statut.required' => "Veuillez sélectionner la date de fin de la publicite.",
        ];

        $request->validate($roles, $customMessages);

        $timestamp = Carbon::now()->format('Ymd_His');

        $publicite = Publicite::findOrFail($id);

        // Gestion image
        if ($request->file('image')) {

            // 🔥 Supprimer ancienne image
            if ($publicite->image) {

                // récupérer le chemin du fichier depuis l'URL
                $oldPath = public_path(parse_url($publicite->image, PHP_URL_PATH));

                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('image');
            $name = 'publicite_' . $timestamp . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('publicites'), $name);
            $diplomePath = url('pharma/public/publicites/' . $name);

            $publicite->image = $diplomePath;
        }

        // Update champs
        $publicite->end_date = $request->fin;
        $publicite->lien = $request->lien;
        $publicite->price = $request->price ?? 0;
        $publicite->start_date = $request->debut;
        $publicite->name = $request->libelle;

        if ($publicite->save()) {
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

        $publicite =  Publicite::findOrFail($id);

        // 🔥 Supprimer ancienne image
        if ($publicite->image) {

            // récupérer le chemin du fichier depuis l'URL
            $oldPath = public_path(parse_url($publicite->image, PHP_URL_PATH));

            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $publicite->delete();

        return back()->with('succes',  "Suppression éffectuée");
    }
}
