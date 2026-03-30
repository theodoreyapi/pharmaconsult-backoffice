<?php

namespace App\Http\Controllers;

use App\Models\Assurances;
use App\Models\Commune;
use App\Models\MoyensPaiment;
use App\Models\Pharmacy;
use App\Models\PharmacyAssurances;
use App\Models\PharmacyPaymentMethods;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PharmacieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $pharmacys = Pharmacy::join('commune', 'pharmacy.commune_id', '=', 'commune.id_commune')
            ->select('pharmacy.*', 'commune.id_commune', 'commune.name as commune')
            ->distinct()
            ->get();

        return view('pharmacies.pharmacy', compact('pharmacys'));
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

        $timestamp = Carbon::now()->format('Ymd_His');

        $roles = [
            'name' => 'required',
            'adresse' => 'required',
            'responsable' => 'required',
            'commune' => 'required',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

        $messages = [
            'name.required' => "Le nom est obligatoire.",
            'adresse.required' => "L'adresse est obligatoire.",
            'responsable.required' => "Le responsable est obligatoire.",
            'commune.required' => "La commune est obligatoire.",
        ];

        $request->validate($roles, $messages);

        $imagePath = null;

        if ($request->file('photo')) {
            $file = $request->file('photo');
            $name = 'pharmacy_' . $timestamp . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('pharmacys'), $name);

            $imagePath = url('pharma/public/pharmacys/' . $name);
        }

        $pharmacy = new Pharmacy();
        $pharmacy->name = $request->name;
        $pharmacy->address = $request->adresse;
        $pharmacy->owner_name = $request->responsable;
        $pharmacy->commune_id = $request->commune;
        $pharmacy->phone_number = $request->phone;
        $pharmacy->whats_app_phone_number = $request->whatsapp;
        $pharmacy->gps_coordinates = $request->longitude;
        $pharmacy->facade_image = $imagePath;
        if ($pharmacy->save()) {
            return back()->with('succes',  "Pharmacie ajoutée avec succès. ");
        } else {
            return back()->withErrors(["Impossible d'ajouter. Veuillez réessayer!!"]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $assurances = Assurances::orderBy('name', 'ASC')->get();

        $selectedAssurances = PharmacyAssurances::where('pharmacy_id', $id)
            ->pluck('assurance_id')
            ->toArray();

        return view('pharmacies.asso-assurance', compact('assurances', 'id', 'selectedAssurances'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $paiements = MoyensPaiment::all();

        $selectedPaiement = PharmacyPaymentMethods::where('pharmacy_id', $id)
            ->pluck('payment_method_id')
            ->toArray();

        return view('pharmacies.asso-paiement', compact('paiements', 'id', 'selectedPaiement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $timestamp = Carbon::now()->format('Ymd_His');

        $pharmacy = Pharmacy::findOrFail($id);

        $roles = [
            'name' => 'required',
            'adresse' => 'required',
            'responsable' => 'required',
            'commune' => 'required',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

        $request->validate($roles);

        // Gestion image
        if ($request->file('photo')) {

            // 🔥 Supprimer ancienne image
            if ($pharmacy->facade_image) {

                // récupérer le chemin du fichier depuis l'URL
                $oldPath = public_path(parse_url($pharmacy->facade_image, PHP_URL_PATH));

                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('photo');
            $name = 'pharmacy_' . $timestamp . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('pharmacys'), $name);
            $diplomePath = url('pharma/public/pharmacys/' . $name);

            $pharmacy->facade_image = $diplomePath;
        }

        // Update champs
        $pharmacy->name = $request->name;
        $pharmacy->address = $request->adresse;
        $pharmacy->owner_name = $request->responsable;
        $pharmacy->commune_id = $request->commune;
        $pharmacy->phone_number = $request->phone;
        $pharmacy->whats_app_phone_number = $request->whatsapp;
        $pharmacy->gps_coordinates = $request->longitude;

        $pharmacy->save();

        return back()->with('succes', "Pharmacie modifiée avec succès.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        Pharmacy::findOrFail($id)->delete();

        return back()->with('succes',  "Suppression éffectuée");
    }

    public function showAllGet($id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        // Récupérer les pharmacies liées à l'assurance donnée
        $pharmacys = Pharmacy::join('commune', 'pharmacy.commune_id', '=', 'commune.id_commune')
            ->where('pharmacy.id_pharmacy', $id)
            ->select('pharmacy.*', 'commune.id_commune', 'commune.name as commune_name')
            ->first();

        $reviews = Review::leftJoin('users_pharma', 'review.username', '=', 'users_pharma.username')
            ->where('review.pharmacy_id', $pharmacys->id_pharmacy)
            ->select(
                'review.id_review as id',
                'review.evaluation as note',
                'review.username as userName',
                'users_pharma.profile_picture as userPicture',
                'review.created_at as dateNotice',
                'review.commentaire as details',
                'review.pharmacy_id as pharmacyId'
            )
            ->get();

        $counter      = $reviews->count();
        $average      = $counter > 0 ? round($reviews->avg('note'), 2) : 0;
        $counterFive  = $reviews->where('note', 5)->count();
        $counterFour  = $reviews->where('note', 4)->count();
        $counterThree = $reviews->where('note', 3)->count();
        $counterTwo   = $reviews->where('note', 2)->count();
        $counterOne   = $reviews->where('note', 1)->count();

        $paymentMethods = PharmacyPaymentMethods::join('moyens_paiement', 'pharmacy_payment_methods.payment_method_id', '=', 'moyens_paiement.id_moyen_payment')
            ->where('pharmacy_payment_methods.pharmacy_id', $pharmacys->id_pharmacy)
            ->select(
                'moyens_paiement.id_moyen_payment as id',
                'moyens_paiement.name',
                'moyens_paiement.payment_method_picture as paymentMethodPicture'
            )
            ->get();

        $assurances = PharmacyAssurances::join('assurances', 'pharmacy_assurances.assurance_id', '=', 'assurances.id_assurance')
            ->where('pharmacy_assurances.pharmacy_id', $pharmacys->id_pharmacy)
            ->select(
                'assurances.id_assurance as id',
                'assurances.name',
                'assurances.assurance_picture as assurancePicture'
            )
            ->get();

        $communes = Commune::orderBy('name', 'ASC')->get();

        return view('pharmacies.view-pharmacy', compact(
            'pharmacys',
            'communes',
            'reviews',
            'counter',
            'average',
            'counterFive',
            'counterFour',
            'counterThree',
            'counterTwo',
            'counterOne',
            'paymentMethods',
            'assurances'
        ));
    }

    public function assoAssurance(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $request->validate([
            'assurances' => 'required|array',
        ], [
            'assurances.required' => "Veuillez sélectionner au moins une assurance.",
        ]);

        // 🔥 supprimer anciennes associations
        PharmacyAssurances::where('pharmacy_id', $id)->delete();

        // 🔥 insérer nouvelles
        foreach ($request->assurances as $assuranceId) {
            PharmacyAssurances::create([
                'pharmacy_id' => $id,
                'assurance_id' => $assuranceId,
            ]);
        }

        return back()->with('succes', "Les assurances ont été associées à la pharmacie");
    }

    public function assoPaiement(Request $request, string $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $request->validate([
            'paiements' => 'required|array',
        ], [
            'paiements.required' => "Veuillez sélectionner au moins une assurance.",
        ]);

        // 🔥 supprimer anciennes associations
        PharmacyPaymentMethods::where('pharmacy_id', $id)->delete();

        // 🔥 insérer nouvelles
        foreach ($request->paiements as $paiementsId) {
            PharmacyPaymentMethods::create([
                'pharmacy_id' => $id,
                'payment_method_id' => $paiementsId,
            ]);
        }

        return back()->with('succes',  "Les mayens de paiement ont été associés a la pharmacie ");
    }
}
