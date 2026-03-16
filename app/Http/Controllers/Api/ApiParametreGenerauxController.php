<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ParametresGeneraux;
use Illuminate\Http\Request;

class ApiParametreGenerauxController extends Controller
{
    /**
     * Méthode générique pour récupérer un paramètre par type
     */
    public function getByType(string $type)
    {
        $parametre = ParametresGeneraux::where('type', $type)
            ->select(
                'id_politique as id',
                'libelle',
                'contenu',
                'type',
                'date_create as dateCreate'
            )
            ->first();

        if (!$parametre) {
            return response()->json(['message' => "Contenu introuvable pour le type : $type"], 404);
        }

        return response()->json($parametre, 200);
    }
}
