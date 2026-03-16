<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PharmacyRequest;
use App\Models\RequestMedicament;
use Illuminate\Http\Request;

class ApiPharmacyRequestController extends Controller
{

    /**
     * GET /api/request/{id}
     * Retourne la pharmacy_request avec la pharmacie associée en fonction du request_medicament_id
     */
    public function show(int $id)
    {
        $pharmacyRequest = PharmacyRequest::join('pharmacy', 'pharmacy_request.pharmacy_id', '=', 'pharmacy.id_pharmacy')
            ->join('commune', 'pharmacy.commune_id', '=', 'commune.id_commune')
            ->where('pharmacy_request.request_medicament_id', $id)
            ->select(
                'pharmacy_request.id_pharmacy_request',
                'pharmacy_request.status',
                'pharmacy_request.created_at',
                'pharmacy_request.updated_at',
                // Pharmacie
                'pharmacy.id_pharmacy',
                'pharmacy.name as pharmacy_name',
                'pharmacy.address',
                'pharmacy.opening_hours',
                'pharmacy.phone_number',
                'pharmacy.whats_app_phone_number',
                'pharmacy.owner_name',
                'pharmacy.facade_image',
                'pharmacy.gps_coordinates',
                'pharmacy.start_garde_date',
                'pharmacy.end_garde_date',
                // Commune
                'commune.id_commune',
                'commune.name as commune_name',
                'commune.description as commune_description'
            )
            ->first();

        if (!$pharmacyRequest) {
            return response()->json(['message' => 'Aucune pharmacie trouvée pour cette demande.'], 404);
        }

        return response()->json([
            'requestId'  => $pharmacyRequest->id_pharmacy_request,
            'status'     => $pharmacyRequest->status,
            'dateUpdate' => $pharmacyRequest->updated_at,
            'dateCreate' => $pharmacyRequest->created_at,
            'pharmacy'   => [
                'id'                  => $pharmacyRequest->id_pharmacy,
                'name'                => $pharmacyRequest->pharmacy_name ?? '',
                'address'             => $pharmacyRequest->address ?? '',
                'openingHours'        => $pharmacyRequest->opening_hours ?? '',
                'phoneNumber'         => $pharmacyRequest->phone_number ?? '',
                'whatsAppPhoneNumber' => $pharmacyRequest->whats_app_phone_number ?? '',
                'ownerName'           => $pharmacyRequest->owner_name ?? '',
                'facadeImage'         => $pharmacyRequest->facade_image ?? '',
                'gpsCoordinates'      => $pharmacyRequest->gps_coordinates ?? '',
                'startGardeDate'      => $pharmacyRequest->start_garde_date,
                'endGardeDate'        => $pharmacyRequest->end_garde_date,
                'commune'             => [
                    'id'          => $pharmacyRequest->id_commune,
                    'name'        => $pharmacyRequest->commune_name ?? '',
                    'description' => $pharmacyRequest->commune_description ?? '',
                ],
            ],
        ], 200);
    }


    /**
     * GET /api/user/{username}
     * Retourne la liste des réservations de médicaments d'un utilisateur
     */
    public function getByUser(string $username)
    {
        $requests = RequestMedicament::leftJoin('medicaments', 'request_medicament.medicament_id', '=', 'medicaments.id_medicament')
            ->leftJoin('users_pharma', 'request_medicament.username', '=', 'users_pharma.username')
            ->where('request_medicament.username', $username)
            ->orderBy('request_medicament.created_at', 'desc')
            ->select(
                'request_medicament.id_request',
                'request_medicament.status',
                'request_medicament.username',
                'request_medicament.comment',
                'request_medicament.medicament_name',
                'request_medicament.created_at',
                'request_medicament.updated_at',
                'users_pharma.first_name',
                'users_pharma.last_name',
                // Médicament
                'medicaments.id_medicament',
                'medicaments.name as med_name',
                'medicaments.principe_actif',
                'medicaments.medicament_picture',
                'medicaments.notice',
                'medicaments.code_cip',
                'medicaments.price'
            )
            ->get();

        $result = $requests->map(function ($req) {
            // Si medicament_id est null, on utilise medicament_name comme fallback
            $medicament = null;
            if ($req->id_medicament) {
                $medicament = [
                    'id'           => $req->id_medicament,
                    'name'         => $req->med_name ?? '',
                    'principeActif' => $req->principe_actif ?? '',
                    'imageId'      => $req->medicament_picture ?? '',
                    'notice'       => $req->notice ?? '',
                    'codeCip'      => $req->code_cip,
                    'price'        => $req->price ?? '',
                ];
            } elseif ($req->medicament_name) {
                $medicament = [
                    'id'           => null,
                    'name'         => $req->medicament_name,
                    'principeActif' => '',
                    'imageId'      => '',
                    'notice'       => '',
                    'codeCip'      => null,
                    'price'        => '',
                ];
            }

            return [
                'requestId'        => $req->id_request,
                'medicament'       => $medicament,
                'userName'         => $req->username,
                'firstAndLastName' => trim(($req->first_name ?? '') . ' ' . ($req->last_name ?? '')),
                'status'           => $req->status,
                'dateUpdate'       => $req->updated_at,
                'dateCreate'       => $req->created_at,
            ];
        });

        return response()->json($result, 200);
    }
}
