<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Vaccin;
use App\Models\Vaccine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VaccinsController extends Controller
{
    /**
     * Liste des vaccins
     */
    public function index(Request $request)
    {
        $vaccines = Vaccine::query()

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

            ->latest()

            ->paginate(15);

        /**
         * Charger catégories manuellement
         */
        foreach ($vaccines as $vaccine) {

            $vaccine->categories = DB::table('vaccine_category')

                ->join(
                    'categories',
                    'categories.id_categorie',
                    '=',
                    'vaccine_category.category_id'
                )

                ->where(
                    'vaccine_category.vaccine_id',
                    $vaccine->id_vaccine
                )

                ->select(
                    'categories.id_categorie',
                    'categories.name'
                )

                ->get();
        }

        /**
         * Stats
         */
        $stats = [
            'total' => Vaccine::count(),
            'active' => Vaccine::where('is_active', true)->count(),
            'human' => Vaccine::where('vaccine_type', 'human')->count(),
            'animal' => Vaccine::where('vaccine_type', 'animal')->count(),
        ];

        $allCategories = Categorie::orderBy('name')->get();

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
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:vaccines,slug',
            'short_name' => 'nullable|string|max:50',

            'description' => 'nullable|string',

            'public_price' => 'nullable|numeric|min:0',
            'private_price_min' => 'nullable|numeric|min:0',
            'private_price_max' => 'nullable|numeric|min:0',

            'vaccine_type' => 'required|in:human,animal',

            'important_info' => 'nullable|string',

            'is_active' => 'nullable',

            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id_categorie',
        ]);

        /**
         * Générer slug si vide
         */
        $slug = $validated['slug']
            ?? Str::slug($validated['name']);

        /**
         * Vérifier unicité slug auto
         */
        if (Vaccine::where('slug', $slug)->exists()) {

            $slug .= '-' . time();
        }

        /**
         * Création
         */
        $vaccine = Vaccine::create([
            'name' => $validated['name'],

            'slug' => $slug,

            'short_name' => $validated['short_name'] ?? null,

            'description' => $validated['description'] ?? null,

            'public_price' => $validated['public_price'] ?? 0,

            'private_price_min' => $validated['private_price_min'] ?? null,

            'private_price_max' => $validated['private_price_max'] ?? null,

            'vaccine_type' => $validated['vaccine_type'],

            'important_info' => $validated['important_info'] ?? null,

            'is_active' => $request->has('is_active'),
        ]);

        /**
         * Catégories
         */
        if ($request->categories) {

            foreach ($request->categories as $categoryId) {

                DB::table('vaccine_category')->insert([
                    'vaccine_id' => $vaccine->id_vaccine,
                    'category_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()
            ->route('vaccins.vaccins')
            ->with('success', 'Vaccin ajouté avec succès.');
    }

    /**
     * Modifier vaccin
     */
    public function update(Request $request, string $id)
    {
        $vaccine = Vaccine::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',

            'slug' => 'nullable|string|max:150|unique:vaccines,slug,' . $vaccine->id_vaccine . ',id_vaccine',

            'short_name' => 'nullable|string|max:50',

            'description' => 'nullable|string',

            'public_price' => 'nullable|numeric|min:0',

            'private_price_min' => 'nullable|numeric|min:0',

            'private_price_max' => 'nullable|numeric|min:0',

            'vaccine_type' => 'required|in:human,animal',

            'important_info' => 'nullable|string',

            'categories' => 'nullable|array',

            'categories.*' => 'exists:categories,id_categorie',
        ]);

        /**
         * Update
         */
        $vaccine->update([
            'name' => $validated['name'],

            'slug' => $validated['slug']
                ?: Str::slug($validated['name']),

            'short_name' => $validated['short_name'] ?? null,

            'description' => $validated['description'] ?? null,

            'public_price' => $validated['public_price'] ?? 0,

            'private_price_min' => $validated['private_price_min'] ?? null,

            'private_price_max' => $validated['private_price_max'] ?? null,

            'vaccine_type' => $validated['vaccine_type'],

            'important_info' => $validated['important_info'] ?? null,

            'is_active' => $request->has('is_active'),
        ]);

        /**
         * Sync catégories
         */
        DB::table('vaccine_category')
            ->where('vaccine_id', $vaccine->id_vaccine)
            ->delete();

        if ($request->categories) {

            foreach ($request->categories as $categoryId) {

                DB::table('vaccine_category')->insert([
                    'vaccine_id' => $vaccine->id_vaccine,
                    'category_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()
            ->route('vaccins.vaccins')
            ->with('success', 'Vaccin mis à jour avec succès.');
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

        $vaccine->delete();

        return redirect()
            ->route('vaccins.vaccins')
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
