<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Services;
use App\Models\Subscriptions;
use App\Models\UsersPharma;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiSubscriptionController extends Controller
{

    /**
     * GET /api/byModuleName/{module}
     * Retourne la liste des services d'un module par son libelle
     */
    public function getForfait(string $module)
    {
        $services = Services::join('modules', 'services.module_id', '=', 'modules.id_module')
            ->where('modules.libelle', $module)
            ->select(
                'services.id_service as id',
                'services.libelle',
                'services.description',
                'services.duration',
                'services.price',
                'services.created_at as dateCreate',
                'services.updated_at as dateUpdate',
                'modules.id_module',
                'modules.libelle as module_libelle',
                'modules.description as module_description',
                'modules.created_at as module_dateCreate',
                'modules.updated_at as module_dateUpdate'
            )
            ->orderBy('services.price', 'asc')
            ->get();

        if ($services->isEmpty()) {
            return response()->json([], 200);
        }

        $result = $services->map(function ($service) {
            return [
                'id'          => $service->id,
                'libelle'     => $service->libelle,
                'description' => $service->description,
                'duration'    => $service->duration,
                'price'       => $service->price,
                'dateCreate'  => $service->dateCreate,
                'dateUpdate'  => $service->dateUpdate,
                'moduleDto'   => [
                    'id'          => $service->id_module,
                    'libelle'     => $service->module_libelle,
                    'description' => $service->module_description,
                    'dateCreate'  => $service->module_dateCreate,
                    'dateUpdate'  => $service->module_dateUpdate,
                ],
            ];
        });

        return response()->json($result, 200);
    }


    /**
     * GET /api/valid/{username}
     * Retourne toutes les souscriptions actives et non expirées de l'utilisateur
     */
    public function checkAll(string $username)
    {
        $now = Carbon::now();

        $subscriptions = Subscriptions::join('modules', 'subscriptions.module_id', '=', 'modules.id_module')
            ->where('subscriptions.username', $username)
            ->where('subscriptions.status', 'active')
            ->where('subscriptions.valid_until', '>', $now)
            ->select(
                'subscriptions.id_subscription as id',
                'subscriptions.description',
                'subscriptions.duree',
                'subscriptions.status',
                'subscriptions.username as userName',
                'subscriptions.valid_until as validUntil',
                'subscriptions.created_at as dateCreate',
                'subscriptions.updated_at as dateUpdate',
                'modules.id_module',
                'modules.libelle as module_libelle',
                'modules.description as module_description',
                'modules.created_at as module_dateCreate',
                'modules.updated_at as module_dateUpdate'
            )
            ->orderBy('subscriptions.valid_until', 'desc')
            ->get();

        $result = $subscriptions->map(function ($sub) {
            return [
                'id'          => $sub->id,
                'moduleDto'   => [
                    'id'          => $sub->id_module,
                    'libelle'     => $sub->module_libelle,
                    'description' => $sub->module_description,
                    'dateCreate'  => $sub->module_dateCreate,
                    'dateUpdate'  => $sub->module_dateUpdate,
                ],
                'duree'       => $sub->duree,
                'dateCreate'  => $sub->dateCreate,
                'dateUpdate'  => $sub->dateUpdate,
                'validUntil'  => $sub->validUntil,
                'description' => $sub->description,
                'status'      => $sub->status,
                'userName'    => $sub->userName,
            ];
        });

        return response()->json($result, 200);
    }


    /**
     * GET /api/valid/module/{username}/{module}
     * Vérifie si l'utilisateur a une souscription active pour un module donné
     *
     * @param string $username  — ex: 002250585831647
     * @param string $module    — ex: "Fiche et prix", "Assurances", "Recherche medicament"
     */
    public function checkByModule(string $username, string $module)
    {
        $now = Carbon::now();

        $subscription = Subscriptions::join('modules', 'subscriptions.module_id', '=', 'modules.id_module')
            ->where('subscriptions.username', $username)
            ->where('modules.libelle', $module)
            ->where('subscriptions.status', 'active')
            ->where('subscriptions.valid_until', '>', $now)
            ->select(
                'subscriptions.id_subscription as id',
                'subscriptions.description',
                'subscriptions.duree',
                'subscriptions.status',
                'subscriptions.username as userName',
                'subscriptions.valid_until as validUntil',
                'subscriptions.created_at as dateCreate',
                'subscriptions.updated_at as dateUpdate',
                'modules.id_module',
                'modules.libelle as module_libelle',
                'modules.description as module_description',
                'modules.created_at as module_dateCreate',
                'modules.updated_at as module_dateUpdate'
            )
            ->orderBy('subscriptions.valid_until', 'desc')
            ->first();

        return response()->json($subscription ? true : false, 200);
    }

    /**
     * POST /api/subscribe
     * Body JSON : { "username": "002250585831647", "forfaitId": 2, "description": "..." }
     *
     * - Récupère le service (forfait) pour obtenir la durée et le module
     * - Expire toute souscription active existante pour ce module
     * - Crée une nouvelle souscription active
     * - Débite le wallet de l'utilisateur
     */
    public function subscribe(Request $request)
    {
        $rules = [
            'username'    => 'required|string',
            'forfaitId'   => 'required|integer',
            'description' => 'nullable|string',
        ];

        $messages = [
            'username.required' => "Votre session a expiré. Veuillez vous reconnecter",
            'forfaitId.required' => "Veuillez sélectionner le forfait",
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => collect($validator->errors()->all()),
            ], 422);
        }

        $username  = $request->username;
        $forfaitId = $request->forfaitId;
        $desc      = $request->description;

        // Récupérer le service (forfait)
        $service = Services::join('modules', 'services.module_id', '=', 'modules.id_module')
            ->where('services.id_service', $forfaitId)
            ->select('services.*', 'modules.id_module', 'modules.libelle as module_libelle')
            ->first();

        if (!$service) {
            return response()->json(['message' => 'Forfait introuvable.'], 404);
        }

        // Vérifier que l'utilisateur existe et a assez de solde
        $user = UsersPharma::where('phone_number', $request->username)->first();
        if (!$user) {
            return response()->json(['message' => 'Utilisateur introuvable.'], 404);
        }

        if ($user->amount < $service->price) {
            return response()->json(['message' => 'Solde insuffisant pour effectuer cet abonnement.'], 402);
        }

        DB::transaction(function () use ($username, $service, $desc, $user) {
            $now        = Carbon::now();
            $validUntil = Carbon::now()->addDays($service->duration);

            // Désactiver les souscriptions actives existantes pour ce module
            Subscriptions::where('username', $username)
                ->where('module_id', $service->id_module)
                ->where('status', 'active')
                ->update(['status' => 'inactive', 'updated_at' => $now]);

            // Créer la nouvelle souscription
            Subscriptions::insert([
                'username'    => $username,
                'module_id'   => $service->id_module,
                'duree'       => $service->duration,
                'status'      => 'active',
                'valid_until' => $validUntil,
                'description' => $desc ?? "Souscription pour {$service->duration} jour(s) du module : {$service->module_libelle}",
                'type_service' => "Souscription pour {$service->duration} jour(s) du module : {$service->module_libelle}",
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);

            // Débiter le wallet
            UsersPharma::where('phone_number', $username)
                ->update([
                    'last_amount' => $user->amount,
                    'amount'      => $user->amount - $service->price,
                    'updated_at'  => $now,
                ]);
        });

        return response()->json(['message' => 'Abonnement souscrit avec succès.'], 201);
    }
}
