<?php

namespace App\Http\Controllers;

use App\Models\Medicamants;
use App\Models\MedicamantSubtituts;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PriceFicheController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        // CORRECTION : utiliser paginate() au lieu de all() + calcul manuel
        $medicaments = Medicamants::orderBy('name', 'asc')->paginate(50);

        return view('pharmacies.medicament', compact('medicaments'));
    }

    public function searchh(Request $request)
    {
        $search = $request->get('search', '');

        if (strlen($search) < 2) {
            return response()->json([]); // Retourne un tableau vide JSON
        }

        // Vérifie bien l'orthographe de ton modèle : Medicamants ?
        $medicaments = Medicamants::where('name', 'LIKE', "%$search%")
            ->select('id_medicament', 'name')
            ->limit(10)
            ->get();

        // Retourne directement la collection, Laravel s'occupe de la conversion
        return response()->json($medicaments);
    }

    /**
     * Recherche AJAX des médicaments
     */
    public function search(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['html' => ''], 401);
        }

        $name = $request->get('name', '');

        if (strlen($name) < 3) {
            return response()->json(['html' => '']);
        }

        // CORRECTION : filtrer par nom au lieu de récupérer tous les médicaments
        $medicaments = Medicamants::where('name', 'LIKE', "%{$name}%")
            ->orWhere('principe_actif', 'LIKE', "%{$name}%")
            ->orWhere('code_cip', 'LIKE', "%{$name}%")
            ->orderBy('name', 'asc')
            ->limit(50)
            ->get();

        $html = view('pharmacies.partials.medicament-list', compact('medicaments'))->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }
        $roles = [
            'name' => 'required',
            'code' => '',
            'price' => 'required',
            'principe' => 'required',
        ];
        $customMessages = [
            'name.required' => "Veuillez saisir le nom du médicament.",
            'price.required' => "Veuillez saisir le prix du médicament.",
            'principe.required' => "Veuillez saisir principe du médicament.",
        ];

        $request->validate($roles, $customMessages);

        $timestamp = Carbon::now()->format('Ymd_His');

        $diplomePath = null;
        $imagePathNotice = null;
        // Gestion image
        if ($request->file('photo')) {
            $file = $request->file('photo');
            $name = 'medicament_' . $timestamp . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('medicaments'), $name);
            $diplomePath = url('admin/public/medicaments/' . $name);
        }

        // Gestion notice
        if ($request->file('notice')) {
            $file = $request->file('notice');
            $name = 'medicament_notice_' . $timestamp . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('notices'), $name);
            $imagePathNotice = url('admin/public/notices/' . $name);
        }

        // Create
        // ── Créer le médicament ───────────────────────────────────────────────
        $medicament = Medicamants::create([
            'name'               => $request->name,
            'price'              => $request->price,
            'principe_actif'     => $request->principe,
            'medicament_picture' => $diplomePath,
            'notice'             => $imagePathNotice,
        ]);

        // ── Insérer les substituts ────────────────────────────────────────────
        if ($request->has('substituts') && !empty($request->substituts)) {

            // Réindexer après filtrage pour éviter les trous de clés
            $substituts = array_values(array_filter(
                $request->substituts,
                fn($id) => (int) $id !== (int) $medicament->id_medicament
            ));

            if (!empty($substituts)) {
                $rows = array_map(fn($id) => [
                    'medicament_id' => $medicament->id_medicament,
                    'substitut_id'  => (int) $id,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ], $substituts);

                MedicamantSubtituts::insert($rows);
            }
        }

        return back()->with('succes',  "Médicament ajouté avec succès. ");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $roles = [
            'name' => 'required',
            'price' => 'required',
            'principe' => 'required',
        ];
        $customMessages = [
            'name.required' => "Veuillez saisir le nom du médicament.",
            'price.required' => "Veuillez saisir le prix du médicament.",
            'principe.required' => "Veuillez saisir principe du médicament.",
        ];

        $request->validate($roles, $customMessages);

        $timestamp = Carbon::now()->format('Ymd_His');

        $medicament = Medicamants::findOrFail($id);

        // Gestion image
        if ($request->file('photo')) {

            // 🔥 Supprimer ancienne image
            if ($medicament->medicament_picture) {

                // récupérer le chemin du fichier depuis l'URL
                $oldPath = public_path(parse_url($medicament->medicament_picture, PHP_URL_PATH));

                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('photo');
            $name = 'medicament_' . $timestamp . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('medicaments'), $name);
            $diplomePath = url('admin/public/medicaments/' . $name);

            $medicament->medicament_picture = $diplomePath;
        }

        // Gestion notice
        if ($request->file('notice')) {

            // 🔥 Supprimer ancienne image
            if ($medicament->notice) {

                // récupérer le chemin du fichier depuis l'URL
                $oldPath = public_path(parse_url($medicament->notice, PHP_URL_PATH));

                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file = $request->file('notice');
            $name = 'medicament_notice_' . $timestamp . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('notices'), $name);
            $diplomePath = url('admin/public/notices/' . $name);

            $medicament->notice = $diplomePath;
        }

        // Update champs
        $medicament->name = $request->name;
        $medicament->price = $request->price;
        $medicament->principe_actif = $request->principe;
        $medicament->code_cip = $request->code;

        $medicament->save();

        // ── Substituts : supprimer les anciens et réinsérer ───────────────
        MedicamantSubtituts::where('medicament_id', $id)->delete();

        if ($request->has('substituts') && !empty($request->substituts)) {
            $substituts = array_values(array_filter(
                $request->substituts,
                fn($sid) => (int) $sid !== (int) $id
            ));

            if (!empty($substituts)) {
                $rows = array_map(fn($sid) => [
                    'medicament_id' => (int) $id,
                    'substitut_id'  => (int) $sid,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ], $substituts);

                MedicamantSubtituts::insert($rows);
            }
        }

        return back()->with('succes',  "Mise à jour effectuée");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        Medicamants::findOrFail($id)->delete();

        return back()->with('succes',  "Suppression éffectuée");
    }

    public function showAllGet($id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $med = Medicamants::where('id_medicament', $id)
            ->select(
                'id_medicament as id',
                'name',
                'principe_actif as principeActif',
                'medicament_picture as medicamentPicture',
                'notice',
                'code_cip as codeCip',
                'price'
            )
            ->first();

        if (!$med) abort(404);

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
                'id'                      => $s->id,
                'substitutId'             => $s->substitutId,
                'substitutName'           => $s->substitutName ?? '',
                'substitutImageId'        => $s->substitutImageId ?? '',
                'substitutPrincipleActif' => $s->substitutPrincipleActif ?? '',
                'substitutPrice'          => $s->substitutPrice ?? '',
                'substitutNotice'         => $s->substitutNotice ?? '',
                'medicamentId'            => $s->medicamentId ?? 0,
            ])
            ->values();

        $medicaments = [
            'id'                => $med->id,
            'name'              => $med->name,
            'principeActif'     => $med->principeActif ?? '',
            'medicamentPicture' => $med->medicamentPicture ?? '',
            'notice'            => $med->notice ?? '',
            'codeCip'           => $med->codeCip,
            'price'             => $med->price ?? '',
            'substitutes'       => $substitutes,
        ];

        // ── Tous les médicaments sauf celui en cours ──────────────────────
        $tousLesMedicaments = Medicamants::where('id_medicament', '!=', $id)
            ->select('id_medicament', 'name')
            ->orderBy('name')
            ->get();

        return view('pharmacies.view-medicament', compact('medicaments', 'tousLesMedicaments'));
    }
}
