<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthProfile;
use App\Models\ProfileSubscription;
use App\Models\ProfileVaccination;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiHealthProfileController extends Controller
{
    /**
     * GET /api/health-profiles
     * Liste tous les profils actifs de l'utilisateur connecté.
     */
    public function index($id): JsonResponse
    {
        $profiles = HealthProfile::query()

            ->where('health_profiles.user_id', $id)

            /**
             * Nombre vaccinations
             */
            ->leftJoin(
                'profile_vaccinations',
                'health_profiles.id_profile',
                '=',
                'profile_vaccinations.profile_id'
            )

            ->selectRaw('
            health_profiles.id_profile,
            health_profiles.user_id,
            health_profiles.name,
            health_profiles.profile_type,
            health_profiles.relation,
            health_profiles.animal_type,
            health_profiles.gender,
            health_profiles.birth_date,
            health_profiles.is_frequent_traveler,
            health_profiles.is_active,
            health_profiles.created_at,

            COUNT(profile_vaccinations.id_vaccination)
            as vaccinations_count
        ')

            ->groupBy(
                'health_profiles.id_profile',
                'health_profiles.user_id',
                'health_profiles.name',
                'health_profiles.profile_type',
                'health_profiles.relation',
                'health_profiles.animal_type',
                'health_profiles.gender',
                'health_profiles.birth_date',
                'health_profiles.is_frequent_traveler',
                'health_profiles.is_active',
                'health_profiles.created_at'
            )

            ->orderByDesc('health_profiles.created_at')

            ->get();

        /**
         * Ajouter abonnement actif manuellement
         */
        $profiles->transform(function ($profile) {

            $activeSubscription = ProfileSubscription::query()
                ->where('profile_id', $profile->id_profile)
                ->where('status', 'paid')
                ->whereDate('end_date', '>=', now())
                ->orderByDesc('end_date')
                ->first();

            return [
                'id_profile' => $profile->id_profile,
                'user_id' => $profile->user_id,
                'name' => $profile->name,
                'profile_type' => $profile->profile_type ?? '',
                'relation' => $profile->relation,
                'animal_type' => $profile->animal_type ?? '',
                'gender' => $profile->gender,
                'birth_date' => $profile->birth_date,
                'is_frequent_traveler' => $profile->is_frequent_traveler,
                'is_active' => $profile->is_active,
                'created_at' => $profile->created_at,

                /**
                 * Vaccinations
                 */
                'vaccinations_count' => $profile->vaccinations_count,

                /**
                 * Abonnement
                 */
                'has_active_subscription' => $activeSubscription !== null,
                'active_subscription' => $activeSubscription,
            ];
        });

        return response()->json(
            $profiles,
        );
    }

    /**
     * GET /api/health-profiles/{id}
     * Détail d'un profil avec ses vaccinations.
     */
    public function show(int $id): JsonResponse
    {
        $profile = HealthProfile::query()
            ->where('is_active', true)
            ->where('id_profile', $id)
            ->first();

        if (!$profile) {

            return response()->json([
                'success' => false,
                'message' => 'Profil introuvable.',
            ], 404);
        }

        /**
         * Vaccinations du profil
         */
        $vaccinations = ProfileVaccination::query()
            ->leftJoin(
                'vaccines',
                'profile_vaccinations.vaccine_id',
                '=',
                'vaccines.id_vaccine'
            )

            ->where('profile_vaccinations.profile_id', $profile->id_profile)

            ->select(
                'profile_vaccinations.id_vaccination',
                'profile_vaccinations.profile_id',
                'profile_vaccinations.vaccine_id',
                'profile_vaccinations.vaccine_name_free',
                'profile_vaccinations.vaccination_date',
                'profile_vaccinations.next_reminder_date',
                'profile_vaccinations.center_type',
                'profile_vaccinations.center_name',
                'profile_vaccinations.certificate_image',
                'profile_vaccinations.notes',
                'profile_vaccinations.created_at',

                /**
                 * Infos vaccin catalogue
                 */
                'vaccines.id_vaccine',
                'vaccines.name as vaccine_name',
                'vaccines.description as vaccine_description'
            )

            ->orderByDesc('profile_vaccinations.vaccination_date')
            ->get();

        /**
         * Abonnement actif
         */
        $activeSubscription = ProfileSubscription::query()
            ->where('profile_id', $profile->id_profile)
            ->where('status', 'paid')
            ->latest('id_subscription')
            ->first();

        return response()->json([
            'success' => true,

            'data' => [

                /**
                 * Profil
                 */
                'id_profile' => $profile->id_profile,
                'user_id' => $profile->user_id,
                'name' => $profile->name,
                'profile_type' => $profile->profile_type,
                'relation' => $profile->relation,
                'animal_type' => $profile->animal_type,
                'gender' => $profile->gender,
                'birth_date' => $profile->birth_date,
                'is_frequent_traveler' => $profile->is_frequent_traveler,
                'is_active' => $profile->is_active,
                'created_at' => $profile->created_at,

                /**
                 * Vaccinations
                 */
                'vaccinations' => $vaccinations,

                /**
                 * Abonnement actif
                 */
                'active_subscription' => $activeSubscription,
            ],
        ]);
    }

    /**
     * POST /api/health-profiles
     * Créer un nouveau profil santé + abonnement pending.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'profile_type' => 'required|in:human,animal',
            'relation' => [
                'nullable',
                'string',
                'max:100',
                'required_if:profile_type,human',
            ],
            'animal_type' => [
                'nullable',
                'string',
                'max:100',
                'required_if:profile_type,animal',
            ],
            'gender' => 'nullable|in:masculin,feminin',
            'birth_date' => 'nullable|date|before:today',
            'is_frequent_traveler' => 'nullable|boolean',
        ]);

        /**
         * Vérifie si l'utilisateur a déjà un profil
         */
        $hasAlreadyProfile = HealthProfile::query()
            ->where('user_id', $request->id_user)
            ->exists();

        /**
         * Création du profil
         */
        $profile = HealthProfile::create([
            'user_id' => $request->id_user,
            'name' => $validated['name'],
            'profile_type' => $validated['profile_type'],
            'relation' => $validated['relation'] ?? null,
            'animal_type' => $validated['animal_type'] ?? null,
            'gender' => $validated['gender'] ?? null,

            'birth_date' => !empty($validated['birth_date'])
                ? Carbon::createFromFormat(
                    'd/m/Y',
                    $validated['birth_date']
                )->format('Y-m-d')
                : null,

            'is_frequent_traveler' => $validated['is_frequent_traveler'] ?? false,

            /**
             * Premier profil = actif directement
             * Autres profils = inactifs jusqu'au paiement
             */
            'is_active' => !$hasAlreadyProfile,
        ]);

        /**
         * Premier profil gratuit
         */
        if (!$hasAlreadyProfile) {

            ProfileSubscription::create([
                'profile_id' => $profile->id_profile,
                'user_id' => $request->id_user,
                'amount' => 0,
                'currency' => 'FCFA',
                'status' => 'paid',
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'payment_reference' => 'FREE-FIRST-PROFILE',
                'paid_at' => now(),
            ]);

            $message = 'Premier profil créé gratuitement et activé.';
        } else {

            try {

                /**
                 * Profils suivants payants
                 */
                $subscription = ProfileSubscription::create([
                    'profile_id' => $profile->id_profile,
                    'user_id' => $request->id_user,
                    'amount' => 1000,
                    'payment_method' => 'wave',
                    'currency' => 'FCFA',
                    'status' => 'pending',
                    'start_date' => now(),
                    'end_date' => now()->addYear(),
                ]);

                $payload = [
                    'amount' => (string) 1000,
                    'currency' => 'XOF',
                    'success_url' => 'https://admin.pharma-consults.com/payment/wave/success/profile' . $subscription->id_subscription,
                    'error_url'   => 'https://admin.pharma-consults.com/payment/wave/error/profile' . $subscription->id_subscription,
                    'client_reference' => (string) $request->id_user,
                ];

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer wave_ci_prod_tIc5B0OlAxjucp29W83a2YLvua7Z7FOTmAFYtQlONucpqcNHU0TklALECuBP-nf5HL8HkGgopw0UzPFz2aXld43qhMcAwXINng',
                    'Content-Type'  => 'application/json',
                ])->post('https://api.wave.com/v1/checkout/sessions', $payload);

                if (!$response->successful()) {
                    Log::error('Wave error', $response->json());

                    return response()->json([
                        'success' => false,
                        'message' => 'Erreur Wave',
                        'details' => $response->json(),
                    ], 500);
                }

                $data = $response->json();

                $subscription->update([
                    'checkout_session_id' => $data['id'],
                ]);

                return response()->json([
                    'success' => true,
                    'rechargement_url' => $data['wave_launch_url'],
                    'rechargement_id' => $subscription->id_subscription,
                    'message' => 'Profil créé. Paiement requis pour activation.',
                ]);
            } catch (\Throwable $e) {
                Log::error('Wave Exception', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur serveur',
                ], 500);
            }
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ], 201);
    }

    /**
     * PUT /api/health-profiles/{id}
     * Modifier un profil.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $profile = HealthProfile::query()
            ->where('user_id', $request->id_user)
            ->where('is_active', true)
            ->where('id_profile', $id)
            ->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil introuvable.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:150',
            'relation' => 'nullable|string|max:100',
            'animal_type' => 'nullable|string|max:100',
            'gender' => 'nullable|in:masculin,feminin',
            'birth_date' => 'nullable|date|before:today',
            'is_frequent_traveler' => 'nullable|boolean',
        ]);

        $profile->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour.',
        ]);
    }

    /**
     * DELETE /api/health-profiles/{id}
     * Désactiver un profil (soft delete logique).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $profile = HealthProfile::query()
            ->where('user_id', $request->id_user)
            ->where('is_active', true)
            ->where('id_profile', $id)
            ->first();

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil introuvable.',
            ], 404);
        }

        /**
         * Suppression logique
         */
        $profile->update([
            'is_active' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil supprimé.',
        ]);
    }

    /**
     * GET /api/health-profiles/reminders
     * Rappels de vaccins dans les 30 prochains jours pour tous les profils.
     */
    public function reminders(Request $request): JsonResponse
    {
        $profiles = HealthProfile::query()
            ->where('user_id', $request->id_user)
            ->where('is_active', true)
            ->with([
                'vaccinations' => function ($query) {

                    $query->whereNotNull('next_reminder_date')
                        ->whereDate('next_reminder_date', '>=', now())
                        ->whereDate(
                            'next_reminder_date',
                            '<=',
                            now()->addDays(30)
                        )
                        ->orderBy('next_reminder_date');
                },

                'vaccinations.vaccine',
            ])
            ->get();

        $result = [];

        foreach ($profiles as $profile) {

            if ($profile->vaccinations->isEmpty()) {
                continue;
            }

            $reminders = [];

            foreach ($profile->vaccinations as $vaccination) {

                $reminders[] = [
                    'id_vaccination' => $vaccination->id_vaccination,
                    'vaccine_name' => $vaccination->vaccine?->name ?? $vaccination->vaccine_name_free,
                    'vaccination_date' => $vaccination->vaccination_date,
                    'next_reminder_date' => $vaccination->next_reminder_date,
                    'days_until' => now()->diffInDays(
                        $vaccination->next_reminder_date,
                        false
                    ),
                    'center_name' => $vaccination->center_name,
                    'center_type' => $vaccination->center_type,
                ];
            }

            $result[] = [
                'id_profile' => $profile->id_profile,
                'profile_name' => $profile->name,
                'profile_type' => $profile->profile_type,
                'reminders' => $reminders,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}
