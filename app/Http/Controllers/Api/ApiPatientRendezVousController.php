<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\RendezVous;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiPatientRendezVousController extends Controller
{
    private array $mesuresLabels = [
        'PRESSION_ARTERIELLE' => 'Tension artérielle',
        'FREQUENCE_CARDIAQUE' => 'Fréquence cardiaque',
        'GLYCEMIE'            => 'Glycémie',
        'POIDS_IMC'           => 'Poids & IMC',
    ];

    private array $statusLabels = [
        'ATTENTE'  => 'À venir',
        'EFFECTUE' => 'Effectué',
        'MANQUE'   => 'Manqué',
    ];

    /**
     * Liste tous les RDV du patient connecté, avec filtre optionnel par statut.
     * GET /api/patients/{patient}/rendez-vous
     */
    public function index($patient)
    {
        $query = RendezVous::where('patient_id', $patient)
            ->orderBy('date', 'desc')
            ->orderBy('heure', 'desc');

        $rdvs = $query->get();

        return response()->json([
            'success' => true,
            'data' => $rdvs->map(fn($rdv) => $this->formatRdv($rdv))->values(),
        ]);
    }

    /**
     * Plannings récurrents actifs du patient.
     * GET /api/patients/{patient}/plannings
     */
    public function plannings($patient)
    {
        $patient = Patient::findOrFail($patient);

        $plannings = $patient->plannings()
            ->where('status', 'ACTIF')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $plannings->map(fn($pl) => $this->formatPlanning($pl))->values(),
        ]);
    }

    private function formatRdv(RendezVous $rdv): array
    {
        $date = Carbon::parse($rdv->date)->locale('fr');

        return [
            'id'            => $rdv->id_rendez_vous,
            'date'          => $date->format('Y-m-d'),
            'date_label'    => ucfirst($date->translatedFormat('l j F Y')),
            'heure'         => Carbon::parse($rdv->heure)->format('H:i'),
            'status'        => $rdv->status,
            'status_label'  => $this->statusLabels[$rdv->status] ?? $rdv->status,
            'is_today'      => $date->isToday(),
            'is_past'       => $date->isPast() && !$date->isToday(),
            'mesures_types' => collect($rdv->mesures_types)->map(fn($t) => [
                'code'  => $t,
                'label' => $this->mesuresLabels[$t] ?? $t,
            ])->values(),
            'notes'         => $rdv->notes,
            'is_recurrent'  => !is_null($rdv->planning_id),
            'created_at'    => $rdv->created_at->toIso8601String(),
        ];
    }

    private function formatPlanning($planning): array
    {
        return [
            'id'              => $planning->id_planning,
            'frequency_type'  => $planning->frequency_type,
            'frequency_label' => $planning->frequency_label,
            'jours'           => $planning->jours,
            'heure'           => Carbon::parse($planning->heure)->format('H:i'),
            'mesures_types'   => collect($planning->mesures_types)->map(fn($t) => [
                'code'  => $t,
                'label' => $this->mesuresLabels[$t] ?? $t,
            ])->values(),
            'rappel_avant'    => $planning->rappel_avant,
            'canal'           => $planning->canal,
            'notes'           => $planning->notes,
            'status'          => $planning->status,
        ];
    }
}
