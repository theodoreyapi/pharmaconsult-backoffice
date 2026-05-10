<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthProfile;
use App\Models\ProfileSubscription;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiProfileSubscriptionController extends Controller
{
    /**
     * GET /api/health-profiles/{profileId}/subscription
     * Abonnement courant d'un profil.
     */
    public function show(Request $request, int $profileId): JsonResponse
    {
        $profile = HealthProfile::where('id_profile', $profileId)
            ->where('user_id', $request->user()->id_user)
            ->where('is_active', true)
            ->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil non trouvé.',
            ], 404);
        }

        $subscription = ProfileSubscription::where('profile_id', $profileId)
            ->orderByDesc('id_subscription')
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun abonnement trouvé.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Abonnement récupéré avec succès.',
            'data' => [
                'id_subscription'   => $subscription->id_subscription,
                'profile_id'        => $subscription->profile_id,
                'amount'            => $subscription->amount,
                'currency'          => $subscription->currency,
                'status'            => $subscription->status,
                'start_date'        => $subscription->start_date,
                'end_date'          => $subscription->end_date,
                'payment_reference' => $subscription->payment_reference,
                'paid_at'           => $subscription->paid_at,
                'created_at'        => $subscription->created_at,

                'profile' => [
                    'id_profile'   => $profile->id_profile,
                    'name'         => $profile->name,
                    'profile_type' => $profile->profile_type,
                    'relation'     => $profile->relation,
                    'animal_type'  => $profile->animal_type,
                ],
            ],
        ]);
    }

    /**
     * POST /api/health-profiles/{profileId}/subscription/pay
     * Confirmer le paiement d'un abonnement (après validation mobile money).
     */
    public function pay(Request $request, int $profileId): JsonResponse
    {
        $profile = HealthProfile::where('id_profile', $profileId)
            ->where('user_id', $request->user()->id_user)
            ->where('is_active', true)
            ->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil non trouvé.',
            ], 404);
        }

        $validated = $request->validate([
            'payment_reference' => 'required|string|max:100',
        ]);

        /**
         * Vérifier si le profil est gratuit
         */
        $firstSubscription = ProfileSubscription::where('profile_id', $profileId)
            ->where('amount', 0)
            ->first();

        if ($firstSubscription) {
            return response()->json([
                'success' => false,
                'message' => 'Ce profil bénéficie déjà de la gratuité.',
            ], 422);
        }

        /**
         * Chercher abonnement pending
         */
        $subscription = ProfileSubscription::where('profile_id', $profileId)
            ->where('status', 'pending')
            ->latest('id_subscription')
            ->first();

        /**
         * Si aucun pending -> créer renouvellement
         */
        if (!$subscription) {

            $lastSubscription = ProfileSubscription::where('profile_id', $profileId)
                ->where('status', 'paid')
                ->latest('id_subscription')
                ->first();

            if (
                $lastSubscription &&
                Carbon::parse($lastSubscription->end_date)->isFuture()
            ) {

                $startDate = Carbon::parse($lastSubscription->end_date)
                    ->addDay();
            } else {

                $startDate = now();
            }

            $subscription = ProfileSubscription::create([
                'profile_id'        => $profileId,
                'user_id'           => $request->user()->id_user,
                'amount'            => 2000,
                'currency'          => 'FCFA',
                'status'            => 'pending',
                'start_date'        => $startDate,
                'end_date'          => $startDate->copy()->addYear(),
                'payment_reference' => null,
                'paid_at'           => null,
            ]);
        }

        /**
         * Validation paiement
         */
        $subscription->update([
            'status'            => 'paid',
            'payment_reference' => $validated['payment_reference'],
            'paid_at'           => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Paiement confirmé avec succès.',
            'data' => [
                'id_subscription'   => $subscription->id_subscription,
                'profile_id'        => $subscription->profile_id,
                'amount'            => $subscription->amount,
                'currency'          => $subscription->currency,
                'status'            => $subscription->status,
                'start_date'        => $subscription->start_date,
                'end_date'          => $subscription->end_date,
                'payment_reference' => $subscription->payment_reference,
                'paid_at'           => $subscription->paid_at,

                'profile' => [
                    'id_profile'   => $profile->id_profile,
                    'name'         => $profile->name,
                    'profile_type' => $profile->profile_type,
                ],
            ],
        ]);
    }

    /**
     * GET /api/subscriptions/summary
     * Résumé de tous les abonnements de l'utilisateur.
     */
    public function summary(Request $request): JsonResponse
    {
        $subscriptions = ProfileSubscription::where('user_id', $request->user()->id_user)
            ->orderByDesc('id_subscription')
            ->get();

        $totalPaid = ProfileSubscription::where('user_id', $request->user()->id_user)
            ->where('status', 'paid')
            ->count();

        $totalPending = ProfileSubscription::where('user_id', $request->user()->id_user)
            ->where('status', 'pending')
            ->count();

        $totalExpired = ProfileSubscription::where('user_id', $request->user()->id_user)
            ->where('status', 'expired')
            ->count();

        /**
         * Expire bientôt
         */
        $expiringSoon = [];

        foreach ($subscriptions as $subscription) {

            if (
                $subscription->status === 'paid' &&
                Carbon::parse($subscription->end_date)->isFuture() &&
                Carbon::today()->diffInDays(
                    Carbon::parse($subscription->end_date)
                ) <= 30
            ) {

                $profile = HealthProfile::where(
                    'id_profile',
                    $subscription->profile_id
                )->first();

                $expiringSoon[] = [
                    'id_subscription' => $subscription->id_subscription,
                    'profile_name'    => $profile?->name,
                    'amount'          => $subscription->amount,
                    'end_date'        => $subscription->end_date,
                    'days_left'       => Carbon::today()->diffInDays(
                        Carbon::parse($subscription->end_date)
                    ),
                ];
            }
        }

        /**
         * Liste complète
         */
        $allSubscriptions = [];

        foreach ($subscriptions as $subscription) {

            $profile = HealthProfile::where(
                'id_profile',
                $subscription->profile_id
            )->first();

            $allSubscriptions[] = [
                'id_subscription'   => $subscription->id_subscription,
                'profile_id'        => $subscription->profile_id,
                'profile_name'      => $profile?->name,
                'profile_type'      => $profile?->profile_type,
                'amount'            => $subscription->amount,
                'currency'          => $subscription->currency,
                'status'            => $subscription->status,
                'start_date'        => $subscription->start_date,
                'end_date'          => $subscription->end_date,
                'payment_reference' => $subscription->payment_reference,
                'paid_at'           => $subscription->paid_at,
                'created_at'        => $subscription->created_at,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Résumé des abonnements récupéré.',

            'summary' => [
                'total_paid'    => $totalPaid,
                'total_pending' => $totalPending,
                'total_expired' => $totalExpired,
                'total'         => $subscriptions->count(),
            ],

            'expiring_soon' => $expiringSoon,

            'subscriptions' => $allSubscriptions,
        ]);
    }
}
