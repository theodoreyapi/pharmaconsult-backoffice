<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commune;
use Illuminate\Http\Request;

class ApiCommuneController extends Controller
{
    /**
     * GET /api/communes/search
     * GET /api/communes/search?name=abobo
     *
     * Sans paramètre  → 50 premières communes (ordre alphabétique)
     * Avec ?name=xxx  → filtre par nom (LIKE), 50 résultats max
     */
    public function getCommunes(Request $request)
    {
        $query = $request->query('name');

        $communes = Commune::when($query, function ($q) use ($query) {
                $q->where('name', 'LIKE', '%' . $query . '%');
            })
            ->orderBy('name', 'asc')
            ->limit(50)
            ->select('id_commune as id', 'name', 'description')
            ->get();

        return response()->json([
            'content' => $communes,
        ], 200);
    }

}
