<?php

namespace App\Http\Controllers;

use App\Exports\UsersPharmaExport;
use App\Models\Appointment;
use App\Models\HealthProfile;
use App\Models\ProfileSubscription;
use App\Models\ProfileVaccination;
use App\Models\Rechargements;
use App\Models\Transfert;
use App\Models\UsersPharma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $query = UsersPharma::query();

        // 🔍 Search (nom, email, téléphone)
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('phone_number', 'like', "%$search%");
            });
        }

        // 📌 Statut
        if ($request->filled('status')) {
            $query->where('active', $request->status);
        }

        // 💰 Solde min
        if ($request->filled('min')) {
            $query->where('amount', '>=', $request->min);
        }

        // 💰 Solde max
        if ($request->filled('max')) {
            $query->where('amount', '<=', $request->max);
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(10);

        // garder les filtres dans pagination
        $patients->appends($request->all());

        return view('users.users-list', compact('patients'));
    }


    public function export()
    {
        return Excel::download(new UsersPharmaExport, 'users_pharma.xlsx');
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
    public function show(Request $request, string $id)
    {
        if (!Auth::check()) {
            return redirect()->intended('logout');
        }

        $user = UsersPharma::where('id_user', $id)->firstOrFail();

        // ======================
        // SUBSCRIPTIONS (Correction du point et sécurisation du type String)
        // ======================
        $subscriptions = DB::table('subscriptions')
            ->join('modules', 'subscriptions.module_id', '=', 'modules.id_module') // Correction ici -> un seul point !
            ->select('subscriptions.*', 'modules.libelle as module_libelle')
            ->where('subscriptions.username', '=', (string) $user->phone_number) // Cast en string pour la sécurité
            ->when($request->status_sub, function ($q) use ($request) {
                $q->where('subscriptions.status', $request->status_sub);
            })
            ->latest('subscriptions.created_at')
            ->paginate(5, ['*'], 'sub_page');

        // ======================
        // TRANSFERTS
        // ======================
        $transferts = DB::table('transfert')
            ->where('sender_username', $user->phone_number)
            ->orWhere('receiver_username', $user->phone_number)
            ->latest()
            ->paginate(5, ['*'], 'trans_page');

        // ======================
        // RECHARGEMENTS
        // ======================
        $rechargements = DB::table('rechargements')
            ->where('username', $user->phone_number)
            ->when($request->status_pay, function ($q) use ($request) {
                $q->where('status', $request->status_pay);
            })
            ->latest()
            ->paginate(5, ['*'], 'pay_page');

        // ======================
        // APPOINTMENTS (Jointures pour obtenir le nom du vaccin et de la pharmacie)
        // ======================
        $appointments = DB::table('appointments')
            ->leftJoin('vaccines', 'appointments.vaccine_id', '=', 'vaccines.id_vaccine')
            ->leftJoin('pharmacy', 'appointments.pharmacy_id', '=', 'pharmacy.id_pharmacy')
            ->select('appointments.*', 'vaccines.name as vaccine_name', 'pharmacy.name as pharmacy_name')
            ->where('appointments.user_id', $id)
            ->when($request->status_app, function ($q) use ($request) {
                $q->where('appointments.status', $request->status_app);
            })
            ->latest('appointments.created_at')
            ->paginate(5, ['*'], 'app_page');

        // ======================
        // HEALTH PROFILES & VACCINATIONS
        // ======================
        $profiles = DB::table('health_profiles')
            ->where('user_id', $id)
            ->latest()
            ->paginate(5, ['*'], 'profile_page');

        // Récupération des rendez-vous de vaccination validés/terminés pour ce patient
        // On fait une jointure pour récupérer le nom du vaccin lié au rendez-vous
        $vaccinations = DB::table('appointments')
            ->join('vaccines', 'appointments.vaccine_id', '=', 'vaccines.id_vaccine')
            ->select('appointments.*', 'vaccines.name as vaccine_name_free')
            ->where('appointments.user_id', $id)
            ->where('appointments.status', 'completed') // Uniquement les vaccins réellement administrés
            ->orderBy('appointments.appointment_date', 'desc')
            ->get()
            ->groupBy('user_id'); // Groupé par l'utilisateur principal

        foreach ($profiles as $profile) {
            // Comme les rendez-vous sont liés à l'user_id global dans votre schéma actuel,
            // on associe la liste des vaccins de l'utilisateur au profil.
            $profile->vaccinations_list = $vaccinations[$id] ?? collect([]);
        }

        return view('users.view-profile', compact(
            'user',
            'subscriptions',
            'transferts',
            'rechargements',
            'appointments',
            'profiles'
        ));
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
