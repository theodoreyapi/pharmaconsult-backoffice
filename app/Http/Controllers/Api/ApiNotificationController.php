<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiNotificationController extends Controller
{
    public function notifications($patient)
{
    $patientId = $patient;

    $rappels = DB::table('rappels as r')
        ->join('pharmacien as p', 'p.id_pharmacien', '=', 'r.pharmacien_id')
        ->select(
            'r.id_rappel as id',
            DB::raw("'RAPPEL' as notification_type"),
            'r.type',
            'r.message',
            'r.status',
            DB::raw("CONCAT(p.first_name,' ',p.last_name) as auteur"),
            'r.created_at'
        )
        ->where('r.patient_id', $patientId);

    $logs = DB::table('network_logs as n')
        ->join('pharmacien as p', 'p.id_pharmacien', '=', 'n.pharmacien_id')
        ->select(
            'n.id_network_log as id',
            DB::raw("'ACTIVITE' as notification_type"),
            DB::raw("NULL as type"),
            'n.action as message',
            DB::raw("'INFO' as status"),
            DB::raw("CONCAT(p.first_name,' ',p.last_name) as auteur"),
            'n.created_at'
        )
        ->where('n.patient_id', $patientId);

    $notifications = DB::query()
        ->fromSub(
            $rappels->unionAll($logs),
            'notifications'
        )
        ->orderByDesc('created_at')
        ->paginate(20);

    return response()->json([
        $notifications
    ]);
}
}
