<?php

use App\Http\Controllers\AbonnementController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssuranceController;
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
use App\Http\Controllers\UserPharmacienController;
use App\Models\Commune;
use App\Models\Medicamants;
use App\Models\Pharmacy;
use App\Models\PharmacyRequest;
use App\Models\Rechargements;
use App\Models\RequestMedicament;
use App\Models\ReservationMedicament;
use App\Models\Subscriptions;
use App\Models\Transfert;
use App\Models\UsersPharma;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('index', [CustomAuthController::class, 'dashboard'])->middleware('auth');;
Route::post('custom-login', [CustomAuthController::class, 'customLogin']);
Route::get('logout', [CustomAuthController::class, 'signOut'])->name('logout');

Route::get('/payment/wave/success/{id}', [PaymentWaveController::class, 'success'])
    ->name('wave.success');
Route::get('/payment/wave/error/{id}', [PaymentWaveController::class, 'error'])
    ->name('wave.error');

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

    // ── Statistiques générales ─────────────────────────────────────────
    $statistiques = [

        // Nombre total d'utilisateurs
        'totalUsers' => UsersPharma::count(),

        // Souscriptions totales (toutes confondues)
        'totalSubscriptions' => Subscriptions::count(),

        // Opérations totales (transferts)
        'totalOperations' => Transfert::count(),

        // Réservations totales
        'totalReservations' => ReservationMedicament::count(),

        // Requêtes totales (pharmacy_request)
        'totalRequests' => PharmacyRequest::count(),

        // Requêtes utilisateurs (request_medicament)
        'totalRequestsUsers' => RequestMedicament::count(),

        // Rechargements totaux (réussis)
        'totalRechargements' => Rechargements::where('status', 'success')
            ->count(),

        // Transferts totaux (uniquement DEBIT pour éviter le double comptage)
        'totalTransferts' => Transfert::where('type_operation', 'DEBIT')
            ->count(),

        // Nombre d'utilisateurs avec au moins une souscription active
        'totalActifSubscriptions' => Subscriptions::where('status', 'active')
            ->where('valid_until', '>', Carbon::now())
            ->distinct('username')
            ->count('username'),

        // Revenu total = somme des montants des rechargements réussis
        'totalSubscriptionAmount' => Rechargements::where('status', 'success')
            ->sum('montant'),
    ];

    // ── Statistiques des souscriptions par mois (année en cours) ──────
    // Regroupe les rechargements réussis par mois pour le graphique
    $souscriptionsParMois = Rechargements::selectRaw("DATE_FORMAT(created_at, '%m') as mois_num, DATE_FORMAT(created_at, '%b') as mois, SUM(montant) as cumulTotal")
        ->where('status', 'success')
        ->whereYear('created_at', $currentYear)
        ->groupByRaw("DATE_FORMAT(created_at, '%m'), DATE_FORMAT(created_at, '%b')")
        ->orderByRaw("DATE_FORMAT(created_at, '%m')")
        ->get()
        ->toArray();

    // Remplir les mois manquants avec 0 pour avoir les 12 mois
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

    $souscriptionsIndexed = collect($souscriptionsParMois)->keyBy('mois_num')->toArray();

    $souscriptions = [];
    foreach ($moisFr as $num => $label) {
        $souscriptions[] = [
            'mois'       => $label,
            'cumulTotal' => isset($souscriptionsIndexed[$num])
                ? (float) $souscriptionsIndexed[$num]->cumulTotal
                : 0,
        ];
    }

    return view('home.index', compact('statistiques', 'souscriptions'));
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
