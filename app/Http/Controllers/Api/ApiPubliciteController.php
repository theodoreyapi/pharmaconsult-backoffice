<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Publicite;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiPubliciteController extends Controller
{
    /**
     * GET /api/get/actives
     * Retourne les publicités actives dont la date est en cours
     */
    public function getActive()
    {
        $now = Carbon::now();

        $publicites = Publicite::where('status', 'active')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->orderBy('start_date', 'desc')
            ->select(
                'id_publicite as id',
                'name',
                'image',
                'lien',
                'start_date as startDate',
                'end_date as endDate',
                'status'
            )
            ->get();

        return response()->json($publicites, 200);
    }
}
