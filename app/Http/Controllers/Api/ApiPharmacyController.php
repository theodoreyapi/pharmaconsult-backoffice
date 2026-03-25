<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PeriodeGarde;
use App\Models\Pharmacy;
use App\Models\PharmacyAssurances;
use App\Models\PharmacyPaymentMethods;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiPharmacyController extends Controller
{

    /**
     * GET /api/pharmacies/{id}/pharmacies
     * Retourne les pharmacies de garde pour une assurance donnée
     */
    public function getById($id)
    {
        // Récupérer les pharmacies liées à l'assurance donnée
        $pharmacies = Pharmacy::join('commune', 'pharmacy.commune_id', '=', 'commune.id_commune')
            ->join('pharmacy_assurances', 'pharmacy.id_pharmacy', '=', 'pharmacy_assurances.pharmacy_id')
            ->where('pharmacy_assurances.assurance_id', $id)
            ->select('pharmacy.*', 'commune.id_commune', 'commune.name as commune_name', 'commune.description as commune_description')
            ->distinct()
            ->get();

        if ($pharmacies->isEmpty()) {
            return response()->json([], 200);
        }

        $result = $pharmacies->map(function ($pharmacy) {
            $reviews = Review::leftJoin('users_pharma', 'review.username', '=', 'users_pharma.username')
                ->where('review.pharmacy_id', $pharmacy->id_pharmacy)
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
                ->where('pharmacy_payment_methods.pharmacy_id', $pharmacy->id_pharmacy)
                ->select(
                    'moyens_paiement.id_moyen_payment as id',
                    'moyens_paiement.name',
                    'moyens_paiement.description',
                    'moyens_paiement.payment_method_picture as paymentMethodPicture'
                )
                ->get();

            $assurances = PharmacyAssurances::join('assurances', 'pharmacy_assurances.assurance_id', '=', 'assurances.id_assurance')
                ->where('pharmacy_assurances.pharmacy_id', $pharmacy->id_pharmacy)
                ->select(
                    'assurances.id_assurance as id',
                    'assurances.name',
                    'assurances.description',
                    'assurances.assurance_picture as assurancePicture'
                )
                ->get();

            return [
                'id'                  => $pharmacy->id_pharmacy,
                'name'                => $pharmacy->name,
                'address'             => $pharmacy->address,
                'openingHours'        => $pharmacy->opening_hours,
                'phoneNumber'         => $pharmacy->phone_number,
                'whatsAppPhoneNumber' => $pharmacy->whats_app_phone_number,
                'ownerName'           => $pharmacy->owner_name,
                'facadeImage'         => $pharmacy->facade_image ?? '',
                'gpsCoordinates'      => $pharmacy->gps_coordinates ?? '',
                'startGardeDate'      => $pharmacy->start_garde_date,
                'endGardeDate'        => $pharmacy->end_garde_date,
                'commune' => [
                    'id'          => $pharmacy->id_commune,
                    'name'        => $pharmacy->commune_name,
                    'description' => $pharmacy->commune_description,
                ],
                'notices' => [
                    'notices' => $reviews->map(fn($r) => [
                        'id'          => $r->id,
                        'note'        => $r->note,
                        'userName'    => $r->userName,
                        'userPicture' => $r->userPicture ?? '',
                        'dateNotice'  => $r->dateNotice,
                        'details'     => $r->details,
                        'pharmacyId'  => $r->pharmacyId ?? 0,
                    ])->values(),
                    'ratingSummary' => [
                        'counter'           => $counter,
                        'average'           => $average,
                        'counterFiveStars'  => $counterFive,
                        'counterFourStars'  => $counterFour,
                        'counterThreeStars' => $counterThree,
                        'counterTwoStars'   => $counterTwo,
                        'counterOneStars'   => $counterOne,
                    ],
                ],
                'paymentMethods' => $paymentMethods->map(fn($p) => [
                    'id'                   => $p->id,
                    'name'                 => $p->name,
                    'description'          => $p->description,
                    'paymentMethodPicture' => $p->paymentMethodPicture ?? '',
                ])->values(),
                'assurances' => $assurances->map(fn($a) => [
                    'id'               => $a->id,
                    'name'             => $a->name,
                    'description'      => $a->description,
                    'assurancePicture' => $a->assurancePicture ?? '',
                ])->values(),
            ];
        });

        return response()->json($result, 200);
    }

    /**
     * GET /api/periode-garde
     * Retourne la période de garde en cours
     */
    public function getPeriodeGarde()
    {
        $periode = PeriodeGarde::first();

        if (!$periode) {
            return response()->json([
                'active'  => false,
                'message' => 'Aucune période de garde en cours.',
                'data'    => null,
            ], 422);
        }

        return response()->json([
            $periode,
        ], 200);
    }


    /**
     * GET /api/pharmacies/gardeIntervalByCommune?communeId=1
     *
     * Retourne les pharmacies de garde pour une commune donnée,
     * dont la période de garde (start_garde_date / end_garde_date en ms)
     * englobe la date actuelle, croisée avec la table periode_garde.
     */
    public function getByCommune(Request $request)
    {
        $communeId = $request->query('communeId');

        if (!$communeId) {
            return response()->json(['message' => 'commune Id est requis.'], 400);
        }


        // Vérifier qu'il existe une période de garde globale active maintenant
        $periodeActive = PeriodeGarde::first();

        if (!$periodeActive) {
            return response()->json([], 200); // Pas de période de garde en cours
        }

        // Récupérer les pharmacies de garde de la commune
        // dont le créneau (start/end en ms) englobe maintenant
        $pharmacies = Pharmacy::join('commune', 'pharmacy.commune_id', '=', 'commune.id_commune')
            ->where('pharmacy.commune_id', $communeId)
            ->where('pharmacy.start_garde_date', '=', $periodeActive->date_debut)
            ->where('pharmacy.end_garde_date', '=', $periodeActive->date_fin)
            ->select('pharmacy.*', 'commune.id_commune', 'commune.name as commune_name', 'commune.description as commune_description')
            ->distinct()
            ->get();

        if ($pharmacies->isEmpty()) {
            return response()->json([], 200);
        }

        $result = $pharmacies->map(function ($pharmacy) {
            // Récupérer les avis (reviews)
            $reviews = Review::join('users_pharma', 'review.username', '=', 'users_pharma.username')
                ->where('review.pharmacy_id', $pharmacy->id_pharmacy)
                ->select(
                    'review.id_review as id',
                    'review.evaluation as note',
                    'users_pharma.profile_picture as userPicture',
                    'users_pharma.first_name',
                    'users_pharma.last_name',
                    'review.created_at as dateNotice',
                    'review.commentaire as details',
                    'review.pharmacy_id as pharmacyId'
                )
                ->get();

            // Calcul du résumé des notes
            $counter         = $reviews->count();
            $average         = $counter > 0 ? round($reviews->avg('note'), 2) : 0;
            $counterFive     = $reviews->where('note', 5)->count();
            $counterFour     = $reviews->where('note', 4)->count();
            $counterThree    = $reviews->where('note', 3)->count();
            $counterTwo      = $reviews->where('note', 2)->count();
            $counterOne      = $reviews->where('note', 1)->count();

            // Récupérer les moyens de paiement
            $paymentMethods = PharmacyPaymentMethods::join('moyens_paiement', 'pharmacy_payment_methods.payment_method_id', '=', 'moyens_paiement.id_moyen_payment')
                ->where('pharmacy_payment_methods.pharmacy_id', $pharmacy->id_pharmacy)
                ->select(
                    'moyens_paiement.id_moyen_payment as id',
                    'moyens_paiement.name',
                    'moyens_paiement.description',
                    'moyens_paiement.payment_method_picture as paymentMethodPicture'
                )
                ->get();

            // Récupérer les assurances
            $assurances = PharmacyAssurances::join('assurances', 'pharmacy_assurances.assurance_id', '=', 'assurances.id_assurance')
                ->where('pharmacy_assurances.pharmacy_id', $pharmacy->id_pharmacy)
                ->select(
                    'assurances.id_assurance as id',
                    'assurances.name',
                    'assurances.description',
                    'assurances.assurance_picture as assurancePicture'
                )
                ->get();

            return [
                'id'                  => $pharmacy->id_pharmacy,
                'name'                => $pharmacy->name,
                'address'             => $pharmacy->address,
                'openingHours'        => $pharmacy->opening_hours,
                'phoneNumber'         => $pharmacy->phone_number,
                'whatsAppPhoneNumber' => $pharmacy->whats_app_phone_number,
                'ownerName'           => $pharmacy->owner_name,
                'facadeImage'         => $pharmacy->facade_image ?? '',
                'gpsCoordinates'      => $pharmacy->gps_coordinates ?? '',
                'startGardeDate'      => $pharmacy->start_garde_date,
                'endGardeDate'        => $pharmacy->end_garde_date,
                'commune'             => [
                    'id'          => $pharmacy->id_commune,
                    'name'        => $pharmacy->commune_name,
                    'description' => $pharmacy->commune_description,
                ],
                'notices' => [
                    'notices' => $reviews->map(function ($r) {
                        return [
                            'id'         => $r->id,
                            'note'       => $r->note,
                            'userName'   => $r->first_name . ' ' . $r->last_name,
                            'userPicture' => $r->userPicture ?? '',
                            'dateNotice' => $r->dateNotice,
                            'details'    => $r->details,
                            'pharmacyId' => $r->pharmacyId ?? 0,
                        ];
                    })->values(),
                    'ratingSummary' => [
                        'counter'          => $counter,
                        'average'          => $average,
                        'counterFiveStars' => $counterFive,
                        'counterFourStars' => $counterFour,
                        'counterThreeStars' => $counterThree,
                        'counterTwoStars'  => $counterTwo,
                        'counterOneStars'  => $counterOne,
                    ],
                ],
                'paymentMethods' => $paymentMethods->map(fn($p) => [
                    'id'                   => $p->id,
                    'name'                 => $p->name,
                    'description'          => $p->description,
                    'paymentMethodPicture' => $p->paymentMethodPicture ?? '',
                ])->values(),
                'assurances' => $assurances->map(fn($a) => [
                    'id'               => $a->id,
                    'name'             => $a->name,
                    'description'      => $a->description,
                    'assurancePicture' => $a->assurancePicture ?? '',
                ])->values(),
            ];
        });

        return response()->json($result, 200);
    }
}
