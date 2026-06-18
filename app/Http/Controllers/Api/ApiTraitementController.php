<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ApiTraitementController extends Controller
{
    public function index($patientId)
    {
        // 1. PHARMACY PHONE
        $patient = DB::table('patients')
            ->where('id_patient', $patientId)
            ->first();

        $pharmacyPhone = DB::table('pharmacy')
            ->where('id_pharmacy', $patient->pharmacy_id ?? null)
            ->value('phone_number');

        // 2. TRAITEMENTS + PATHOLOGIE (JOIN SQL)
        $traitements = DB::table('traitements')
            ->leftJoin(
                'pathologies',
                'pathologies.id_pathologie',
                '=',
                'traitements.pathologie_id'
            )
            ->where('traitements.patient_id', $patientId)
            ->where('traitements.status', 'ACTIF')
            ->orderByDesc('traitements.dispensed_at')
            ->select(
                'traitements.*',
                'pathologies.name as pathologie_name'
            )
            ->get();

        // 3. GROUP BY PATHOLOGIE (PHP)
        $groupes = $traitements->groupBy('pathologie_name');

        // 4. FORMAT RESPONSE
        $result = $groupes->map(function ($items, $pathologie) {

            return [
                'pathologie' => $pathologie ?? 'Autres',

                'medicaments' => $items->map(function ($t) {

                    $today = Carbon::now();
                    $startDate = Carbon::parse($t->dispensed_at);

                    $endDate = $t->estimated_end_date
                        ? Carbon::parse($t->estimated_end_date)
                        : $startDate->copy()->addDays($t->duration_days);

                    $daysRemaining = $today->diffInDays($endDate, false);

                    // status logique simple
                    if ($daysRemaining < 0) {
                        $status = ['label' => 'En retard', 'color' => 'E74C3C'];
                    } elseif ($daysRemaining <= 7) {
                        $status = ['label' => 'Bientôt fini', 'color' => 'F39C12'];
                    } else {
                        $status = ['label' => 'À jour', 'color' => '27AE60'];
                    }

                    $total = $startDate->diffInDays($endDate);
                    $used = $startDate->diffInDays(now());

                    $progress = $total > 0
                        ? min(round($used / $total, 2), 1)
                        : 1;

                    return [
                        'id' => $t->id_traitement,
                        'name' => $t->medication_name,
                        'dosage' => $t->dose_per_take,
                        'frequency_per_day' => $t->frequency_per_day,
                        'quantity_delivered' => $t->quantity_delivered,

                        'dispensed_at' => $startDate->format('Y-m-d'),
                        'estimated_end_date' => $endDate->format('Y-m-d'),

                        'days_remaining' => max($daysRemaining, 0),
                        'delay_days' => $daysRemaining < 0 ? abs($daysRemaining) : 0,

                        'status' => $status['label'],
                        'status_color' => $status['color'],

                        'progress' => $progress,
                    ];
                })->values(),
            ];
        })->values();

        return response()->json([
            'pharmacy_phone' => $pharmacyPhone,
            'total_traitements' => $traitements->count(),
            'traitements' => $result,
        ]);
    }

    public function show($patientId)
    {
        // 1. patient
        $patient = DB::table('patients')
            ->where('id_patient', $patientId)
            ->first();

        if (!$patient) {
            return response()->json(['message' => 'Patient introuvable'], 404);
        }

        // 2. pharmacy
        $pharmacy = DB::table('pharmacy')
            ->where('id_pharmacy', $patient->pharmacy_id)
            ->first();

        if (!$pharmacy) {
            return response()->json(['message' => 'Pharmacie introuvable'], 404);
        }

        // 3. rating stats (IMPORTANT)
        $ratingData = DB::table('review')
            ->where('pharmacy_id', $pharmacy->id_pharmacy)
            ->selectRaw('
                COUNT(*) as total_reviews,
                AVG(evaluation) as average_rating
            ')
            ->first();

        $averageRating = round($ratingData->average_rating ?? 0, 1);
        $totalReviews = $ratingData->total_reviews ?? 0;

        // 4. google maps
        $googleMapsUrl = !empty($pharmacy->gps_coordinates)
            ? "https://www.google.com/maps?q=" . urlencode($pharmacy->gps_coordinates)
            : null;

        return response()->json([
            'id' => $pharmacy->id_pharmacy,
            'name' => $pharmacy->name ?? 'Ma pharmacie',
            'owner_name' => $pharmacy->owner_name,

            // ⭐ RATING RÉEL
            'rating' => $averageRating,
            'reviews_count' => $totalReviews,

            // CONTACT
            'phone' => $pharmacy->phone_number,
            'whatsapp' => $pharmacy->whats_app_phone_number,

            // ADDRESS
            'address' => $pharmacy->address,

            // HOURS
            'opening_hours' => $pharmacy->opening_hours,
            'closing_hours' => $pharmacy->closing_hours,

            // LOCATION
            'gps_coordinates' => $pharmacy->gps_coordinates,
            'google_maps_url' => $googleMapsUrl,

            // STATUS
            'is_active' => (bool) $pharmacy->is_active,

            // UI BADGE
            'badge' => $pharmacy->is_active
                ? 'Pharmacie principale'
                : 'Pharmacie secondaire',
        ]);
    }

    public function verify($qrCode)
    {
        // 1. chercher patient par QR CODE (CMU)
        $patient = DB::table('patients')
            ->where('qr_code', $qrCode)
            ->first();

        // 2. si pas trouvé
        if (!$patient) {
            return response()->json([
                'status' => false,
                'message' => 'Patient introuvable'
            ], 404);
        }

        // 6. response
        return response()->json([
            'id' => $patient->id_patient,
            'first_name' => $patient->first_name,
            'last_name' => $patient->last_name,
            'phone_number' => $patient->phone_number,
            'cmu' => $patient->qr_code,
            'gender' => $patient->gender,
            'birth_date' => $patient->birth_date,
            'city' => $patient->city,
            'pharmacieId' => $patient->pharmacy_id,
            'status' => $patient->status,
            'active' => (bool) $patient->active,
        ]);
    }
}
