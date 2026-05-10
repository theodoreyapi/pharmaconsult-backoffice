<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;

class ApiCategoryController extends Controller
{
    /**
     * GET /api/categories
     * Liste toutes les catégories disponibles.
     */
    public function index()
    {
        $categories = Categorie::orderBy('name')
            ->select('id_categorie', 'name', 'slug')
            ->get();

        return response()->json(
            $categories,
        );
    }
}
