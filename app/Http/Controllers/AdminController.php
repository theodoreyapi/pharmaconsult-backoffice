<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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

        $user = new User();
        $user->name = $request->firstname;
        $user->email = $request->email;
        $user->password = password_hash($request->password, PASSWORD_BCRYPT, ['cost' => 10]);
        $user->last_name = $request->lastname;
        $user->phone = $request->phone;
        $user->role = $request->profil;
        if ($user->save()) {

            $this->sendEmail($request->email, $request->password, $request->firstname);

            return back()->with('succes',  "Ajoutée avec succès.");
        } else {
            return back()->withErrors(["Impossible d'ajouter. Veuillez réessayer!!"]);
        }
    }

    /**
     * Envoie les informations de connexion par Email via SMTP Gmail
     */
    private function sendEmail(string $email, string $password, string $firstName): void
    {
        Mail::send([], [], function ($message) use ($email, $password, $firstName) {
            $message
                ->to($email)
                ->from(
                    env('MAIL_FROM_ADDRESS', 'pharmaconsultsexpertise@gmail.com'),
                    env('MAIL_FROM_NAME', 'PharmaConsults')
                )
                ->subject('Vos paramètres de connexion - PharmaConsults')
                ->html("
                <div style='font-family: Helvetica, Arial, sans-serif; max-width: 500px; margin: 20px auto; padding: 25px; border: 1px solid #e0e0e0; border-radius: 12px; color: #333;'>
                    <div style='text-align: center; margin-bottom: 25px;'>
                        <h2 style='color: #115010; margin-bottom: 10px;'>Bonjour $firstName</h2>
                        <p style='color: #666; font-size: 15px;'>Utilisez les informations ci-dessous pour accéder à votre compte.</p>
                    </div>

                    <div style='background-color: #f4fbf4; padding: 25px; border-radius: 10px; border: 1px solid #41BA3E;'>
                        <div style='margin-bottom: 20px; text-align: center;'>
                            <span style='font-size: 12px; color: #666; text-transform: uppercase; font-weight: bold;'>Identifiant (E-mail)</span>
                            <div style='font-size: 18px; color: #115010; margin-top: 5px; font-weight: bold;'>$email</div>
                        </div>

                        <div style='text-align: center;'>
                            <span style='font-size: 12px; color: #666; text-transform: uppercase; font-weight: bold;'>Mot de passe temporaire</span>
                            <div style='font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #115010; margin-top: 10px; font-family: monospace;'>
                                $password
                            </div>
                        </div>
                    </div>

                    <div style='margin-top: 25px; text-align: center;'>
                        <p style='font-size: 14px;'>Ce code expire dans <strong style='color: #d9534f;'>2 minutes</strong>.</p>
                        <p style='color: #999; font-size: 12px; margin-top: 20px; line-height: 1.5;'>
                            Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet e-mail en toute sécurité.
                        </p>
                    </div>

                    <div style='border-top: 1px solid #eee; margin-top: 30px; padding-top: 20px; text-align: center; color: #bbb; font-size: 11px;'>
                        &copy; " . date('Y') . " PharmaConsults — Sécurité & Santé
                    </div>
                </div>
            ");
        });
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

        $user = User::findOrFail($id);
        $user->name = $request->firstname;
        $user->email = $request->email;

        if ($request->password != null && !password_verify($request->password, $user->password)) {
            $user->password = password_hash($request->password, PASSWORD_BCRYPT, ['cost' => 10]);
        }

        $user->last_name = $request->lastname;
        $user->phone = $request->phone;
        $user->role = $request->profil;
        $user->save();

        return back()->with('succes',  "Modification éffectuée ");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        User::findOrFail($id)->delete();

        return back()->with('succes',  "Suppression éffectuée ");
    }
}
