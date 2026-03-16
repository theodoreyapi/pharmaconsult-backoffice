<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicamants;
use App\Models\MedicamantSubtituts;
use Illuminate\Http\Request;

class ApiMedicamentController extends Controller
{
    /**
     * GET /api/search
     * GET /api/search?name=paracetamol
     *
     * Sans paramètre  → 100 premiers médicaments (ordre alphabétique)
     * Avec ?name=xxx  → filtre par nom ou principe actif (LIKE), 100 résultats max
     */
    public function search(Request $request)
    {
        $query = $request->query('name');

        $medicaments = Medicamants::when($query, function ($q) use ($query) {
            $q->where(function ($sub) use ($query) {
                $sub->where('medicaments.name', 'LIKE', '%' . $query . '%')
                    ->orWhere('medicaments.principe_actif', 'LIKE', '%' . $query . '%')
                    ->orWhere('medicaments.code_cip', 'LIKE', '%' . $query . '%');
            });
        })
            ->orderBy('medicaments.name', 'asc')
            ->limit(100)
            ->select(
                'id_medicament as id',
                'name',
                'principe_actif as principeActif',
                'medicament_picture as medicamentPicture',
                'notice',
                'code_cip as codeCip',
                'price'
            )
            ->get();

        // Pour chaque médicament, charger ses substituts
        $result = $medicaments->map(function ($med) {
            $substitutes = MedicamantSubtituts::join('medicaments as sub_med', 'medicament_substituts.substitut_id', '=', 'sub_med.id_medicament')
                ->where('medicament_substituts.medicament_id', $med->id)
                ->select(
                    'medicament_substituts.id_subtitut as id',
                    'sub_med.id_medicament as substitutId',
                    'sub_med.name as substitutName',
                    'sub_med.medicament_picture as substitutImageId',
                    'sub_med.principe_actif as substitutPrincipleActif',
                    'sub_med.price as substitutPrice',
                    'sub_med.notice as substitutNotice',
                    'medicament_substituts.medicament_id as medicamentId'
                )
                ->get()
                ->map(fn($s) => [
                    'id'                    => $s->id,
                    'substitutId'           => $s->substitutId,
                    'substitutName'         => $s->substitutName ?? '',
                    'substitutImageId'      => $s->substitutImageId ?? '',
                    'substitutPrincipleActif' => $s->substitutPrincipleActif ?? '',
                    'substitutPrice'        => $s->substitutPrice ?? '',
                    'substitutNotice'       => $s->substitutNotice ?? '',
                    'medicamentId'          => $s->medicamentId ?? 0,
                ])
                ->values();

            return [
                'id'               => $med->id,
                'name'             => $med->name,
                'principeActif'    => $med->principeActif ?? '',
                'medicamentPicture' => $med->medicamentPicture ?? '',
                'notice'           => $med->notice ?? '',
                'codeCip'          => $med->codeCip,
                'price'            => $med->price ?? '',
                'substitutes'      => $substitutes,
            ];
        });

        return response()->json([
            'content' => $result,
        ], 200);
    }
}
