<?php

use App\Http\Controllers\Api\ApiAppointmentController;
use App\Http\Controllers\Api\ApiAssuranceController;
use App\Http\Controllers\Api\ApiCategoryController;
use App\Http\Controllers\Api\ApiCommuneController;
use App\Http\Controllers\Api\ApiHealthProfileController;
use App\Http\Controllers\Api\ApiMedicamentController;
use App\Http\Controllers\Api\ApiMesureController;
use App\Http\Controllers\Api\ApiNotificationController;
use App\Http\Controllers\Api\ApiParametreGenerauxController;
use App\Http\Controllers\Api\ApiPharmacyController;
use App\Http\Controllers\Api\ApiPharmacyRequestController;
use App\Http\Controllers\Api\ApiProfileSubscriptionController;
use App\Http\Controllers\Api\ApiProfileVaccinationController;
use App\Http\Controllers\Api\ApiPubliciteController;
use App\Http\Controllers\Api\ApiPushNotifController;
use App\Http\Controllers\Api\ApiReservationMedicamentController;
use App\Http\Controllers\Api\ApiReviewController;
use App\Http\Controllers\Api\ApiSubscriptionController;
use App\Http\Controllers\Api\ApiTraitementController;
use App\Http\Controllers\Api\ApiTransfertController;
use App\Http\Controllers\Api\ApiUsersPharmaController;
use App\Http\Controllers\Api\ApiVaccineController;
use App\Http\Controllers\Api\ApiWavePaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('internal/v1')->group(function () {

    // -----------------------
    // AUTHENTICATION
    // -----------------------
    Route::prefix('auth')->group(function () {
        Route::post('login', [ApiUsersPharmaController::class, 'login']);
        Route::post('generateToken', [ApiUsersPharmaController::class, 'generateToken']);
    });

    // -----------------------
    // USER
    // -----------------------
    Route::prefix('user')->group(function () {
        Route::post('save', [ApiUsersPharmaController::class, 'store']);
        Route::post('otp/validate', [ApiUsersPharmaController::class, 'validateOtp']);
        Route::post('otp/generate', [ApiUsersPharmaController::class, 'generateOtp']);
        Route::post('reinitialiser/password', [ApiUsersPharmaController::class, 'resetPassword']);
        Route::get('delete/{username}', [ApiUsersPharmaController::class, 'deleteAccount']);
        Route::get('delete/status/{username}', [ApiUsersPharmaController::class, 'deleteAccountStatus']);
        Route::put('update', [ApiUsersPharmaController::class, 'update']);
        Route::put('updateProfilePicture', [ApiUsersPharmaController::class, 'updatePicture']);
        Route::post('changePassword', [ApiUsersPharmaController::class, 'changePassword']);
        Route::get('getUserByUsername/{username}', [ApiUsersPharmaController::class, 'getByUsername']);
    });

    // -----------------------
    // PHARMACIES
    // -----------------------
    Route::prefix('pharma')->group(function () {
        Route::get('communes/search', [ApiCommuneController::class, 'getCommunes']);
        Route::get('communes', [ApiCommuneController::class, 'getCommune']);
        Route::get('pharmacies/gardeIntervalByCommune', [ApiPharmacyController::class, 'getByCommune']);
        Route::get('pharmacies/{id}/pharmacies', [ApiPharmacyController::class, 'getById']);
        Route::get('assurances/getAll', [ApiAssuranceController::class, 'getAssurance']);
    });

    // -----------------------
    // REVIEW
    // -----------------------
    Route::prefix('pharma/notices')->group(function () {
        Route::post('add', [ApiReviewController::class, 'addReview']);
        Route::get('get/{id}', [ApiReviewController::class, 'getByPharmacy']);
    });

    // -----------------------
    // MEDICAMENTS
    // -----------------------
    Route::prefix('pharma/medicaments')->group(function () {
        Route::get('search', [ApiMedicamentController::class, 'search']);
    });

    Route::prefix('requests-medicament')->group(function () {
        Route::post('/', [ApiPharmacyRequestController::class, 'store']);
        Route::get('user/{username}', [ApiPharmacyRequestController::class, 'getByUser']);
    });

    Route::prefix('reservations-medicament')->group(function () {
        Route::post('create', [ApiReservationMedicamentController::class, 'store']);
        Route::get('user/{username}', [ApiReservationMedicamentController::class, 'getByUser']);
    });

    Route::prefix('request-pharmacies')->group(function () {
        Route::get('request/{id}', [ApiPharmacyRequestController::class, 'show']);
    });

    // -----------------------
    // NOTIFICATIONS
    // -----------------------
    Route::prefix('notifications')->group(function () {
        Route::post('register', [ApiPushNotifController::class, 'register']);
    });

    // -----------------------
    // PUBLICITIES
    // -----------------------
    Route::prefix('publicites')->group(function () {
        Route::get('get/actives', [ApiPubliciteController::class, 'getActive']);
    });

    // -----------------------
    // TRANSACTIONS
    // -----------------------
    Route::prefix('pharma/operations')->group(function () {
        Route::get('byUsername/{username}', [ApiTransfertController::class, 'getTransactionsByUser']);
    });

    Route::prefix('pharma/transfers')->group(function () {
        Route::post('process', [ApiTransfertController::class, 'processTransfer']);
    });

    // -----------------------
    // MOBILE MONEY / PAYMENTS
    // -----------------------
    Route::prefix('pharma/cinetpay')->group(function () {
        Route::post('payment', [ApiWavePaymentController::class, 'initiatePayment']);
    });

    Route::prefix('pharma/wallet')->group(function () {
        Route::get('getWalletByUserName/{username}', [ApiWavePaymentController::class, 'getByUsername']);
    });

    // -----------------------
    // SUBSCRIPTIONS
    // -----------------------
    Route::prefix('pharma/subscriptions')->group(function () {
        Route::post('subscribe', [ApiSubscriptionController::class, 'subscribe']);
        Route::get('valid/{username}', [ApiSubscriptionController::class, 'checkAll']);
        Route::get('valid/module/{username}/{module}', [ApiSubscriptionController::class, 'checkByModule']);
    });

    // -----------------------
    // SUBSCRIPTIONS
    // -----------------------
    Route::prefix('pharma/forfaits')->group(function () {
        Route::get('byModuleName/{module}', [ApiSubscriptionController::class, 'getForfait']);
    });

    // -----------------------
    // GENERAL PARAMETERS
    // -----------------------
    Route::prefix('pharma/parametres-generaux')->group(function () {
        Route::get('getbyType/{type}', [ApiParametreGenerauxController::class, 'getByType']);
    });

    // -----------------------
    // GARDES
    // -----------------------
    Route::prefix('periodes-garde')->group(function () {
        Route::get('/', [ApiPharmacyController::class, 'getPeriodeGarde']);
    });

    // -----------------------
    // VACCINS
    // -----------------------
    Route::prefix('vaccins')->group(function () {
        Route::get('/', [ApiVaccineController::class, 'index']); // GET /api/vaccins?search=...&category=...
        Route::get('type/{type}', [ApiVaccineController::class, 'type']); // GET /api/vaccins/type/{type}

        // Catégories
        Route::get('categories', [ApiCategoryController::class, 'index']);   // GET /api/categories

        // Créer un RDV (sans auth obligatoire)
        Route::post('appointments', [ApiAppointmentController::class, 'store']); // POST /api/appointments

        // Rendez-vous du patient connecté
        Route::get('appointments/{id}', [ApiAppointmentController::class, 'index']);   // GET /api/appointments
    });

    // ── Profils santé ─────────────────────────────────────────────────────
    Route::prefix('health-profiles')->group(function () {

        // GET    /api/health-profiles              → liste des profils
        // POST   /api/health-profiles              → créer un profil + abonnement pending
        // GET    /api/health-profiles/{id}         → détail d'un profil
        // PUT    /api/health-profiles/{id}         → modifier un profil
        // DELETE /api/health-profiles/{id}         → désactiver un profil
        Route::get('/{id}', [ApiHealthProfileController::class, 'index']);
        Route::post('/', [ApiHealthProfileController::class, 'store']);
        Route::get('show/{id}', [ApiHealthProfileController::class, 'show']);
        Route::put('/{id}', [ApiHealthProfileController::class, 'update']);
        Route::delete('/{id}', [ApiHealthProfileController::class, 'destroy']);

        // GET /api/health-profiles/reminders       → rappels dans les 30 jours
        Route::get('/reminders', [ApiHealthProfileController::class, 'reminders']);
    });
    // ── Vaccinations (imbriquées dans un profil) ──────────────────────────

    // GET    /api/health-profiles/{profileId}/vaccinations         → liste
    // POST   /api/health-profiles/{profileId}/vaccinations         → ajouter (multipart)
    // GET    /api/health-profiles/{profileId}/vaccinations/{id}    → détail
    // POST   /api/health-profiles/{profileId}/vaccinations/{id}    → modifier (multipart + _method=PUT)
    // DELETE /api/health-profiles/{profileId}/vaccinations/{id}    → supprimer
    Route::prefix('health-profiles/{profileId}/vaccinations')->group(function () {
        Route::get('/',     [ApiProfileVaccinationController::class, 'index']);
        Route::post('/',    [ApiProfileVaccinationController::class, 'store']);
        Route::get('/{id}', [ApiProfileVaccinationController::class, 'show']);
        Route::post('/{id}', [ApiProfileVaccinationController::class, 'update']);   // POST + _method=PUT
        Route::delete('/{id}', [ApiProfileVaccinationController::class, 'destroy']);

        // DELETE /api/health-profiles/{profileId}/vaccinations/{id}/certificate
        Route::delete('/{id}/certificate', [ApiProfileVaccinationController::class, 'deleteCertificate']);
    });

    // ── Abonnements ───────────────────────────────────────────────────────

    // GET  /api/health-profiles/{profileId}/subscription       → abonnement courant
    // POST /api/health-profiles/{profileId}/subscription/pay   → confirmer paiement
    Route::prefix('health-profiles/{profileId}/subscription')->group(function () {
        Route::get('/',    [ApiProfileSubscriptionController::class, 'show']);
        Route::post('/pay', [ApiProfileSubscriptionController::class, 'pay']);
    });

    // GET /api/subscriptions/summary → résumé tous les abonnements
    Route::get('subscriptions/summary', [ApiProfileSubscriptionController::class, 'summary']);

    Route::prefix('patients/{patient}')->group(function () {
        Route::get('/dashboard-mesures',    [ApiMesureController::class, 'dashboardMesures']);
        Route::get('/bilan',    [ApiMesureController::class, 'generate']);
        Route::get('/rappel',    [ApiMesureController::class, 'latest']);
        Route::get('/traitements', [ApiTraitementController::class, 'index']);
        Route::get('/pharmacy', [ApiTraitementController::class, 'show']);
        Route::get('/notifications', [ApiNotificationController::class, 'notifications']);
    });

    Route::get('patients/cmu/{qrCode}', [ApiTraitementController::class, 'verify']);
});
