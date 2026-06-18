<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiAppointmentController extends Controller
{
    /**
     * GET /api/appointments
     * Liste les rendez-vous (admin: tous | patient: par téléphone).
     *
     * Query params:
     *   - phone    : string — filtrer par numéro de téléphone patient
     *   - status   : string — pending|confirmed|cancelled|completed
     *   - date     : string — YYYY-MM-DD
     *   - per_page : int
     */
    public function index($id)
    {
        $appointments = Appointment::leftJoin('vaccines', 'appointments.vaccine_id', '=', 'vaccines.id_vaccine')
            ->leftJoin('pharmacy', 'appointments.pharmacy_id', '=', 'pharmacy.id_pharmacy')

            ->select(
                'appointments.id_appointment',
                'appointments.reference',
                'appointments.patient_name',
                'appointments.patient_phone',
                'appointments.patient_email',
                'appointments.appointment_date',
                'appointments.status',
                'appointments.notes',
                'appointments.created_at',
                'appointments.confirmed_at',
                'appointments.cancelled_at',
                'appointments.vaccine_id',

                'vaccines.id_vaccine',
                'vaccines.name as vaccine_name',
                'vaccines.short_name',
                'vaccines.public_price',
                'vaccines.description',
                'vaccines.important_info',
                'vaccines.currency',

                'pharmacy.id_pharmacy',
                'pharmacy.name as pharmacy_name',
                'pharmacy.address',
                'pharmacy.phone_number',
                'pharmacy.whats_app_phone_number',
                'pharmacy.opening_hours',
                'pharmacy.closing_hours'
            )

            ->where('appointments.user_id', $id)
            ->orderBy('appointments.created_at', 'DESC')
            ->get();

        // Charger les équivalents pour chaque vaccin concerné
        $vaccineIds = $appointments->pluck('vaccine_id')->filter()->unique();

        $equivalents = DB::table('vaccine_equivalents')
            ->whereIn('vaccine_id', $vaccineIds)
            ->where('is_active', true)
            ->get()
            ->groupBy('vaccine_id');

        $appointments->transform(function ($appointment) use ($equivalents) {
            $appointment->equivalents = $equivalents->get($appointment->vaccine_id, collect())->values();
            return $appointment;
        });

        return response()->json($appointments);
    }

    /**
     * POST /api/appointments
     * Créer un nouveau rendez-vous (réservation).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vaccine_id'       => 'required|exists:vaccines,id_vaccine',
            'pharmacy_id'      => 'required|exists:pharmacy,id_pharmacy',
            'patient_name'     => 'nullable|string|max:150',
            'patient_phone'    => 'required|string|max:20',
            'patient_email'    => 'nullable|email|max:150',
            'appointment_date' => 'required|date|after_or_equal:today',
            'notes'            => 'nullable|string|max:500',
        ]);

        // Génération référence unique
        $lastAppointment = Appointment::latest('id_appointment')->first();

        $nextId = $lastAppointment
            ? $lastAppointment->id_appointment + 1
            : 1;

        $reference = 'RDV-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // Insertion
        Appointment::insertGetId([
            'reference'        => $reference,
            'vaccine_id'       => $validated['vaccine_id'],
            'pharmacy_id'      => $validated['pharmacy_id'],
            'user_id'          => $request->id_user,

            'patient_name'     => $validated['patient_name'] ?? '',
            'patient_phone'    => $validated['patient_phone'],
            'patient_email'    => $validated['patient_email'] ?? '',

            'appointment_date' => Carbon::createFromFormat(
                'd/m/Y',
                $validated['appointment_date']
            )->format('Y-m-d'),
            'notes'            => $validated['notes'] ?? '',

            'status'           => 'pending',

            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Réservation créée avec succès',
        ], 201);
    }
}
