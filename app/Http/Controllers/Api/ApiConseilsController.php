<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conseils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiConseilsController extends Controller
{
    public function conseils()
    {

        $conseil = Conseils::orderBy('created_at', 'desc')->get();

        return response()->json($conseil);
    }
    public function campagnes()
    {

        $campagnes = DB::table('campagnes')
            ->leftJoin('pathologies', 'pathologies.id_pathologie', '=', 'campagnes.pathologie_id')
            ->leftJoin('pharmacy', 'pharmacy.id_pharmacy', '=', 'campagnes.pharmacy_id')
            ->select(
                'campagnes.*',
                'pathologies.code as pathologie_code',
                'pathologies.name as pathologie_name',
                'pharmacy.name as pharmacy_name',
            )
            ->latest('campagnes.created_at')
            ->get();

        return response()->json($campagnes);
    }
}
