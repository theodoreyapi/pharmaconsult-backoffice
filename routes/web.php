<?php

use App\Http\Controllers\AbonnementController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssuranceController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\CommuneController;
use App\Http\Controllers\ConditionController;
use App\Http\Controllers\CustomAuthController;
use App\Http\Controllers\GardeController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\MentionController;
use App\Http\Controllers\MoyenPaieController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentWaveController;
use App\Http\Controllers\PharmacieController;
use App\Http\Controllers\PharmacienController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\PriceFicheController;
use App\Http\Controllers\PublicitesController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\RechargementController;
use App\Http\Controllers\ReponsesController;
use App\Http\Controllers\RequetesController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VaccinsController;
use App\Models\Appointment;
use App\Models\Commune;
use App\Models\HealthProfile;
use App\Models\Medicamants;
use App\Models\Pharmacy;
use App\Models\PharmacyRequest;
use App\Models\ProfileSubscription;
use App\Models\ProfileVaccination;
use App\Models\Rechargements;
use App\Models\RequestMedicament;
use App\Models\ReservationMedicament;
use App\Models\Subscriptions;
use App\Models\Transfert;
use App\Models\UsersPharma;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('index', [CustomAuthController::class, 'dashboard'])->middleware('auth');;
Route::post('custom-login', [CustomAuthController::class, 'customLogin']);
Route::get('logout', [CustomAuthController::class, 'signOut'])->name('logout');

// Wave rechargement
Route::get('/payment/wave/success/{id}', [PaymentWaveController::class, 'success'])
    ->name('wave.success');
Route::get('/payment/wave/error/{id}', [PaymentWaveController::class, 'error'])
    ->name('wave.error');

// Wave vaccination
Route::get('/payment/wave/success/profile/{id}', [PaymentWaveController::class, 'successVacci'])
    ->name('wave.success.profile');
Route::get('/payment/wave/error/profile/{id}', [PaymentWaveController::class, 'errorVacci'])
    ->name('wave.error.profile');

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->intended('index');
    }
    return view('auth.sign-in');
});

Route::get('/proxy/pharmacies/{commune}', function ($commune) {
    return Pharmacy::where('commune_id', $commune)->get();
});

// Authentification
Route::get('sign-up', function () {
    return view('auth.sign-up');
});
Route::get('sign-up', function () {
    return view('auth.sign-up');
});
Route::get('forgot', function () {
    return view('auth.forgot-password');
});

// tableau de bord
Route::get('index', function () {

    if (!Auth::check()) {
        return redirect()->intended('logout');
    }

    $currentYear = date('Y');

    // ── Statistiques générales ────────────────────────────────────────────────
    $statistiques = [

        // Utilisateurs
        'totalUsers' => UsersPharma::count(),

        // Souscriptions (table subscriptions existante)
        'totalSubscriptions' => Subscriptions::count(),

        // Opérations / transferts
        'totalOperations' => Transfert::count(),

        // Réservations
        'totalReservations' => ReservationMedicament::count(),

        // Requêtes pharmacies
        'totalRequests' => PharmacyRequest::count(),

        // Requêtes utilisateurs
        'totalRequestsUsers' => RequestMedicament::count(),

        // Rechargements réussis
        'totalRechargements' => Rechargements::where('status', 'success')->count(),

        // Transferts DEBIT uniquement
        'totalTransferts' => Transfert::where('type_operation', 'DEBIT')->count(),

        // Abonnements actifs (table subscriptions)
        'totalActifSubscriptions' => Subscriptions::where('status', 'active')
            ->where('valid_until', '>', Carbon::now())
            ->distinct('username')
            ->count('username'),

        // Revenu rechargements réussis
        'totalSubscriptionAmount' => Rechargements::where('status', 'success')
            ->sum('montant'),

        // ── Profils santé ─────────────────────────────────────────────────────

        // Nombre total de profils santé créés (actifs + désactivés)
        'totalHealthProfiles' => HealthProfile::count(),

        // Profils actifs uniquement
        'totalActiveHealthProfiles' => HealthProfile::where('is_active', true)->count(),

        // Abonnements profils — total
        'totalProfileSubscriptions' => ProfileSubscription::count(),

        // Abonnements profils — payés et non expirés
        'totalPaidProfileSubscriptions' => ProfileSubscription::where('status', 'paid')
            ->where('end_date', '>', Carbon::today())
            ->count(),

        // Abonnements profils — en attente
        'totalPendingProfileSubscriptions' => ProfileSubscription::where('status', 'pending')
            ->count(),

        // Revenu total des abonnements profils payés
        'totalProfileSubscriptionRevenue' => ProfileSubscription::where('status', 'paid')
            ->sum('amount'),

        // Revenu du mois courant (profils)
        'totalProfileRevenueThisMonth' => ProfileSubscription::where('status', 'paid')
            ->whereMonth('paid_at', date('m'))
            ->whereYear('paid_at', $currentYear)
            ->sum('amount'),

        // Vaccinations enregistrées
        'totalVaccinations' => ProfileVaccination::count(),

        // Réservations vaccins
        'totalVaccinAppointments' => Appointment::count(),

        // Réservations vaccins en attente
        'totalPendingVaccinAppointments' => Appointment::where('status', 'pending')
            ->count(),
    ];

    // ── Graphique rechargements par mois ──────────────────────────────────────
    $souscriptionsParMois = Rechargements::selectRaw(
        "DATE_FORMAT(created_at, '%m') as mois_num,
         DATE_FORMAT(created_at, '%b') as mois,
         SUM(montant) as cumulTotal"
    )
        ->where('status', 'success')
        ->whereYear('created_at', $currentYear)
        ->groupByRaw("DATE_FORMAT(created_at, '%m'), DATE_FORMAT(created_at, '%b')")
        ->orderByRaw("DATE_FORMAT(created_at, '%m')")
        ->get()
        ->toArray();

    // ── Graphique abonnements profils par mois ────────────────────────────────
    $profileSubsParMois = ProfileSubscription::selectRaw(
        "DATE_FORMAT(paid_at, '%m') as mois_num,
         DATE_FORMAT(paid_at, '%b') as mois,
         SUM(amount) as cumulTotal,
         COUNT(*) as nbr"
    )
        ->where('status', 'paid')
        ->whereYear('paid_at', $currentYear)
        ->groupByRaw("DATE_FORMAT(paid_at, '%m'), DATE_FORMAT(paid_at, '%b')")
        ->orderByRaw("DATE_FORMAT(paid_at, '%m')")
        ->get()
        ->toArray();

    // ── Remplissage des 12 mois ───────────────────────────────────────────────
    $moisFr = [
        '01' => 'Jan',
        '02' => 'Fév',
        '03' => 'Mar',
        '04' => 'Avr',
        '05' => 'Mai',
        '06' => 'Juin',
        '07' => 'Juil',
        '08' => 'Août',
        '09' => 'Sep',
        '10' => 'Oct',
        '11' => 'Nov',
        '12' => 'Déc',
    ];

    // Rechargements — 12 mois
    $souscriptionsIndexed = collect($souscriptionsParMois)->keyBy('mois_num')->toArray();
    $souscriptions = [];
    foreach ($moisFr as $num => $label) {
        $souscriptions[] = [
            'mois'       => $label,
            'cumulTotal' => isset($souscriptionsIndexed[$num])
                ? (float) $souscriptionsIndexed[$num]['cumulTotal']
                : 0,
        ];
    }

    // Abonnements profils — 12 mois
    $profileSubsIndexed = collect($profileSubsParMois)->keyBy('mois_num')->toArray();
    $profileSubscriptions = [];
    foreach ($moisFr as $num => $label) {
        $profileSubscriptions[] = [
            'mois'       => $label,
            'cumulTotal' => isset($profileSubsIndexed[$num])
                ? (float) $profileSubsIndexed[$num]['cumulTotal']
                : 0,
            'nbr'        => isset($profileSubsIndexed[$num])
                ? (int) $profileSubsIndexed[$num]['nbr']
                : 0,
        ];
    }

    return view('home.index', compact(
        'statistiques',
        'souscriptions',
        'profileSubscriptions'
    ));
});

// utilisateurs
Route::resource('users', UserController::class);
Route::get('view-users', [UserController::class, 'showUserGet'])->name('users.showUserGet');
Route::get('user-add', function () {
    return view('users.add-user');
});
Route::get('conditions', function () {
    return view('conditions.condition');
});

// Publicites
Route::resource('publicites', PublicitesController::class);

//{{ url()->previous() }}
// pharmacie
Route::resource('assurance', AssuranceController::class);
Route::resource('paiement', MoyenPaieController::class);
Route::resource('pharmacy', PharmacieController::class);
Route::get('/search-medicaments', [PriceFicheController::class, 'search'])->name('medicaments.search');
Route::resource('garde', GardeController::class);
Route::get('add-garde', [GardeController::class, 'getCommune']);
Route::post('add-garde', [GardeController::class, 'storeGarde']);
Route::resource('commune', CommuneController::class);
Route::resource('medicament', PriceFicheController::class);
Route::resource('requete', RequetesController::class);
Route::resource('reservation', ReservationsController::class);
Route::resource('transactions', TransactionController::class);
Route::resource('rechargement', RechargementController::class);
Route::resource('qrcode', QrCodeController::class);
Route::resource('reponse', ReponsesController::class);

Route::post('asso-assurance/{id}', [PharmacieController::class, 'assoAssurance']);
Route::post('asso-paiement/{id}', [PharmacieController::class, 'assoPaiement']);
Route::post('rechargement/init', [RechargementController::class, 'init'])->name('rechargement.init');

Route::post('/save-fcm-token', [NotificationController::class, 'storeToken']);

Route::get('add-pharmacy', function () {

    $communes = Commune::orderBy('name', 'ASC')->get();

    return view('pharmacies.add-pharmacy', compact('communes'));
});
Route::get('view-pharmacy/{id}', [PharmacieController::class, 'showAllGet'])->name('pharmacy.showAllGet');
Route::get('view-medicament/{id}', [PriceFicheController::class, 'showAllGet'])->name('medicament.showAllGet');

Route::get('add-medicament', function () {
    $medicaments = Medicamants::select('id_medicament', 'name')->orderBy('name')->get();
    return view('pharmacies.add-medicament', compact('medicaments'));
});

Route::get('/medicament/search', [PriceFicheController::class, 'searchh'])
    ->name('medicament.search');

Route::get('/pharmacy/search', [PriceFicheController::class, 'search'])->name('search.pharmacy');

// rapports
Route::get('transaction', function () {
    return view('layouts.master');
});
Route::get('abonnement', function () {
    return view('layouts.master');
});
Route::get('utilisateur', function () {
    return view('layouts.master');
});
Route::get('pharmacies', function () {
    return view('layouts.master');
});

// abonnement
Route::resource('pricing', AbonnementController::class);
Route::post('add-forfait/{id}', [AbonnementController::class, 'addForfait']);
Route::post('update-module/{id}', [AbonnementController::class, 'updateModule']);

// termes
Route::resource('terms-about', AboutController::class);
Route::resource('terms-politicy', PolicyController::class);
Route::resource('terms-mention', MentionController::class);
Route::resource('terms-aide', HelpController::class);
Route::resource('terms-condition', ConditionController::class);
Route::get('add-about', function () {
    return view('termes.about.add-about');
});
Route::get('add-politicy', function () {
    return view('termes.policy.add-politicy');
});
Route::get('add-mention', function () {
    return view('termes.mention.add-mention');
});
Route::get('add-aide', function () {
    return view('termes.help.add-aide');
});
Route::get('add-condition', function () {
    return view('termes.terms.add-termes');
});

// setting
Route::resource('company', AdminController::class);
Route::resource('user-pharma', PharmacienController::class);
Route::post('profile', [PharmacienController::class, 'profile']);

Route::get('add-admin', function () {

    $communes = Commune::orderBy('name', 'ASC')->get();

    return view('users.add-admin', compact('communes'));
});


// Vaccins
Route::resource('categories', CategorieController::class);
Route::resource('vaccins', VaccinsController::class);
Route::patch('/vaccins/{id}/toggle', [VaccinsController::class, 'toggle'])->name('vaccins.toggle');
