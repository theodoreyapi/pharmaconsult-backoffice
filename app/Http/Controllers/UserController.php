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
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        // SUBSCRIPTIONS
        // ======================
        $subscriptions = ProfileSubscription::where('user_id', $id)
            ->when($request->status_sub, function ($q) use ($request) {
                $q->where('status', $request->status_sub);
            })
            ->latest()
            ->paginate(5, ['*'], 'sub_page');

        // ======================
        // TRANSFERTS
        // ======================
        $transferts = Transfert::where('sender_username', $user->phone_number)
            ->orWhere('receiver_username', $user->phone_number)
            ->latest()
            ->paginate(5, ['*'], 'trans_page');

        // ======================
        // RECHARGEMENTS
        // ======================
        $rechargements = Rechargements::where('username', $user->phone_number)
            ->when($request->status_pay, function ($q) use ($request) {
                $q->where('status', $request->status_pay);
            })
            ->latest()
            ->paginate(5, ['*'], 'pay_page');

        // ======================
        // APPOINTMENTS
        // ======================
        $appointments = Appointment::where('user_id', $id)
            ->when($request->status_app, function ($q) use ($request) {
                $q->where('status', $request->status_app);
            })
            ->latest()
            ->paginate(5, ['*'], 'app_page');

        // ======================
        // HEALTH PROFILES
        // ======================
        $profiles = HealthProfile::where('user_id', $id)
            ->latest()
            ->paginate(5, ['*'], 'profile_page');

        // récupérer IDs des profils
        $profileIds = $profiles->pluck('id_profile');

        // vaccinations liées
        $vaccinations = ProfileVaccination::whereIn('profile_id', $profileIds)
            ->orderBy('vaccination_date', 'desc')
            ->get()
            ->groupBy('profile_id');

        // injecter dans chaque profil
        foreach ($profiles as $profile) {
            $profile->vaccinations_list = $vaccinations[$profile->id_profile] ?? [];
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
