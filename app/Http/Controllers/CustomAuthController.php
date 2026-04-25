<?php

namespace App\Http\Controllers;

use App\Auth\ApiUser;
use App\Models\User;
use App\Models\UsersPharma;
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
            'email.required' => "Veuillez saisir votre adresse email.",
            'password.required' => "Veuillez saisir votre mot de passe.",
        ];

        $request->validate($roles, $customMessages);

        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        if ($user && password_verify($credentials['password'], $user->password)) {
            Auth::login($user);
            return redirect()->intended('index');
        } else {
            return back()->withErrors(['E-mail ou mot de passe incorrect.']);
        }
    }

    public function dashboard()
    {
        if (Auth::check()) {
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
