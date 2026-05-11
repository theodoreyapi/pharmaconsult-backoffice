<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HealthProfile;
use App\Models\ProfileVaccination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ApiProfileVaccinationController extends Controller
{
    /**
     * GET /api/health-profiles/{profileId}/vaccinations
     * Liste toutes les vaccinations d'un profil.
     */
    public function index(Request $request, int $profileId): JsonResponse
    {
        /**
         * Vérifier que le profil appartient
         * bien à l'utilisateur connecté
         */
        $profile = HealthProfile::where([
            'id_profile' => $profileId,
            'user_id' => $request->id_user,
            'is_active' => true,
        ])
            ->first();

        if (!$profile) {

            return response()->json([
                'success' => false,
                'message' => 'Profil introuvable.',
            ], 404);
        }

        /**
         * Récupérer les vaccinations
         */
        $vaccinations = ProfileVaccination::query()
            ->select(
                'profile_vaccinations.*',
                'vaccines.name as vaccine_name',
                'vaccines.description as vaccine_description',
                'vaccines.short_name',
                'vaccines.public_price',
                'vaccines.vaccine_type',
                'vaccines.important_info'
            )
            ->leftJoin('vaccines', 'vaccines.id_vaccine', '=', 'profile_vaccinations.vaccine_id')
            ->where('profile_vaccinations.profile_id', $profileId)
            ->orderByDesc('profile_vaccinations.vaccination_date')
            ->get();

        return response()->json([
            'success' => true,

            'profile' => [
                'id_profile' => $profile->id_profile,
                'name' => $profile->name,
                'profile_type' => $profile->profile_type,
            ],

            'count' => $vaccinations->count(),

            'data' => $vaccinations,
        ], 200);
    }

    /**
     * GET /api/health-profiles/{profileId}/vaccinations/{id}
     * Détail d'une vaccination.
     */
    public function show(Request $request, int $profileId, int $id): JsonResponse
    {

        /**
         * Vérifier le profil
         */
        $profile = HealthProfile::where([
            'id_profile' => $profileId,
            'user_id' => $request->id_user,
            'is_active' => true,
        ])
            ->first();

        if (!$profile) {

            return response()->json([
                'success' => false,
                'message' => 'Profil introuvable.',
            ], 404);
        }

        /**
         * Récupérer la vaccination
         */
        $vaccination = ProfileVaccination::where([
            'id_vaccination' => $id,
            'profile_id' => $profileId,
        ])

            ->with([

                'vaccine' => function ($query) {

                    $query->select(
                        'id_vaccine',
                        'name',
                        'description',
                        'recommended_age',
                        'recall_period'
                    );
                }
            ])

            ->first();

        if (!$vaccination) {

            return response()->json([
                'success' => false,
                'message' => 'Vaccination introuvable.',
            ], 404);
        }

        return response()->json([
            'success' => true,

            'profile' => [
                'id_profile' => $profile->id_profile,
                'name' => $profile->name,
            ],

            'data' => $vaccination,
        ], 200);
    }

    /**
     * POST /api/health-profiles/{profileId}/vaccinations
     * Ajouter une vaccination à un profil.
     *
     * Accepte multipart/form-data pour l'upload du certificat.
     */
    public function store(Request $request, int $profileId): JsonResponse
    {

        /**
         * Vérifier le profil
         */
        $profile = HealthProfile::where([
            'id_profile' => $profileId,
            'user_id' => $request->id_user,
            'is_active' => true,
        ])
            ->first();

        if (!$profile) {

            return response()->json([
                'success' => false,
                'message' => 'Profil introuvable.',
            ], 404);
        }

        /**
         * Validation API propre
         */
        $validator = Validator::make($request->all(), [
            'vaccine_id' => 'nullable|exists:vaccines,id_vaccine',
            'vaccine_name_free' => 'nullable|string|max:150',
            'vaccination_date' => 'required|date|before_or_equal:today',
            'next_reminder_date' => 'nullable|date|after:vaccination_date',
            'center_type' => 'required|in:public,private',
            'center_name' => 'nullable|string|max:200',
            'certificate_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        /**
         * Vérifier qu'au moins un vaccin est renseigné
         */
        if (
            empty($validated['vaccine_id']) &&
            empty($validated['vaccine_name_free'])
        ) {

            return response()->json([
                'success' => false,
                'message' =>
                'Veuillez renseigner un vaccin.',
            ], 422);
        }

        /**
         * Upload image certificat
         */
        $imagePath = null;

        if ($request->hasFile('certificate_image')) {

            $imagePath = $request->file('certificate_image')
                ->store(
                    'certificates/' . date('Y'),
                    'public'
                );
        }

        /**
         * Création vaccination
         */
        $vaccination = ProfileVaccination::create([
            'profile_id' => $profileId,
            'vaccine_id' => $validated['vaccine_id'] ?? null,
            'vaccine_name_free' => $validated['vaccine_name_free'] ?? null,
            'vaccination_date' => $validated['vaccination_date'],
            'next_reminder_date' => $validated['next_reminder_date'] ?? null,
            'center_type' => $validated['center_type'],
            'center_name' => $validated['center_name'] ?? null,
            'certificate_image' => $imagePath,
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vaccination ajoutée avec succès.',
        ], 201);
    }

    /**
     * POST /api/health-profiles/{profileId}/vaccinations/{id}
     * Modifier une vaccination (avec remplacement d'image possible).
     * Utilise POST + _method=PUT pour compatibilité multipart.
     */
    public function update(Request $request, int $profileId, int $id): JsonResponse
    {

        /**
         * Vérifier profil
         */
        $profile = HealthProfile::where([
            'id_profile' => $profileId,
            'user_id' => $request->id_user,
            'is_active' => true,
        ])
            ->first();

        if (!$profile) {

            return response()->json([
                'success' => false,
                'message' => 'Profil introuvable.',
            ], 404);
        }

        /**
         * Vérifier vaccination
         */
        $vaccination = ProfileVaccination::where([
            'id_vaccination' => $id,
            'profile_id' => $profileId,
        ])
            ->first();

        if (!$vaccination) {

            return response()->json([
                'success' => false,
                'message' => 'Vaccination introuvable.',
            ], 404);
        }

        /**
         * Validation
         */
        $validated = $request->validate([
            'vaccine_id' => 'nullable|exists:vaccines,id_vaccine',
            'vaccine_name_free' => 'nullable|string|max:150',
            'vaccination_date' => 'nullable|date|before_or_equal:today',
            'next_reminder_date' => 'nullable|date',
            'center_type' => 'nullable|in:public,private',
            'center_name' => 'nullable|string|max:200',
            'certificate_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'notes' => 'nullable|string|max:500',
        ]);

        /**
         * Remplacer image
         */
        if ($request->hasFile('certificate_image')) {

            /**
             * Supprimer ancienne image
             */
            if ($vaccination->certificate_image) {

                Storage::disk('public')
                    ->delete($vaccination->certificate_image);
            }

            /**
             * Nouvelle image
             */
            $validated['certificate_image'] =
                $request->file('certificate_image')
                ->store(
                    'certificates/' . date('Y'),
                    'public'
                );
        }

        /**
         * Mise à jour
         */
        $vaccination->update($validated);

        /**
         * Recharger relation vaccin
         */
        $vaccination->load([
            'vaccine'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vaccination mise à jour.',
            'data' => $vaccination,
        ], 200);
    }

    /**
     * DELETE /api/health-profiles/{profileId}/vaccinations/{id}
     * Supprimer une vaccination (+ image associée).
     */
    public function destroy(Request $request, int $profileId, int $id): JsonResponse
    {

        /**
         * Vérifier profil
         */
        $profile = HealthProfile::where([
            'id_profile' => $profileId,
            'user_id' => $request->id_user,
            'is_active' => true,
        ])
            ->first();

        if (!$profile) {

            return response()->json([
                'success' => false,
                'message' => 'Profil introuvable.',
            ], 404);
        }

        /**
         * Vérifier vaccination
         */
        $vaccination = ProfileVaccination::where([
            'id_vaccination' => $id,
            'profile_id' => $profileId,
        ])
            ->first();

        if (!$vaccination) {

            return response()->json([
                'success' => false,
                'message' => 'Vaccination introuvable.',
            ], 404);
        }

        /**
         * Supprimer image
         */
        if ($vaccination->certificate_image) {

            Storage::disk('public')
                ->delete($vaccination->certificate_image);
        }

        /**
         * Supprimer vaccination
         */
        $vaccination->delete();

        return response()->json([
            'success' => true,
            'message' => 'Vaccination supprimée.',
        ], 200);
    }

    /**
     * DELETE /api/health-profiles/{profileId}/vaccinations/{id}/certificate
     * Supprimer uniquement l'image du certificat.
     */
    public function deleteCertificate(Request $request, int $profileId, int $id): JsonResponse
    {

        /**
         * Vérifier profil
         */
        $profile = HealthProfile::where([
            'id_profile' => $profileId,
            'user_id' => $request->id_user,
            'is_active' => true,
        ])
            ->first();

        if (!$profile) {

            return response()->json([
                'success' => false,
                'message' => 'Profil introuvable.',
            ], 404);
        }

        /**
         * Vérifier vaccination
         */
        $vaccination = ProfileVaccination::where([
            'id_vaccination' => $id,
            'profile_id' => $profileId,
        ])
            ->first();

        if (!$vaccination) {

            return response()->json([
                'success' => false,
                'message' => 'Vaccination introuvable.',
            ], 404);
        }

        /**
         * Vérifier image
         */
        if (!$vaccination->certificate_image) {

            return response()->json([
                'success' => false,
                'message' => 'Aucune image trouvée.',
            ], 422);
        }

        /**
         * Supprimer image
         */
        Storage::disk('public')
            ->delete($vaccination->certificate_image);

        /**
         * Mettre champ à null
         */
        $vaccination->update([
            'certificate_image' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Certificat supprimé.',
        ], 200);
    }
}
