<?php

namespace App\Http\Controllers;

use App\Models\Modules;
use App\Models\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AbonnementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $publicites = Modules::all();

        return view('pricing.pricing', compact('publicites'));
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
            'description' => 'required',
            'libelle' => 'required',
        ];
        $customMessages = [
            'description.required' => "Veuillez saisir la description.",
            'libelle.required' => "Veuillez saisir le libelle.",
        ];

        $request->validate($roles, $customMessages);

        $module = new Modules();
        $module->description = $request->description;
        $module->libelle = $request->libelle;
        if ($module->save()) {
            return back()->with('succes',  "Vous avez ajouter " . $request->libelle);
        } else {
            return back()->withErrors(["Impossible d'ajouter " . $request->libelle . ". Veuillez réessayer!!"]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $publicites = Services::where('module_id', '=', $id)->get();
        $libelle = Modules::where('id_module', '=', $id)->first('libelle');

        return view('pricing.view-pricing', compact('publicites', 'libelle', 'id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Add forfait the specified resource in storage.
     */
    public function updateModule(Request $request, string $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }
        $roles = [
            'description' => 'required',
            'libelle' => 'required',
        ];
        $customMessages = [
            'description.required' => "Veuillez saisir la description.",
            'libelle.required' => "Veuillez saisir le libelle.",
        ];

        $request->validate($roles, $customMessages);

        $module = Modules::findOrFail($id);

        // Update champs
        $module->description = $request->description;
        $module->libelle = $request->libelle;
        if ($module->save()) {
            return back()->with('succes',  "Modification éffectuée");
        } else {
            return back()->withErrors(["Impossible de modifier. Veuillez réessayer!!"]);
        }
    }

    /**
     * Add forfait the specified resource in storage.
     */
    public function addForfait(Request $request, string $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }
        $roles = [
            'description' => 'required',
            'libelle' => 'required',
            'prix' => 'required',
            'duration' => 'required',
        ];
        $customMessages = [
            'description.required' => "Veuillez saisir la description.",
            'libelle.required' => "Veuillez saisir le libelle.",
            'prix.required' => "Veuillez saisir le prix.",
            'duration.required' => "Veuillez saisir la duree.",
        ];

        $request->validate($roles, $customMessages);

        $services = new Services();
        $services->description = $request->description;
        $services->duration = $request->duration;
        $services->libelle = $request->libelle;
        $services->price = $request->prix;
        $services->module_id = $id;
        if ($services->save()) {
            return back()->with('succes',  "Vous avez ajouter " . $request->libelle);
        } else {
            return back()->withErrors(["Impossible d'ajouter " . $request->libelle . ". Veuillez réessayer!!"]);
        }
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
            'description' => 'required',
            'libelle' => 'required',
            'prix' => 'required',
            'duration' => 'required',
        ];
        $customMessages = [
            'description.required' => "Veuillez saisir la description.",
            'libelle.required' => "Veuillez saisir le libelle.",
            'prix.required' => "Veuillez saisir le prix.",
            'duration.required' => "Veuillez saisir la duree.",
        ];

        $request->validate($roles, $customMessages);

        $services = Services::findOrFail($id);

        // Update champs
        $services->description = $request->description;
        $services->duration = $request->duration;
        $services->libelle = $request->libelle;
        $services->price = $request->prix;

        if ($services->save()) {
            return back()->with('succes',  "Modification éffectuée ");
        } else {
            return back()->withErrors(["Impossible de modifier. Veuillez réessayer!!"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
