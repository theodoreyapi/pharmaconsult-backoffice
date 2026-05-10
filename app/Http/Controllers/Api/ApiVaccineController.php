<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Vaccine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class ApiVaccineController extends Controller
{
    /**
     * GET /api/vaccines
     * Liste tous les vaccins actifs avec filtrage par catégorie et recherche.
     *
     * Query params:
     *   - search   : string  — recherche dans name, short_name, description
     *   - category : string  — slug de la catégorie (ex: "enfants-5-ans")
     *   - per_page : int     — nombre d'éléments par page (défaut: 20)
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $category = $request->category;

        $vaccines = Vaccine::leftJoin('vaccine_category', 'vaccines.id_vaccine', '=', 'vaccine_category.vaccine_id')
            ->leftJoin('categories', 'categories.id_categorie', '=', 'vaccine_category.category_id')

            ->select(
                'vaccines.id_vaccine',
                'vaccines.name',
                'vaccines.slug',
                'vaccines.short_name',
                'vaccines.description',
                'vaccines.public_price',
                'vaccines.private_price_min',
                'vaccines.private_price_max',
                'vaccines.currency',
                'vaccines.important_info',

                'categories.id_categorie',
                'categories.name as category_name',
                'categories.slug as category_slug'
            )

            ->where('vaccines.is_active', true)

            // Recherche
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('vaccines.name', 'LIKE', "%{$search}%")
                        ->orWhere('vaccines.short_name', 'LIKE', "%{$search}%")
                        ->orWhere('vaccines.description', 'LIKE', "%{$search}%");
                });
            })

            // Filtre catégorie
            ->when(
                $category && $category !== 'tous-les-vaccins',
                function ($query) use ($category) {
                    $query->where('categories.slug', $category);
                }
            )

            ->orderBy('vaccines.name', 'ASC')
            ->get();

        // Grouper les catégories par vaccin
        $groupedVaccines = $vaccines->groupBy('id_vaccine')->map(function ($items) {

            $first = $items->first();

            return [
                'id_vaccine' => $first->id_vaccine,
                'name' => $first->name,
                'slug' => $first->slug,
                'short_name' => $first->short_name,
                'description' => $first->description,
                'public_price' => $first->public_price,
                'private_price_min' => $first->private_price_min,
                'private_price_max' => $first->private_price_max,
                'currency' => $first->currency,
                'important_info' => $first->important_info,

                'categories' => $items->map(function ($item) {
                    return [
                        'id_categorie' => $item->id_categorie,
                        'name' => $item->category_name,
                        'slug' => $item->category_slug,
                    ];
                })->unique('id_categorie')->values(),
            ];
        })->values();

        return response()->json(
            $groupedVaccines
        );
    }
}
