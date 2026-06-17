<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Vaccine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VaccinsController extends Controller
{
    /**
     * Liste des vaccins
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $vaccines = DB::table('vaccines')

            ->when($request->search, function ($q) use ($request) {

                $q->where(function ($sub) use ($request) {

                    $sub->where('name', 'LIKE', '%' . $request->search . '%')
                        ->orWhere('slug', 'LIKE', '%' . $request->search . '%')
                        ->orWhere('short_name', 'LIKE', '%' . $request->search . '%');
                });
            })

            ->when($request->type, function ($q) use ($request) {

                $q->where('vaccine_type', $request->type);
            })

            ->when($request->status !== null && $request->status !== '', function ($q) use ($request) {

                $q->where('is_active', $request->status);
            })

            ->when($request->category, function ($q) use ($request) {

                $q->whereIn('id_vaccine', function ($sub) use ($request) {

                    $sub->select('vaccine_id')
                        ->from('vaccine_category')
                        ->where('category_id', $request->category);
                });
            })

            ->orderByDesc('id_vaccine')

            ->paginate(15);

        /**
         * IDs vaccins
         */
        $ids = $vaccines->pluck('id_vaccine')->toArray();

        /**
         * Catégories
         */
        $categories = DB::table('vaccine_category')

            ->join(
                'categories',
                'categories.id_categorie',
                '=',
                'vaccine_category.category_id'
            )

            ->whereIn('vaccine_category.vaccine_id', $ids)

            ->select(
                'vaccine_category.vaccine_id',
                'categories.id_categorie',
                'categories.name'
            )

            ->get()

            ->groupBy('vaccine_id');

        /**
         * Schedules
         */
        $schedules = DB::table('vaccine_schedules')

            ->whereIn('vaccine_id', $ids)

            ->get()

            ->keyBy('vaccine_id');

        /**
         * Restrictions
         */
        $restrictions = DB::table('vaccine_restrictions')

            ->whereIn('vaccine_id', $ids)

            ->get()

            ->groupBy('vaccine_id');

        /**
         * Equivalents
         */
        $equivalents = DB::table('vaccine_equivalents')
            ->whereIn('vaccine_id', $ids)
            ->get()
            ->groupBy('vaccine_id');

        /**
         * Injection dans chaque vaccin
         */
        foreach ($vaccines as $vaccine) {

            $vaccine->categories =
                $categories[$vaccine->id_vaccine] ?? collect();

            $vaccine->schedule =
                $schedules[$vaccine->id_vaccine] ?? null;

            $vaccine->restrictions =
                $restrictions[$vaccine->id_vaccine] ?? collect();

            $vaccine->equivalents =
                $equivalents[$vaccine->id_vaccine] ?? collect();
        }

        /**
         * Stats
         */
        $stats = [

            'total' => DB::table('vaccines')->count(),

            'active' => DB::table('vaccines')
                ->where('is_active', 1)
                ->count(),

            'human' => DB::table('vaccines')
                ->where('vaccine_type', 'human')
                ->count(),

            'animal' => DB::table('vaccines')
                ->where('vaccine_type', 'animal')
                ->count(),

            'equivalents' => DB::table('vaccine_equivalents')->count(),
        ];

        $allCategories = DB::table('categories')
            ->orderBy('name')
            ->get();

        return view('vaccins.vaccins', compact(
            'vaccines',
            'stats',
            'allCategories'
        ));
    }

    /**
     * Ajouter vaccin
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                        => 'required|string|max:150',
            'slug'                        => 'nullable|string|max:150|unique:vaccines,slug',
            'short_name'                  => 'nullable|string|max:50',
            'description'                 => 'nullable|string',
            'target_species'                 => 'nullable|string',
            'validation_status'                 => 'nullable|string',
            'targeted_disease'                 => 'nullable|string',
            'administration_mode'                 => 'nullable|string',
            'scientific_type'                 => 'nullable|string',
            'protected_against'                 => 'nullable|string',
            'target_public'                 => 'nullable|string',
            'source_url'                 => 'nullable|string',
            'public_price'                => 'nullable|numeric|min:0',
            'vaccine_type'                => 'required|in:human,animal',
            'important_info'              => 'nullable|string',
            'categories'                  => 'nullable|array',
            'categories.*'                => 'exists:categories,id_categorie',

            // schedule — préfixe schedule[]
            'schedule.min_age_months'     => 'nullable|numeric|min:0',
            'schedule.max_age_months'     => 'nullable|numeric|min:0',
            'schedule.age_label'          => 'nullable|string|max:100',
            'schedule.gender'             => 'nullable|in:all,masculin,feminin',
            'schedule.dose_number'        => 'nullable|integer|min:1',
            'schedule.priority'           => 'nullable|integer|min:0',
            'schedule.is_booster'         => 'nullable',
            'schedule.booster_every_months' => 'nullable|integer|min:0',
            'schedule.important_note'     => 'nullable|string',
            'schedule.only_pregnant'     => 'nullable',
            'schedule.for_travelers'     => 'nullable',
            'schedule.in_community'     => 'nullable',
            'schedule.for_health_workers'     => 'nullable',
            'schedule.for_immunocompromised'     => 'nullable',
            'schedule.for_seniors'     => 'nullable',
            'schedule.exposed_to_vectors'     => 'nullable',
            'schedule.travel_zone'     => 'nullable',
            'schedule.phase_name'     => 'nullable',

            // restrictions
            'restrictions'                => 'nullable|array',
            'restrictions.*'              => 'in:pregnancy,immunocompromised,allergy',
            'restriction_reason'          => 'nullable|string',

            // équivalent
            'equivalents' => 'nullable|array',
            'equivalents.*.name' => 'required_with:equivalents|string|max:255',
            'equivalents.*.description' => 'nullable|string',
            'equivalents.*.price' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $slug = $validated['slug'] ?? Str::slug($validated['name']);
            if (Vaccine::where('slug', $slug)->exists()) {
                $slug .= '-' . time();
            }

            $vaccine = Vaccine::create([
                'name'              => $validated['name'],
                'slug'              => $slug,
                'short_name'        => $validated['short_name'] ?? null,
                'description'       => $validated['description'] ?? null,
                'public_price'      => $validated['public_price'] ?? 0,
                'vaccine_type'      => $validated['vaccine_type'],
                'important_info'    => $validated['important_info'] ?? null,
                'target_species'    => $validated['target_species'] ?? null,
                'targeted_disease'    => $validated['targeted_disease'] ?? null,
                'administration_mode'    => $validated['administration_mode'] ?? null,
                'scientific_type'    => $validated['scientific_type'] ?? null,
                'protected_against'    => $validated['protected_against'] ?? null,
                'target_public'    => $validated['target_public'] ?? null,
                'source_url'    => $validated['source_url'] ?? null,
                'validation_status'    => $validated['validation_status'] ?? null,
                'is_active'         => $request->boolean('is_active'),
            ]);

            foreach ($request->equivalents ?? [] as $equivalent) {

                DB::table('vaccine_equivalents')->insert([

                    'vaccine_id' => $vaccine->id_vaccine,

                    'name' => $equivalent['name'],

                    'description' => $equivalent['description'] ?? null,

                    'price' => $equivalent['price'] ?? 0,

                    'created_at' => now(),
                    'updated_at' => now(),

                ]);
            }

            // Catégories
            foreach ($request->input('categories', []) as $categoryId) {
                DB::table('vaccine_category')->insert([
                    'vaccine_id'  => $vaccine->id_vaccine,
                    'category_id' => $categoryId,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            // Schedule
            $schedule = $request->input('schedule', []);
            DB::table('vaccine_schedules')->insert([
                'vaccine_id'           => $vaccine->id_vaccine,
                'min_age_months'       => $schedule['min_age_months'] ?? 0,
                'max_age_months'       => $schedule['max_age_months'] ?? null,
                'age_label'            => $schedule['age_label'] ?? null,
                'gender'               => $schedule['gender'] ?? 'all',
                'dose_number'          => $schedule['dose_number'] ?? null,
                'priority'             => $schedule['priority'] ?? 0,
                'is_booster'           => isset($schedule['is_booster']) ? 1 : 0,
                'booster_every_months' => $schedule['booster_every_months'] ?? null,
                'important_note'       => $schedule['important_note'] ?? null,
                'phase_name'           => $schedule['phase_name'] ?? null,
                'in_community'         => $request->boolean('schedule.in_community'),
                'exposed_to_vectors'   => $request->boolean('schedule.exposed_to_vectors'),
                'travel_zone'   => $schedule['travel_zone'] ?? null,
                'only_pregnant'   => $request->boolean('schedule.only_pregnant'),
                'for_health_workers'   => $request->boolean('schedule.for_health_workers'),
                'for_travelers'   => $request->boolean('schedule.for_travelers'),
                'for_immunocompromised'   => $request->boolean('schedule.for_immunocompromised'),
                'for_seniors'   => $request->boolean('schedule.for_seniors'),
                'created_at'           => now(),
                'updated_at'           => now(),
            ]);

            // Restrictions
            $reason = $request->input('restriction_reason');
            foreach ($request->input('restrictions', []) as $type) {
                DB::table('vaccine_restrictions')->insert([
                    'vaccine_id'       => $vaccine->id_vaccine,
                    'restriction_type' => $type,
                    'reason'           => $reason,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('vaccins.index')->with('success', 'Vaccin ajouté avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Modifier vaccin
     */
    public function update(Request $request, string $id)
    {
        $vaccine = Vaccine::findOrFail($id);

        $validated = $request->validate([
            'name'              => 'required|string|max:150',
            'slug'              => 'nullable|string|max:150|unique:vaccines,slug,' . $vaccine->id_vaccine . ',id_vaccine',
            'short_name'                  => 'nullable|string|max:50',
            'description'                 => 'nullable|string',
            'target_species'                 => 'nullable|string',
            'validation_status'                 => 'nullable|string',
            'targeted_disease'                 => 'nullable|string',
            'administration_mode'                 => 'nullable|string',
            'scientific_type'                 => 'nullable|string',
            'protected_against'                 => 'nullable|string',
            'target_public'                 => 'nullable|string',
            'source_url'                 => 'nullable|string',
            'public_price'                => 'nullable|numeric|min:0',
            'vaccine_type'                => 'required|in:human,animal',
            'important_info'              => 'nullable|string',
            'categories'                  => 'nullable|array',
            'categories.*'      => 'exists:categories,id_categorie',

            'schedule.min_age_months'       => 'nullable|numeric|min:0',
            'schedule.max_age_months'       => 'nullable|numeric|min:0',
            'schedule.age_label'            => 'nullable|string|max:100',
            'schedule.gender'               => 'nullable|in:all,masculin,feminin',
            'schedule.dose_number'          => 'nullable|integer|min:1',
            'schedule.priority'             => 'nullable|integer|min:0',
            'schedule.is_booster'         => 'nullable',
            'schedule.booster_every_months' => 'nullable|integer|min:0',
            'schedule.important_note'     => 'nullable|string',
            'schedule.only_pregnant'     => 'nullable',
            'schedule.for_travelers'     => 'nullable',
            'schedule.in_community'     => 'nullable',
            'schedule.for_health_workers'     => 'nullable',
            'schedule.for_immunocompromised'     => 'nullable',
            'schedule.for_seniors'     => 'nullable',
            'schedule.exposed_to_vectors'     => 'nullable',
            'schedule.travel_zone'     => 'nullable',
            'schedule.phase_name'     => 'nullable',

            'restrictions'      => 'nullable|array',
            'restrictions.*'    => 'in:pregnancy,immunocompromised,allergy',
            'restriction_reason' => 'nullable|string',

            'equivalents_edit'            => 'nullable|array',
            'equivalents_edit.*.name'     => 'required|string|max:150',
            'equivalents_edit.*.description' => 'nullable|string',
            'equivalents_edit.*.price'    => 'nullable|numeric|min:0',
        ]);

        $vaccine->update([
            'name'              => $validated['name'],
            'slug'              => $validated['slug'] ?: Str::slug($validated['name']),
            'short_name'        => $validated['short_name'] ?? null,
            'description'       => $validated['description'] ?? null,
            'public_price'      => $validated['public_price'] ?? 0,
            'vaccine_type'      => $validated['vaccine_type'],
            'important_info'    => $validated['important_info'] ?? null,
            'target_species'    => $validated['target_species'] ?? null,
            'targeted_disease'    => $validated['targeted_disease'] ?? null,
            'administration_mode'    => $validated['administration_mode'] ?? null,
            'scientific_type'    => $validated['scientific_type'] ?? null,
            'protected_against'    => $validated['protected_against'] ?? null,
            'target_public'    => $validated['target_public'] ?? null,
            'source_url'    => $validated['source_url'] ?? null,
            'validation_status'    => $validated['validation_status'] ?? null,
            'is_active'         => $request->boolean('is_active'),
        ]);

        // Sync catégories
        DB::table('vaccine_category')->where('vaccine_id', $vaccine->id_vaccine)->delete();
        foreach ($request->input('categories', []) as $categoryId) {
            DB::table('vaccine_category')->insert([
                'vaccine_id'  => $vaccine->id_vaccine,
                'category_id' => $categoryId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        // Sync schedule (upsert sur le premier schedule existant)
        $schedule = $request->input('schedule', []);
        $scheduleData = [
            'min_age_months'        => $schedule['min_age_months'] ?? 0,
            'max_age_months'        => $schedule['max_age_months'] ?? null,
            'age_label'             => $schedule['age_label'] ?? null,
            'gender'                => $schedule['gender'] ?? 'all',
            'dose_number'           => $schedule['dose_number'] ?? null,
            'priority'              => $schedule['priority'] ?? 0,
            'is_booster'            => !empty($schedule['is_booster']) ? 1 : 0,
            'booster_every_months'  => $schedule['booster_every_months'] ?? null,
            'important_note'        => $schedule['important_note'] ?? null,
            'phase_name'            => $schedule['phase_name'] ?? null,
            'in_community'          => !empty($schedule['in_community']) ? 1 : 0,     // ✅
            'exposed_to_vectors'    => !empty($schedule['exposed_to_vectors']) ? 1 : 0, // ✅
            'travel_zone'           => $schedule['travel_zone'] ?? null,
            'only_pregnant'         => !empty($schedule['only_pregnant']) ? 1 : 0,    // ✅
            'for_health_workers'    => !empty($schedule['for_health_workers']) ? 1 : 0, // ✅
            'for_travelers'         => !empty($schedule['for_travelers']) ? 1 : 0,    // ✅
            'for_immunocompromised' => !empty($schedule['for_immunocompromised']) ? 1 : 0, // ✅
            'for_seniors'           => !empty($schedule['for_seniors']) ? 1 : 0,      // ✅
            'updated_at'            => now(),
        ];
        $existing = DB::table('vaccine_schedules')->where('vaccine_id', $vaccine->id_vaccine)->first();
        if ($existing) {
            DB::table('vaccine_schedules')
                ->where('id_schedule', $existing->id_schedule)
                ->update($scheduleData);
        } else {
            DB::table('vaccine_schedules')->insert(
                array_merge($scheduleData, ['vaccine_id' => $vaccine->id_vaccine, 'created_at' => now()])
            );
        }

        // Sync restrictions
        DB::table('vaccine_restrictions')->where('vaccine_id', $vaccine->id_vaccine)->delete();
        $reason = $request->input('restriction_reason');
        foreach ($request->input('restrictions', []) as $type) {
            DB::table('vaccine_restrictions')->insert([
                'vaccine_id'       => $vaccine->id_vaccine,
                'restriction_type' => $type,
                'reason'           => $reason,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        // Sync équivalents
        DB::table('vaccine_equivalents')->where('vaccine_id', $vaccine->id_vaccine)->delete();
        foreach ($request->input('equivalents_edit', []) as $eq) {
            if (empty($eq['name'])) continue;
            DB::table('vaccine_equivalents')->insert([
                'vaccine_id'  => $vaccine->id_vaccine,
                'name'        => $eq['name'],
                'description' => $eq['description'] ?? null,
                'price'       => $eq['price'] ?? 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        return redirect()->route('vaccins.index')->with('success', 'Vaccin mis à jour avec succès.');
    }

    /**
     * Supprimer vaccin
     */
    public function destroy(string $id)
    {
        $vaccine = Vaccine::findOrFail($id);

        DB::table('vaccine_category')
            ->where('vaccine_id', $vaccine->id_vaccine)
            ->delete();

        DB::table('vaccine_schedules')
            ->where('vaccine_id', $vaccine->id_vaccine)
            ->delete();

        DB::table('vaccine_restrictions')
            ->where('vaccine_id', $vaccine->id_vaccine)
            ->delete();

        DB::table('vaccine_equivalents')
            ->where('vaccine_id', $vaccine->id_vaccine)
            ->delete();

        $vaccine->delete();

        return redirect()
            ->route('vaccins.index')
            ->with('success', 'Vaccin supprimé avec succès.');
    }

    /**
     * Activer / désactiver
     */
    public function toggle(string $id)
    {
        $vaccine = Vaccine::findOrFail($id);

        $vaccine->update([
            'is_active' => !$vaccine->is_active
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                $vaccine->is_active
                    ? 'Vaccin activé avec succès.'
                    : 'Vaccin désactivé avec succès.'
            );
    }
}
