<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assurances;
use Illuminate\Http\Request;

class ApiAssuranceController extends Controller
{
    /**
     * GET /api/assurances/getAll
     * GET /api/assurances/getAll?name=sante
     *
     * Sans paramètre  → 100 premières assurances (ordre alphabétique)
     * Avec ?name=xxx  → filtre par nom (LIKE), 100 résultats max
     */
    public function getAssurance(Request $request)
    {
        $query = $request->query('name');

        $assurances = Assurances::when($query, function ($q) use ($query) {
            $q->where('name', 'LIKE', '%' . $query . '%');
        })
            ->orderBy('name', 'asc')
            ->limit(100)
            ->select(
                'id_assurance as id',
                'name',
                'assurance_picture as assurancePicture'
            )
            ->get()
            ->map(fn($a) => [
                'id'              => $a->id,
                'name'            => $a->name,
                'assurancePicture' => $a->assurancePicture ?? '',
            ])
            ->values();

        return response()->json(
            $assurances,
            200
        );
    }
}
