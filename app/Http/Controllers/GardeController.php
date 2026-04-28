<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\PeriodeGarde;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;

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

    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function storeGarde(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        // ✅ VALIDATION
        $request->validate([
            'debut' => 'required|date',
            'fin' => 'required|date|after_or_equal:debut',
        ], [
            'debut.required' => "Veuillez sélectionner la date début.",
            'fin.required' => "Veuillez sélectionner la date fin.",
            'fin.after_or_equal' => "La date de fin doit être après la date de début.",
        ]);

        // ✅ INSERT / UPDATE
        $garde = PeriodeGarde::first();

        if ($garde) {
            $garde->update([
                'date_debut' => $request->debut,
                'date_fin' => $request->fin,
                'date_miseajour' => now(),
            ]);
        } else {
            PeriodeGarde::create([
                'date_debut' => $request->debut,
                'date_fin' => $request->fin,
                'date_miseajour' => now(),
            ]);
        }

        // ===============================
        // 🔔 ENVOI NOTIFICATION FCM
        // ===============================

        // ✅ 1. Récupérer tokens
        $tokens = DB::table('fcm_token')
            ->whereNotNull('token')
            ->pluck('token')
            ->toArray();

        Log::info("FCM Tokens count: " . count($tokens));

        if (empty($tokens)) {
            Log::warning("Aucun token FCM trouvé !");
            return back()->with('succes', "Période mise à jour (aucun utilisateur à notifier)");
        }

        $title = "Pharmacie de garde";
        $body = "La liste des pharmacies de garde vient d'être mise à jour : du {$request->debut} au {$request->fin}";

        // ✅ 2. Message FCM (format SAFE Android/iOS)
        $message = CloudMessage::new()
            ->withNotification([
                'title' => $title,
                'body' => $body,
            ])
            ->withData([
                'type' => 'garde_update',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ]);

        try {
            // ✅ 3. Envoi multicast
            $response = $this->messaging->sendMulticast($message, $tokens);

            Log::info("FCM Success: " . $response->successes()->count());
            Log::info("FCM Failures: " . $response->failures()->count());

            // // ✅ 4. Nettoyage tokens invalides
            // foreach ($response->failures()->getItems() as $failure) {
            //     $invalidToken = $failure->target()->value();

            //     DB::table('fcm_token')
            //         ->where('token', $invalidToken)
            //         ->delete();

            //     Log::warning("Token supprimé: " . $invalidToken);
            // }

        } catch (\Throwable $e) {
            Log::error("Erreur FCM: " . $e->getMessage());
        }

        return back()->with('succes', "La période de garde a été mise à jour");
    }
}
