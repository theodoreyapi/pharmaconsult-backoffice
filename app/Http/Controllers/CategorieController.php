<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Vaccine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategorieController extends Controller
{
    /**
     * Liste des catégories
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $categories = Categorie::query()

            /**
             * Nombre de vaccins liés
             */
            ->leftJoin(
                'vaccine_category',
                'categories.id_categorie',
                '=',
                'vaccine_category.category_id'
            )

            ->select(
                'categories.*',
                DB::raw('COUNT(vaccine_category.vaccine_id) as vaccines_count')
            )

            /**
             * Recherche
             */
            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('categories.name', 'LIKE', "%{$search}%")
                        ->orWhere('categories.slug', 'LIKE', "%{$search}%")
                        ->orWhere('categories.description', 'LIKE', "%{$search}%");
                });
            })

            /**
             * Group by obligatoire
             */
            ->groupBy(
                'categories.id_categorie',
                'categories.name',
                'categories.slug',
                'categories.description',
                'categories.created_at',
                'categories.updated_at'
            )

            /**
             * Tri
             */
            ->orderBy('categories.created_at', 'DESC')

            /**
             * Pagination
             */
            ->paginate(15)
            ->withQueryString();

        /**
         * Total vaccins
         */
        $totalVaccines = Vaccine::count();

        return view('vaccins.categories', compact(
            'categories',
            'totalVaccines'
        ));
    }

    /**
     * Enregistrer catégorie
     */
    public function store(Request $request)
    {
        /**
         * Validation
         */
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'slug' => 'nullable|string|max:100|unique:categories,slug',
            'description' => 'nullable|string',
        ]);

        /**
         * Slug auto
         */
        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        /**
         * Vérifier unicité slug
         */
        $slugExists = Categorie::where('slug', $slug)->exists();

        if ($slugExists) {

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ce slug existe déjà.');
        }

        /**
         * Création
         */
        Categorie::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Afficher catégorie
     */
    public function show(string $id)
    {
        $category = Categorie::with([
            'vaccines'
        ])->findOrFail($id);

        return view('categories.index', compact('category'));
    }

    /**
     * Form edit
     */
    public function edit(string $id)
    {
        $category = Categorie::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    /**
     * Modifier catégorie
     */
    public function update(Request $request, string $id)
    {
        $category = Categorie::findOrFail($id);

        /**
         * Validation
         */
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $id . ',id_categorie',
            'slug' => 'nullable|string|max:100|unique:categories,slug,' . $id . ',id_categorie',
            'description' => 'nullable|string',
        ]);

        /**
         * Slug auto
         */
        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        /**
         * Update
         */
        $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprimer catégorie
     */
    public function destroy(string $id)
    {
        $linkedVaccines = DB::table('vaccine_category')
            ->where('category_id', $id)
            ->count();

        if ($linkedVaccines > 0) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Impossible de supprimer une catégorie liée à des vaccins.'
                );
        }

        $category = Categorie::findOrFail($id);

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with(
                'success',
                'Catégorie supprimée avec succès.'
            );
    }
}
