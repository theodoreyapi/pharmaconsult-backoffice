<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!session('api_token')) {
            return redirect()->intended('logout');
        }

        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->get(env('API_BASE_URL') . '/user/getAll');

        if ($response->status() == 200) {
            $utilisateurs = collect($response->json()['content']);

            // On filtre seulement les patients
            $patients = $utilisateurs->where('role', '=', 'PATIENT')->values();

            return view('users.users-list', compact('patients'));
        } else {
            // Gérer l'erreur
            return abort(500, 'Erreur lors du chargement des données.');
        }
    }

    public function showUserGet(Request $request)
    {
        if (!session('api_token')) {
            return redirect()->intended('logout');
        }
        $data = $request->query('data');

        // Décodage des données JSON
        $users = json_decode(urldecode($data), true);

        return view('users.view-profile', compact('users'));
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
