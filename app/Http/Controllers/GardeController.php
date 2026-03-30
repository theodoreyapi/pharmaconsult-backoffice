<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\PeriodeGarde;
use App\Models\Pharmacy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GardeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        // Vérifier qu'il existe une période de garde globale active maintenant
        $premier = PeriodeGarde::first();

        $gardes = Pharmacy::join('commune', 'pharmacy.commune_id', '=', 'commune.id_commune')
            ->where('pharmacy.start_garde_date', '=', $premier->date_debut)
            ->where('pharmacy.end_garde_date', '=', $premier->date_fin)
            ->select('commune.name as communeName', DB::raw('COUNT(pharmacy.id_pharmacy) as nombreDePharmacie'))
            ->groupBy('commune.id_commune', 'commune.name')
            ->get();

        return view('pharmacies.garde', compact('gardes', 'premier'));
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
            'commune' => 'required',
            'debut' => 'required|date',
            'fin' => 'required|date|after_or_equal:debut',
            'pharmacys' => 'required|array',
        ];

        $customMessages = [
            'commune.required' => "Veuillez sélectionner la commune.",
            'debut.required' => "Veuillez sélectionner la date début.",
            'fin.required' => "Veuillez sélectionner la date fin.",
            'fin.after_or_equal' => "La date de fin doit être après la date de début.",
            'pharmacys.required' => "Veuillez sélectionner au moins une pharmacie.",
        ];

        $request->validate($roles, $customMessages);

        Pharmacy::whereIn('id_pharmacy', $request->pharmacys)
            ->update([
                'start_garde_date' => $request->debut,
                'end_garde_date' => $request->fin,
            ]);

        return back()->with('succes', "Les pharmacies ont été ajoutées à la garde.");
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getCommune()
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $communes = Commune::orderBy('name', 'ASC')->get();

        return view('pharmacies.add-garde', compact('communes'));
    }

    public function storeGarde(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $roles = [
            'debut' => 'required|date',
            'fin' => 'required|date|after_or_equal:debut',
        ];

        $customMessages = [
            'debut.required' => "Veuillez sélectionner la date début.",
            'fin.required' => "Veuillez sélectionner la date fin.",
            'fin.after_or_equal' => "La date de fin doit être après la date de début.",
        ];

        $request->validate($roles, $customMessages);

        // 👉 Ici on suppose qu’il n’y a qu’une seule ligne (cas classique)
        $garde = PeriodeGarde::first();

        if ($garde) {
            // UPDATE
            PeriodeGarde::where('id_garde', $garde->id_garde)
                ->update([
                    'date_debut' => $request->debut,
                    'date_fin' => $request->fin,
                    'date_miseajour' => now(),
                    'updated_at' => now(),
                ]);
        } else {
            // INSERT
            PeriodeGarde::insert([
                'date_debut' => $request->debut,
                'date_fin' => $request->fin,
                'date_miseajour' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return back()->with('succes', "La période de garde a été mise à jour");
    }
}
