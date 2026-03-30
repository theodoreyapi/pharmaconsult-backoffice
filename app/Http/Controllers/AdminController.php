<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $admins = User::all();

        return view('users.admin', compact('admins'));
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
            'phone' => '',
            'email' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'profil' => 'required',
        ];
        $customMessages = [
            'email.required' => "Veuillez saisir l'adresse email de l'utilisateur'.",
            'firstname.required' => "Veuillez saisir le nom de l'utilisateur.",
            'lastname.required' => "Veuillez saisir le prénom de l'utilisateur.",
            'profil.required' => "Veuillez sélectionner le profil de l'utilisateur.",
        ];

        $request->validate($roles, $customMessages);

        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post(env('API_BASE_URL') . '/user/createUser', [
            'username' => $request->email,
            'email' => $request->email,
            'phoneNumber' => $request->phone,
            'firstName' => $request->firstname,
            'lastName' => $request->lastname,
            'typeUser' => $request->profil,
            'createdBy' => session('user_data')['lastName'] . ' ' . session('user_data')['firstName'],
            'pharmacyId' => $request->pharmacys[0] ?? 0,
        ]);

        if ($response->status() == 201) {
            return back()->with('succes',  "Vous avez ajouter " . $request->firstname);
        } else {
            return back()->withErrors(["Impossible d'ajouter " . $request->firstname . ". Veuillez réessayer!!"]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

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
            'email' => 'required',
            'firstname' => 'required',
            'lastname' => 'required',
            'profil' => 'required',
        ];
        $customMessages = [
            'email.required' => "Veuillez saisir l'adresse email de l'utilisateur'.",
            'firstname.required' => "Veuillez saisir le nom de l'utilisateur.",
            'lastname.required' => "Veuillez saisir le prénom de l'utilisateur.",
            'profil.required' => "Veuillez sélectionner le profil de l'utilisateur.",
        ];

        $request->validate($roles, $customMessages);

        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->put(env('API_BASE_URL') . '/user/updateUser', [
            'username' => $request->email,
            'mobilePhone' => $request->phone,
            'firstName' => $request->firstname,
            'lastName' => $request->lastname,
            'role' => $request->profil,
            'pharmacyId' => $request->pharmacys[0] ?? 0,
        ]);

        if ($response->status() == 200) {
            return back()->with('succes',  "Modification effectuée ");
        } else {
            return back()->withErrors(["Impossible de modifier. Veuillez réessayer!!"]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }
        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->delete(env('API_BASE_URL') . '/user/deleteUser/' . $id);
        // dd($response->status() . ' ' . $response->body());
        if ($response->status() == 200) {
            return back()->with('succes',  "Suppression éffectuée ");
        } else {
            return back()->withErrors(["Impossible de supprimer. Veuillez réessayer!!"]);
        }
    }
}
