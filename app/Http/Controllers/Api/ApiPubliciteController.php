<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiPubliciteController extends Controller
{
    /**
     * Liste toutes les publicités
     */
    public function index()
    {
        $publicities = DB::table('publicities')->get();
        return response()->json($publicities);
    }

    /**
     * Récupère une publicité spécifique
     */
    public function show($id)
    {
        $publicity = DB::table('publicities')->where('id', $id)->first();
        if (!$publicity) return response()->json(['message' => 'Publicité non trouvée'], 404);

        return response()->json($publicity);
    }

    /**
     * Crée une nouvelle publicité
     */
    public function store(Request $request)
    {
        $id = DB::table('publicities')->insertGetId([
            'name' => $request->name,
            'lien' => $request->lien ?? '',
            'image' => $request->image ?? '',
            'start_date' => $request->startDate,
            'end_date' => $request->endDate,
            'status' => $request->status ?? 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['id' => $id, 'message' => 'Publicité créée']);
    }

    /**
     * Met à jour une publicité
     */
    public function update(Request $request, $id)
    {
        $updated = DB::table('publicities')->where('id', $id)->update([
            'name' => $request->name ?? DB::raw('name'),
            'lien' => $request->lien ?? DB::raw('lien'),
            'image' => $request->image ?? DB::raw('image'),
            'start_date' => $request->startDate ?? DB::raw('start_date'),
            'end_date' => $request->endDate ?? DB::raw('end_date'),
            'status' => $request->status ?? DB::raw('status'),
            'updated_at' => now(),
        ]);

        if (!$updated) return response()->json(['message' => 'Publicité non trouvée ou inchangée'], 404);

        return response()->json(['message' => 'Publicité mise à jour']);
    }

    /**
     * Supprime une publicité
     */
    public function destroy($id)
    {
        $deleted = DB::table('publicities')->where('id', $id)->delete();
        if (!$deleted) return response()->json(['message' => 'Publicité non trouvée'], 404);

        return response()->json(['message' => 'Publicité supprimée']);
    }

    /**
     * Liste toutes les publicités actives
     */
    public function active()
    {
        $today = date('Y-m-d');
        $publicities = DB::table('publicities')
            ->where('status', 'active')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->get();

        return response()->json($publicities);
    }

    /**
     * Liste toutes les publicités par statut
     */
    public function byStatus($status)
    {
        $publicities = DB::table('publicities')
            ->where('status', $status)
            ->get();

        return response()->json($publicities);
    }
}
