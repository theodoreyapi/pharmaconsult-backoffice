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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('index', [CustomAuthController::class, 'dashboard'])->middleware('auth');;
Route::post('custom-login', [CustomAuthController::class, 'customLogin']);
Route::get('logout', [CustomAuthController::class, 'signOut'])->name('logout');

Route::get('/', function () {
    if (session('api_token')) {
        return redirect()->intended('index');
    }
    return view('auth.sign-in');
});

Route::get('/proxy/pharmacies/{commune}', function ($commune) {
    $response = Http::withOptions([
        'verify' => false
    ])->get(env('API_BASE_URL_PHARMA') . '/pharma/pharmacies/getByCommune/' . $commune);

    return response()->json($response->json());
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

    if (!session('api_token')) {
        return redirect()->intended('logout');
    }

    if (session('user_data')['role'] != 'ADMIN' && session('user_data')['role'] != 'SUPERADMIN') {
        return back()->withErrors(["Vous n'êtes pas autorisé à accéder à cette page."]);
    }

    $responsestates = Http::withOptions([
        'verify' => false
    ])->withHeaders([
        'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->get(env('API_BASE_URL_PHARMA') . '/pharma/statistiques');

    $statistiques = $responsestates->json();


    $response = Http::withOptions([
        'verify' => false
    ])->withHeaders([
        'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->get(env('API_BASE_URL_PHARMA') . '/pharma/statistiques/subscription-amounts/year');

    $souscriptions = $response->json();

    return view('home.index', compact('statistiques', 'souscriptions'));
});
Route::get('pharma-index', function () {

    if (!session('api_token')) {
        return redirect()->intended('logout');
    }

    $responsestates = Http::withOptions([
        'verify' => false
    ])->withHeaders([
        'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->get(env('API_BASE_URL_PHARMA') . '/pharma/statistiques/pharmacy/' . session('user_data')['wallet']['pharmacyId'] . '/cumulative-money');

    $statistiques = $responsestates->json();

    $response = Http::withOptions([
        'verify' => false
    ])->withHeaders([
        'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->get(env('API_BASE_URL_PHARMA') . '/pharma/statistiques/monthly-stats/pharmacy?pharmacyId=' . session('user_data')['wallet']['pharmacyId']);

    $souscriptions = $response->json();

    $montant = Http::withOptions([
        'verify' => false
    ])->withHeaders([
        'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->get(env('API_BASE_URL') . '/pharma' . '/' . session('user_data')['wallet']['pharmacyId'] . '/wallet-balance');

    $solde = $montant->json();

    return view('home.pharma-index', compact('statistiques', 'souscriptions', 'solde'));
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
    $response = Http::withOptions([
        'verify' => false
    ])->withHeaders([
        'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->get(env('API_BASE_URL_PHARMA') . '/pharma/communes/search?page=0&size=1000');

    if ($response->status() == 200) {
        $communes = $response->json();

        return view('pharmacies.add-pharmacy', compact('communes'));
    } else {
        return back()->withErrors(["Impossible de charger les communes. Veuillez réessayer!!"]);
    }
});
Route::get('view-pharmacy', [PharmacieController::class, 'showAllGet'])->name('pharmacy.showAllGet');
Route::get('view-medicament', [PriceFicheController::class, 'showAllGet'])->name('medicament.showAllGet');

Route::get('add-medicament', function () {
    return view('pharmacies.add-medicament');
});

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
Route::resource('pharmacien', PharmacienController::class);
Route::post('profile', [PharmacienController::class, 'profile']);
Route::get('notification', function () {
    return view('layouts.master');
});
Route::get('notification-alert', function () {
    return view('layouts.master');
});
Route::get('payment-gateway', function () {
    return view('layouts.master');
});
Route::get('view-profile', function () {
    return view('users.profile');
});
Route::get('add-admin', function () {
    $response = Http::withOptions([
        'verify' => false
    ])->withHeaders([
        'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIwMDIyNTA1ODU4MzE2NDciLCJpc3MiOiJQQVRJRU5UIiwiaWF0IjoxNzQ3MDg0NzgzLCJleHAiOjE3NDcwODgzODN9.S0sMywcFkT8xnvqqCurUPkIEe_Os8m2iSnt8-h60mXk',
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
    ])->get(env('API_BASE_URL_PHARMA') . '/pharma/communes/search?page=0&size=1000');

    if ($response->status() == 200) {
        $communes = $response->json();

        return view('users.add-admin', compact('communes'));
    } else {
        return back()->withErrors(["Impossible de charger les communes. Veuillez réessayer!!"]);
    }
});
