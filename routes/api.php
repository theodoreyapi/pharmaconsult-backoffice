<?php

use App\Http\Controllers\Api\ApiAssuranceController;
use App\Http\Controllers\Api\ApiCommuneController;
use App\Http\Controllers\Api\ApiMedicamentController;
use App\Http\Controllers\Api\ApiParametreGenerauxController;
use App\Http\Controllers\Api\ApiPharmacyController;
use App\Http\Controllers\Api\ApiPharmacyRequestController;
use App\Http\Controllers\Api\ApiPubliciteController;
use App\Http\Controllers\Api\ApiPushNotifController;
use App\Http\Controllers\Api\ApiReservationMedicamentController;
use App\Http\Controllers\Api\ApiReviewController;
use App\Http\Controllers\Api\ApiSubscriptionController;
use App\Http\Controllers\Api\ApiTransfertController;
use App\Http\Controllers\Api\ApiUsersPharmaController;
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
        Route::delete('delete/{username}', [ApiUsersPharmaController::class, 'deleteAccount']);
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
});
