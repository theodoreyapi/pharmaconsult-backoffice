<?php

namespace App\Http\Controllers;

use App\Auth\ApiUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CustomAuthController extends Controller
{
    public function index()
    {
        return view('auth.sign-in');
    }

    public function customLogin(Request $request)
    {

        $roles = [
            'email' => 'required',
            'password' => 'required',
        ];
        $customMessages = [
            'email.required' => "Veuillez saisir son adresse email.",
            'password.required' => "Veuillez saisir son mot de passe.",
        ];

        $request->validate($roles, $customMessages);

        $response = Http::withOptions([
            'verify' => false
        ])->post(env('API_BASE_URL') . '/auth/login', [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->status() == 200) {
            $data = $response->json();

            if ($data['user']['active'] === 'ACTIVE') {
                $user = new ApiUser($data['user']);

                // Manually create session to ensure it persists
                Auth::login($user); // Second parameter "true" for "remember me"

                // Store token in session
                session([
                    'api_token' => $data['token']['token'],
                    'user_data' => $data['user'] // Optional: Store raw user data
                ]);

                // Regenerate session ID for security
                $request->session()->regenerate();

                if (session('user_data')['role'] == 'ADMIN' || session('user_data')['role'] == 'SUPERADMIN') {
                    return redirect()->intended('index');
                } else if (session('user_data')['role'] == 'PHARMACIEN') {
                    return redirect()->intended('pharma-index');
                } else if (session('user_data')['role'] == 'GESTIONNAIRE') {
                    return redirect()->intended('requete');
                } else if (session('user_data')['role'] == 'CAISSIERE') {
                    return redirect()->intended('transactions');
                }else {
                    return back()->withErrors(['Rôle utilisateur non reconnu.']);
                }
            } else {
                return back()->withErrors(['Votre compte est désactivé. Veuillez contacter l\'administrateur.']);
            }
        } else {
            return back()->withErrors(['Erreur lors de l\'authentification.']);
        }
    }

    public function dashboard()
    {
        if (session('api_token')) {
            return view('home.index');
        } else {
            return view('auth.sign-in');
        }
    }

    public function signOut()
    {
        Session::flush();
        Auth::logout();
        return Redirect('/');
    }
}
