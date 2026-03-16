<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Medicamants;
use App\Models\Pharmacy;
use App\Models\PharmacyRequest;
use App\Models\RequestMedicament;
use App\Models\ReservationMedicament;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiReservationMedicamentController extends Controller
{
    /**
     * POST /api/create
     * Body JSON : { "requestId": 1, "pharmacyId": 3, "medicamentId": 5, "userName": "002250585831647" }
     *
     * - Crée une réservation dans reservation_medicament (status = RESERVE)
     * - Met à jour le status de request_medicament à RESERVE
     * - Met à jour le status de pharmacy_request à RESERVE
     */
    public function store(Request $request)
    {
        $request->validate([
            'requestId'    => 'required|integer',
            'pharmacyId'   => 'required|integer',
            'medicamentId' => 'required|integer',
            'userName'     => 'required|string',
        ]);

        $requestId    = $request->input('requestId');
        $pharmacyId   = $request->input('pharmacyId');
        $medicamentId = $request->input('medicamentId');
        $userName     = $request->input('userName');

        // Vérifier que la pharmacie existe
        $pharmacy = Pharmacy::where('id_pharmacy', $pharmacyId)->first();
        if (!$pharmacy) {
            return response()->json(['message' => 'Pharmacie introuvable.'], 404);
        }

        // Vérifier que le médicament existe
        $medicament = Medicamants::where('id_medicament', $medicamentId)->first();
        if (!$medicament) {
            return response()->json(['message' => 'Médicament introuvable.'], 404);
        }

        DB::transaction(function () use ($requestId, $pharmacyId, $medicamentId, $userName) {
            $now            = now();
            $dateExpiration = now()->addHours(24);

            // 1. Créer la réservation
            ReservationMedicament::insert([
                'medicament_id'    => $medicamentId,
                'pharmacy_id'      => $pharmacyId,
                'user_name'        => $userName,
                'status'           => 'RESERVE',
                'date_reservation' => $now,
                'date_expiration'  => $dateExpiration,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            // 2. Mettre à jour le status de request_medicament
            RequestMedicament::where('id_request', $requestId)
                ->update([
                    'status'     => 'RESERVE',
                    'updated_at' => $now,
                ]);

            // 3. Mettre à jour le status de pharmacy_request
            PharmacyRequest::where('request_medicament_id', $requestId)
                ->where('pharmacy_id', $pharmacyId)
                ->update([
                    'status'     => 'RESERVE',
                    'updated_at' => $now,
                ]);
        });

        return response()->json(['message' => 'Réservation effectuée avec succès.'], 201);
    }


    /**
     * GET /api/reservations/user/{username}
     * Retourne la liste des réservations (reservation_medicament) d'un utilisateur
     */
    public function getByUser(string $username)
    {
        $reservations = ReservationMedicament::join('medicaments', 'reservation_medicament.medicament_id', '=', 'medicaments.id_medicament')
            ->join('pharmacy', 'reservation_medicament.pharmacy_id', '=', 'pharmacy.id_pharmacy')
            ->join('commune', 'pharmacy.commune_id', '=', 'commune.id_commune')
            ->where('reservation_medicament.user_name', $username)
            ->orderBy('reservation_medicament.date_reservation', 'desc')
            ->select(
                'reservation_medicament.id_reservation',
                'reservation_medicament.status',
                'reservation_medicament.user_name',
                'reservation_medicament.date_expiration',
                'reservation_medicament.date_reservation',
                // Médicament
                'medicaments.id_medicament',
                'medicaments.name as med_name',
                // Pharmacie
                'pharmacy.id_pharmacy',
                'pharmacy.name as pharmacy_name',
                'pharmacy.address as pharmacy_address',
                'pharmacy.gps_coordinates',
                // Commune
                'commune.id_commune',
                'commune.name as commune_name'
            )
            ->get();

        $result = $reservations->map(fn($r) => [
            'id'              => $r->id_reservation,
            'userName'        => $r->user_name,
            'status'          => $r->status,
            'expirationDate'  => $r->date_expiration,
            'dateReservation' => $r->date_reservation,
            'medicament'      => [
                'id'   => $r->id_medicament,
                'name' => $r->med_name ?? '',
            ],
            'pharmacy' => [
                'id'             => $r->id_pharmacy,
                'name'           => $r->pharmacy_name ?? '',
                'address'        => $r->pharmacy_address ?? '',
                'gpsCoordinates' => $r->gps_coordinates ?? '',
                'commune'        => [
                    'id'   => $r->id_commune,
                    'name' => $r->commune_name ?? '',
                ],
            ],
        ]);

        return response()->json($result, 200);
    }
}
