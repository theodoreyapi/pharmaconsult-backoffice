<?php

namespace App\Http\Controllers;

use App\Models\Commune;
use App\Models\Pharmacy;
use App\Models\PharmacyAssurances;
use App\Models\PharmacyPaymentMethods;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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
        $roles = [
            'photo' => '',
            'name' => 'required',
            'adresse' => 'required',
            'responsable' => 'required',
            'commune' => 'required',
            'phone' => '',
            'whatsapp' => '',
            'longitude' => '',
        ];
        $customMessages = [
            'name.required' => "Veuillez saisir le nom d ela pharmacie.",
            'responsable.required' => "Veuillez saisir le nom du pharmacie.",
            'commune.required' => "Veuillez sélectionner la commune.",
        ];

        $request->validate($roles, $customMessages);

        if ($request->file('photo') == null) {
            //dd($request->all());
            $response = Http::withOptions([
                'verify' => false
            ])->withHeaders([
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(env('API_BASE_URL') . '/pharma/add', [
                'pharmacyCmd' => json_encode([
                    'name' => $request->name,
                    'address' => $request->adresse,
                    'phoneNumber' => $request->phone ?? '',
                    'whatsAppPhoneNumber' => $request->whatsapp ?? '',
                    'ownerName' => $request->responsable,
                    'gpsCoordinates' => $request->longitude,
                    'startGardeDate' => 0,
                    'endGardeDate' => 0,
                    'communeId' => $request->commune,
                ])
            ]);
            // dd($response->status() . ' </br>' . $response->body());
            if ($response->status() == 201) {
                return back()->with('succes',  "Vous avez ajouter " . $request->name);
            } else {
                return back()->withErrors(["Impossible d'ajouter " . $request->name . ". Veuillez réessayer!!"]);
            }
        } else {
            $response = Http::withOptions([
                'verify' => false
            ])->asMultipart()
                ->withHeaders([
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                    'Accept' => 'application/json',
                ])->attach(
                    'facadeImage',
                    file_get_contents($request->file('photo')->getRealPath()),
                    $request->file('photo')->getClientOriginalName()
                )->post(env('API_BASE_URL') . '/pharma/add', [
                    'pharmacyCmd' => json_encode([
                        'name' => $request->name,
                        'address' => $request->adresse,
                        'phoneNumber' => $request->phone ?? '',
                        'whatsAppPhoneNumber' => $request->whatsapp ?? '',
                        'ownerName' => $request->responsable,
                        'gpsCoordinates' => $request->longitude,
                        'startGardeDate' => 0,
                        'endGardeDate' => 0,
                        'communeId' => $request->commune,
                    ])
                ]);
            //dd($response->status() . ' </br>' . $response->body(). ' </br>' . $response->json(). ' </br>' . $request->file('photo') . ' </br>' . $request->commune);
            if ($response->status() == 201) {
                return back()->with('succes',  "Vous avez ajouter " . $request->name);
            } else {
                return back()->withErrors(["Impossible d'ajouter " . $request->name . ". Veuillez réessayer!!"]);
            }
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
        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get(env('API_BASE_URL_PHARMA') . '/pharma/assurances/getAll');

        if ($response->status() == 200) {
            $assurances = $response->json();

            return view('pharmacies.asso-assurance', compact('assurances', 'id'));
        } else {
            return back()->withErrors(["Impossible de charger les assurances. Veuillez réessayer!!"]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }
        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get(env('API_BASE_URL_PHARMA') . '/pharma/payment-methods/all');

        if ($response->status() == 200) {
            $paiements = $response->json();

            return view('pharmacies.asso-paiement', compact('paiements', 'id'));
        } else {
            return back()->withErrors(["Impossible de charger les moyens de paiement. Veuillez réessayer!!"]);
        }
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
            'photo' => '',
            'name' => 'required',
            'adresse' => 'required',
            'responsable' => 'required',
            'commune' => 'required',
            'phone' => '',
            'whatsapp' => '',
            'longitude' => '',
        ];
        $customMessages = [
            'name.required' => "Veuillez saisir le nom d ela pharmacie.",
            'responsable.required' => "Veuillez saisir le nom du pharmacie.",
            'commune.required' => "Veuillez sélectionner la commune.",
        ];

        $request->validate($roles, $customMessages);

        if ($request->file('photo') == null) {
            //dd($request->all());
            $response = Http::withOptions([
                'verify' => false
            ])->asMultipart()
                ->withHeaders([
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                    'Accept' => 'application/json',
                ])->post(env('API_BASE_URL') . '/pharma/update/' . $id, [
                    'pharmacyCmd' => json_encode([
                        'name' => $request->name,
                        'address' => $request->adresse,
                        'phoneNumber' => $request->phone ?? '',
                        'whatsAppPhoneNumber' => $request->whatsapp ?? '',
                        'ownerName' => $request->responsable,
                        'gpsCoordinates' => $request->longitude,
                        'startGardeDate' => 0,
                        'endGardeDate' => 0,
                        'communeId' => $request->commune,
                    ])
                ]);
            // dd($response->status() . ' </br>' . $response->body());
            if ($response->status() == 200) {
                return back()->with('succes',  "Mise à jour effectuée");
            } else {
                return back()->withErrors(["Impossible de mettre à jour. Veuillez réessayer!!"]);
            }
        } else {
            $response = Http::withOptions([
                'verify' => false
            ])->asMultipart()
                ->withHeaders([
                    'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
                    'Accept' => 'application/json',
                ])->attach(
                    'facadeImage',
                    file_get_contents($request->file('photo')->getRealPath()),
                    $request->file('photo')->getClientOriginalName()
                )->post(env('API_BASE_URL') . '/pharma/update/' . $id, [
                    'pharmacyCmd' => json_encode([
                        'name' => $request->name,
                        'address' => $request->adresse,
                        'phoneNumber' => $request->phone ?? '',
                        'whatsAppPhoneNumber' => $request->whatsapp ?? '',
                        'ownerName' => $request->responsable,
                        'gpsCoordinates' => $request->longitude,
                        'startGardeDate' => 0,
                        'endGardeDate' => 0,
                        'communeId' => $request->commune,
                    ])
                ]);
            // dd($response->status() . ' </br>' . $response->body(). ' </br>' . $request->file('photo') . ' </br>' . $request->commune);
            if ($response->status() == 200) {
                return back()->with('succes',  "Mise à jour effectuée");
            } else {
                return back()->withErrors(["Impossible de mettre à jour. Veuillez réessayer!!"]);
            }
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
        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->delete(env('API_BASE_URL') . '/pharma/delete/' . $id);

        if ($response->status() == 200) {
            return back()->with('succes',  "Suppression éffectuée");
        } else {
            return back()->withErrors(["Impossible de supprimer. Veuillez réessayer!!"]);
        }
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

    public function assoAssurance(Request $request, string $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }
        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post(env('API_BASE_URL') . '/pharma/associate/assurance', [
            'pharmacyId' => (int) $id,
            'assuranceIds' => array_map('intval', $request->assurances)
        ]);
        // dd($response->status() . ' </br>' . $response->body() . ' </br>' . json_encode(array_map('intval', $request->assurances)));
        if ($response->status() == 201) {
            return back()->with('succes',  "Les assurances ont été associés a la pharmacie ");
        } else {
            return back()->withErrors(["Impossible d'associer les assurances. Veuillez réessayer!!"]);
        }
    }

    public function assoPaiement(Request $request, string $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }
        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post(env('API_BASE_URL') . '/pharma/associate/payment-methods', [
            'pharmacyId' => (int) $id,
            'paymentMethodIds' => array_map('intval', $request->paiements)
        ]);
        // dd($response->status() . ' </br>' . $response->body() . ' ' . json_encode(array_map('intval', $request->paiements)));
        if ($response->status() == 201) {
            return back()->with('succes',  "Les mayens de paiement ont été associés a la pharmacie ");
        } else {
            return back()->withErrors(["Impossible d'associer les moyens de paiement. Veuillez réessayer!!"]);
        }
    }
}
