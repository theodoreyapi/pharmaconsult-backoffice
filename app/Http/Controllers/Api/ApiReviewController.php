<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiReviewController extends Controller
{
    /**
     * POST /api/notices
     * Body JSON : { "note": 4, "userName": "002250585831647", "details": "Très bien", "pharmacyId": 1 }
     */
    public function addReview(Request $request)
    {
        $rules = [
            'note'       => 'required|integer|min:1|max:5',
            'userName'   => 'required|string',
            'pharmacyId' => 'required|integer',
            'details'    => 'nullable|string',
        ];

        $messages = [
            'note.required' => "Veuillez sélectionner au moins 1 étoile",
            'userName.required' => "Vous n'ête pas connecter pour mener cette action",
            'pharmacyId.required' => "Pharmacie introuvable",
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => collect($validator->errors()->all()),
            ], 422);
        }

        // Vérifier que la pharmacie existe
        $pharmacy = Pharmacy::where('id_pharmacy', $request->pharmacyId)->first();
        if (!$pharmacy) {
            return response()->json(['message' => 'Pharmacie introuvable.'], 404);
        }

        // Vérifier si l'utilisateur a déjà noté cette pharmacie
        $existing = Review::where('username', $request->userName)
            ->where('pharmacy_id', $request->pharmacyId)
            ->first();

        if ($existing) {
            // Mettre à jour l'avis existant
            $existing->update([
                'evaluation'  => $request->note,
                'commentaire' => $request->details ?? '',
                'updated_at'  => now(),
            ]);
            return response()->json(['message' => 'Avis mis à jour avec succès.'], 200);
        }

        // Insérer un nouvel avis
        Review::insert([
            'evaluation'  => $request->note,
            'username'    => $request->userName,
            'commentaire' => $request->details ?? '',
            'pharmacy_id' => $request->pharmacyId,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json(['message' => 'Avis ajouté avec succès.'], 201);
    }

    /**
     * GET /api/notices/{pharmacyId}
     * Retourne la liste des avis + résumé des notes pour une pharmacie
     */
    public function getByPharmacy(int $pharmacyId)
    {
        // Vérifier que la pharmacie existe
        $pharmacy = Pharmacy::where('id_pharmacy', $pharmacyId)->first();
        if (!$pharmacy) {
            return response()->json(['message' => 'Pharmacie introuvable.'], 404);
        }

        $reviews = Review::leftJoin('users_pharma', 'review.username', '=', 'users_pharma.phone_number')
            ->where('review.pharmacy_id', $pharmacyId)
            ->orderBy('review.created_at', 'desc')
            ->select(
                'review.id_review as id',
                'review.evaluation as note',
                'users_pharma.first_name',
                'users_pharma.last_name',
                'users_pharma.profile_picture as userPicture',
                'review.created_at as dateNotice',
                'review.commentaire as details',
                'review.pharmacy_id as pharmacyId'
            )
            ->get();

        // Calcul du résumé des notes
        $counter      = $reviews->count();
        $average      = $counter > 0 ? round($reviews->avg('note'), 2) : 0.0;
        $counterFive  = $reviews->where('note', 5)->count();
        $counterFour  = $reviews->where('note', 4)->count();
        $counterThree = $reviews->where('note', 3)->count();
        $counterTwo   = $reviews->where('note', 2)->count();
        $counterOne   = $reviews->where('note', 1)->count();

        $noticesList = $reviews->map(fn($r) => [
            'id'          => $r->id,
            'note'        => $r->note,
            'userName'    => $r->first_name . ' ' . $r->last_name,
            'userPicture' => $r->userPicture ?? '',
            'dateNotice'  => $r->dateNotice,
            'details'     => $r->details,
            'pharmacyId'  => $r->pharmacyId,
        ])->values();

        return response()->json([
            'notices'       => $noticesList,
            'ratingSummary' => [
                'counter'           => $counter,
                'average'           => $average,
                'counterFiveStars'  => $counterFive,
                'counterFourStars'  => $counterFour,
                'counterThreeStars' => $counterThree,
                'counterTwoStars'   => $counterTwo,
                'counterOneStars'   => $counterOne,
            ],
        ], 200);
    }
}
