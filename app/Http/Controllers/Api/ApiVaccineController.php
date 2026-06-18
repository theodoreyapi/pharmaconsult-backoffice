<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Vaccine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
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

        // Charger les équivalents pour tous les vaccins concernés (une seule requête)
        $vaccineIds = $vaccines->pluck('id_vaccine')->unique();

        $equivalents = DB::table('vaccine_equivalents')
            ->whereIn('vaccine_id', $vaccineIds)
            ->where('is_active', true)
            ->get()
            ->groupBy('vaccine_id');

        // Grouper les catégories par vaccin
        $groupedVaccines = $vaccines->groupBy('id_vaccine')->map(function ($items) use ($equivalents) {

            $first = $items->first();

            return [
                'id_vaccine' => $first->id_vaccine,
                'name' => $first->name,
                'slug' => $first->slug,
                'short_name' => $first->short_name,
                'description' => $first->description,
                'public_price' => $first->public_price,
                'currency' => $first->currency,
                'important_info' => $first->important_info,

                'categories' => $items->map(function ($item) {
                    return [
                        'id_categorie' => $item->id_categorie,
                        'name' => $item->category_name,
                        'slug' => $item->category_slug,
                    ];
                })->unique('id_categorie')->values(),

                'equivalents' => $equivalents->get($first->id_vaccine, collect())->map(function ($eq) {
                    return [
                        'id_equivalent' => $eq->id_equivalent,
                        'name' => $eq->name,
                        'description' => $eq->description,
                        'price' => $eq->price,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json(
            $groupedVaccines
        );
    }

    /**
     * GET /api/vaccines/type/{type}
     * Liste tous les vaccins actifs d'un type donné.
     *
     */
    public function type($type)
    {
        $vaccines = Vaccine::where('is_active', true)
            ->where('vaccine_type', $type)
            ->select(
                'id_vaccine',
                'name',
            )
            ->get();

        return response()->json(
            $vaccines
        );
    }
}
