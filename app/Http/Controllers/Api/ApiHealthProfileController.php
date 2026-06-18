<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthProfile;
use App\Models\ProfileSubscription;
use App\Models\ProfileVaccination;
use App\Models\VaccineSchedule;
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

            'birth_date' => $validated['birth_date'] ?? null,

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
     * Modifie un profil santé
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $profile = HealthProfile::find($id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil introuvable.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'profile_type' => 'sometimes|required|in:human,animal',
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
            'is_pregnant' => 'nullable|boolean',
            'is_traveler' => 'nullable|boolean',
            'travel_destination' => 'nullable|string|max:150',
            'is_health_worker' => 'nullable|boolean',
            'is_immunocompromised' => 'nullable|boolean',
        ]);

        $profile->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès.',
            'data' => $profile,
        ], 200);
    }

    /**
     * DELETE /api/health-profiles/{id}
     * Supprime un profil santé
     */
    public function destroy(string $id): JsonResponse
    {
        $profile = HealthProfile::find($id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil introuvable.',
            ], 404);
        }

        $profile->delete();

        return response()->json([
            'success' => true,
            'message' => 'Profil supprimé avec succès.',
        ], 200);
    }

    /**
     * GET /api/health-profiles/reminders/{id}
     * Liste les rappels de vaccination d'un profil avec statut (à venir / en retard)
     */
    public function reminders(string $id): JsonResponse
    {
        $profile = HealthProfile::find($id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil introuvable.',
            ], 404);
        }

        $vaccinations = ProfileVaccination::query()
            ->where('profile_id', $id)
            ->whereNotNull('next_reminder_date')
            ->with('vaccine')
            ->orderBy('next_reminder_date', 'asc')
            ->get();

        $today = now()->startOfDay();

        $reminders = $vaccinations->map(function ($v) use ($today) {
            $reminderDate = \Carbon\Carbon::parse($v->next_reminder_date)->startOfDay();
            $status = $reminderDate->lt($today) ? 'en_retard' : 'a_venir';

            return [
                'id_vaccination' => $v->id_vaccination,
                'vaccine_id' => $v->vaccine_id,
                'vaccine_name' => $v->vaccine->name ?? $v->vaccine_name_free,
                'vaccination_date' => $v->vaccination_date,
                'next_reminder_date' => $v->next_reminder_date,
                'status' => $status,
                'days_diff' => $today->diffInDays($reminderDate, false),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $reminders,
        ], 200);
    }


    /**
     * GET /api/health-profiles/calendar/{id}
     * Retourne le calendrier vaccinal applicable à un profil
     * (filtré selon âge, genre, contextes, type humain/animal)
     * et regroupé par tranche d'âge dynamique.
     */
    public function calendar(string $id): JsonResponse
    {
        $profile = HealthProfile::find($id);

        if (!$profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profil introuvable.',
            ], 404);
        }

        // ── Âge réel ──────────────────────────────────────────────────
        $ageInMonths = null;
        $realAgeString = "Âge inconnu";

        if ($profile->birth_date) {
            $birthDate = Carbon::parse($profile->birth_date);
            $ageInMonths = $birthDate->diffInMonths(now());
            $years  = $birthDate->diffInYears(now());
            $months = $birthDate->diffInMonths(now()) % 12;
            $realAgeString = $years > 0
                ? "{$years} ans {$months} mois"
                : "{$months} mois";
        }

        // ── Requête principale ─────────────────────────────────────────
        $query = VaccineSchedule::query()
            ->with(['vaccine.categories', 'vaccine.restrictions', 'vaccine.equivalents'])
            ->whereHas('vaccine', function ($q) use ($profile) {
                $q->where('is_active', true)
                    ->where('vaccine_type', $profile->profile_type);

                if ($profile->profile_type === 'animal' && $profile->animal_type) {
                    $q->where(function ($sub) use ($profile) {
                        $sub->whereNull('target_species')
                            ->orWhere('target_species', 'like', '%' . $profile->animal_type . '%');
                    });
                }
            });

        // ── Filtre genre ───────────────────────────────────────────────
        if ($profile->gender) {
            $query->where(function ($q) use ($profile) {
                $q->where('gender', 'all')
                    ->orWhere('gender', $profile->gender);
            });
        }

        // ── Filtres contextuels ────────────────────────────────────────
        // Principe : exclure les vaccins RÉSERVÉS à un contexte
        // que le profil n'a pas (only_pregnant, for_travelers, etc.)
        // Les vaccins généraux (tous ces flags à false) passent toujours.
        if (!$profile->is_pregnant) {
            $query->where('only_pregnant', false);
        }

        if (!($profile->is_traveler || $profile->is_frequent_traveler)) {
            $query->where('for_travelers', false);
        }

        if (!$profile->is_health_worker) {
            $query->where('for_health_workers', false);
        }

        if (!$profile->is_immunocompromised) {
            $query->where('for_immunocompromised', false);
        }

        $schedules = $query
            ->orderBy('min_age_months', 'asc')
            ->orderBy('priority', 'desc')
            ->get();

        // ── Regroupement par tranche d'âge ─────────────────────────────
        $grouped = $schedules->groupBy(function ($schedule) {
            return $this->resolveAgeGroupLabel(
                (float) $schedule->min_age_months,
                $schedule->max_age_months ? (float) $schedule->max_age_months : null,
                (bool) $schedule->is_booster
            );
        });

        $result = $grouped->map(function ($items, $label) use ($ageInMonths) {
            return [
                'age_group' => $label,
                'count'     => $items->count(),
                'vaccines'  => $items->map(function ($schedule) use ($ageInMonths) {
                    $vaccine = $schedule->vaccine;

                    if ($ageInMonths === null) {
                        $status = 'applicable';
                    } elseif (
                        $schedule->min_age_months <= $ageInMonths &&
                        ($schedule->max_age_months === null || $schedule->max_age_months >= $ageInMonths)
                    ) {
                        $status = 'a_faire';
                    } elseif ($schedule->min_age_months > $ageInMonths) {
                        $status = 'a_venir';
                    } else {
                        $status = 'en_retard';
                    }

                    return [
                        'id_vaccine'         => $vaccine->id_vaccine,
                        'name'               => $vaccine->name,
                        'short_name'         => $vaccine->short_name,
                        'public_price'       => (float) $vaccine->public_price,
                        'currency'           => $vaccine->currency,
                        'targeted_disease'   => $vaccine->targeted_disease,
                        'description'        => $vaccine->description,
                        'protected_against'  => $vaccine->protected_against,
                        'administration_mode' => $vaccine->administration_mode,
                        'important_info'     => $vaccine->important_info,
                        'status'             => $status,
                        'categories'         => $vaccine->categories->pluck('name'),
                        'restrictions'       => $vaccine->restrictions->pluck('restriction_type'),
                        'equivalents'        => $vaccine->equivalents->map(fn($eq) => [
                            'id_equivalent' => $eq->id_equivalent,
                            'name'          => $eq->name,
                            'description'   => $eq->description,
                            'price'         => (float) $eq->price,
                        ])->values(),
                        'schedule' => [
                            'phase_name'           => $schedule->phase_name,
                            'dose_number'          => $schedule->dose_number,
                            'age_label'            => $schedule->age_label,
                            'min_age_months'       => (float) $schedule->min_age_months,
                            'max_age_months'       => $schedule->max_age_months ? (float) $schedule->max_age_months : null,
                            'is_booster'           => (bool) $schedule->is_booster,
                            'booster_every_months' => $schedule->booster_every_months,
                            'important_note'       => $schedule->important_note,
                        ],
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'profile' => [
                'id_profile'   => $profile->id_profile,
                'name'         => $profile->name,
                'profile_type' => $profile->profile_type,
                'age_in_months' => $ageInMonths ? round($ageInMonths, 1) : null,
                'real_age'     => $realAgeString,
            ],
            'data' => $result,
        ], 200);
    }

    /**
     * Détermine le libellé de tranche d'âge à afficher,
     * basé sur min/max_age_months du schedule.
     */
    private function resolveAgeGroupLabel(?float $min, ?float $max, bool $isBooster): string
    {
        // Rappel périodique sans tranche d'âge précise (ex: tétanos tous les 10 ans)
        if ($isBooster && $min >= 216) { // 18 ans = 216 mois
            return "Tous les 10 ans";
        }

        if ($min >= 780) { // 65 ans = 780 mois
            return "65 ans et plus";
        }

        if ($min < 12) {
            return "0 - 11 mois";
        }

        if ($min < 24) {
            return "1 - 2 ans";
        }

        if ($min < 72) {
            return "2 - 6 ans";
        }

        if ($min < 144) {
            return "6 - 12 ans";
        }

        if ($min < 216) {
            return "12 - 18 ans";
        }

        return "Adulte (18 ans et plus)";
    }


    /**
     * GET /api/health-profiles/reminders-category/{user_id}?category=all|children|adults|animals
     * Retourne tous les rappels (profile_vaccinations à venir) pour tous les profils
     * d'un utilisateur, filtrés par catégorie, avec statut Urgent/Proche/Futur.
     */
    public function remindersByCategory(Request $request, string $userId): JsonResponse
    {
        // Seuil enfant/adulte en mois — à ajuster si besoin
        $childAdultThresholdMonths = 216; // 18 ans

        $category = $request->query('category', 'all'); // all | children | adults | animals

        $profilesQuery = HealthProfile::query()->where('user_id', $userId);

        if ($category === 'animals') {
            $profilesQuery->where('profile_type', 'animal');
        } elseif ($category === 'children') {
            $profilesQuery->where('profile_type', 'human')
                ->whereNotNull('birth_date')
                ->whereRaw('TIMESTAMPDIFF(MONTH, birth_date, NOW()) < ?', [$childAdultThresholdMonths]);
        } elseif ($category === 'adults') {
            $profilesQuery->where('profile_type', 'human')
                ->where(function ($q) use ($childAdultThresholdMonths) {
                    $q->whereNull('birth_date')
                        ->orWhereRaw('TIMESTAMPDIFF(MONTH, birth_date, NOW()) >= ?', [$childAdultThresholdMonths]);
                });
        }
        // 'all' → aucun filtre supplémentaire

        $profileIds = $profilesQuery->pluck('id_profile');

        if ($profileIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'counts' => ['urgent' => 0, 'proche' => 0, 'futur' => 0],
                'next_dose' => null,
                'data' => [],
            ], 200);
        }

        $today = now()->startOfDay();

        $vaccinations = ProfileVaccination::query()
            ->whereIn('profile_id', $profileIds)
            ->whereNotNull('next_reminder_date')
            ->whereDate('next_reminder_date', '>=', $today)
            ->with(['vaccine', 'profile'])
            ->orderBy('next_reminder_date', 'asc')
            ->get();

        $reminders = $vaccinations->map(function ($v) use ($today) {
            $reminderDate = \Carbon\Carbon::parse($v->next_reminder_date)->startOfDay();
            $daysLeft = $today->diffInDays($reminderDate, false);

            if ($daysLeft <= 5) {
                $urgency = 'urgent';
            } elseif ($daysLeft <= 30) {
                $urgency = 'proche';
            } else {
                $urgency = 'futur';
            }

            return [
                'id_vaccination' => $v->id_vaccination,
                'vaccine_name' => $v->vaccine->name ?? $v->vaccine_name_free,
                'profile' => [
                    'id_profile' => $v->profile->id_profile,
                    'name' => $v->profile->name,
                    'profile_type' => $v->profile->profile_type,
                    'relation' => $v->profile->relation,
                    'animal_type' => $v->profile->animal_type,
                ],
                'next_reminder_date' => $v->next_reminder_date,
                'next_reminder_date_label' => $reminderDate->translatedFormat('l d F Y'),
                'days_left' => $daysLeft,
                'urgency' => $urgency,
                'center_name' => $v->center_name,
                'center_type' => $v->center_type,
            ];
        });

        $counts = [
            'urgent' => $reminders->where('urgency', 'urgent')->count(),
            'proche' => $reminders->where('urgency', 'proche')->count(),
            'futur' => $reminders->where('urgency', 'futur')->count(),
        ];

        $nextDose = $reminders->first(); // déjà trié par date croissante

        return response()->json([
            'success' => true,
            'category' => $category,
            'counts' => $counts,
            'next_dose' => $nextDose,
            'data' => $reminders->values(),
        ], 200);
    }

    /**
     * POST /api/health-profiles/reminders/store
     * Enregistre un rappel de vaccination dans profile_vaccinations
     */
    public function storeReminder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'profile_id'          => 'required|exists:health_profiles,id_profile',
            'next_reminder_date'  => 'required|date|after:today',

            // Vaccin catalogue OU vaccin libre (au moins l'un des deux)
            'vaccine_id'          => 'nullable|exists:vaccines,id_vaccine',
            'vaccine_name_free'   => 'nullable|string|max:150',

            // Infos optionnelles
            'vaccination_date'    => 'nullable|date|before_or_equal:today',
            'center_type'         => 'nullable|in:public,private',
            'center_name'         => 'nullable|string|max:200',
            'notes'               => 'nullable|string',
        ]);

        // Au moins vaccine_id ou vaccine_name_free doit être fourni
        if (empty($validated['vaccine_id']) && empty($validated['vaccine_name_free'])) {
            return response()->json([
                'success' => false,
                'message' => 'Veuillez fournir un vaccin du catalogue (vaccine_id) ou un nom libre (vaccine_name_free).',
            ], 422);
        }

        // Vérifier que le profil appartient bien à l'utilisateur connecté
        $profile = HealthProfile::find($validated['profile_id']);
        if ((string) $profile->user_id !== (string) $request->id_user) {
            return response()->json([
                'success' => false,
                'message' => 'Ce profil ne vous appartient pas.',
            ], 403);
        }

        // Vérifier qu'un rappel n'existe pas déjà pour ce vaccin + profil
        $alreadyExists = ProfileVaccination::query()
            ->where('profile_id', $validated['profile_id'])
            ->where(function ($q) use ($validated) {
                if (!empty($validated['vaccine_id'])) {
                    $q->where('vaccine_id', $validated['vaccine_id']);
                } else {
                    $q->where('vaccine_name_free', $validated['vaccine_name_free']);
                }
            })
            ->whereNotNull('next_reminder_date')
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'success' => false,
                'message' => 'Un rappel existe déjà pour ce vaccin sur ce profil.',
            ], 409);
        }

        $vaccination = ProfileVaccination::create([
            'profile_id'         => $validated['profile_id'],
            'vaccine_id'         => $validated['vaccine_id'] ?? null,
            'vaccine_name_free'  => $validated['vaccine_name_free'] ?? null,
            'vaccination_date'   => $validated['vaccination_date'] ?? now()->toDateString(),
            'next_reminder_date' => $validated['next_reminder_date'],
            'center_type'        => $validated['center_type'] ?? 'public',
            'center_name'        => $validated['center_name'] ?? null,
            'notes'              => $validated['notes'] ?? null,
        ]);

        // Charger le vaccin associé pour la réponse
        $vaccination->load('vaccine');

        return response()->json([
            'success' => true,
            'message' => 'Rappel enregistré avec succès.',
            'data' => [
                'id_vaccination'     => $vaccination->id_vaccination,
                'profile_id'         => $vaccination->profile_id,
                'vaccine_id'         => $vaccination->vaccine_id,
                'vaccine_name'       => $vaccination->vaccine->name ?? $vaccination->vaccine_name_free,
                'vaccination_date'   => $vaccination->vaccination_date,
                'next_reminder_date' => $vaccination->next_reminder_date,
                'center_type'        => $vaccination->center_type,
                'center_name'        => $vaccination->center_name,
                'notes'              => $vaccination->notes,
            ],
        ], 201);
    }
}
