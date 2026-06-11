<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiMesureController extends Controller
{
    public function dashboardMesures(Patient $patient)
    {
        $pathologies = $patient->pathologies()
            ->with('pathologie')
            ->where('status', 'ACTIF')
            ->get();

        return response()->json([
            'patient' => [
                'id' => $patient->id_patient,
                'nom' => $patient->first_name . ' ' . $patient->last_name,
                'taille_cm' => $patient->height_cm,
            ],

            'pathologies' => $pathologies->map(function ($p) {
                return [
                    'code' => $p->pathologie->code,
                    'nom' => $p->pathologie->name,
                    'priority' => $p->priority
                ];
            }),

            'pression_arterielle' => $this->getPressionData($patient),

            'frequence_cardiaque' => $this->getHeartRateData($patient),

            'glycemie' => $this->getGlycemieData($patient),

            'poids' => $this->getPoidsData($patient),

            'imc' => $this->getImcData($patient),
        ]);
    }

    private function getPressionData($patient)
    {
        $mesures = $patient->mesures()
            ->where('type', 'PRESSION_ARTERIELLE')
            ->latest()
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $last = $mesures->last();

        return [
            'current_pression' => [
                'value' => $last
                    ? "{$last->systolic}/{$last->diastolic}"
                    : null,
                'unit' => 'mmHg',
                'date' => optional($last)->created_at?->format('d M Y'),
                'status' => optional($last)->status_label,
            ],

            'chart_pression' => [
                'labels' => $mesures->map(
                    fn($m) => Carbon::parse($m->created_at)
                        ->format('d M')
                ),
                'systolic' => $mesures->pluck('systolic'),
                'diastolic' => $mesures->pluck('diastolic'),
            ],

            'history_pression' => $mesures->sortByDesc('created_at')
                ->values()
                ->map(function ($m) {
                    return [
                        'systolic' => $m->systolic,
                        'diastolic' => $m->diastolic,
                        'date' => $m->created_at->format('d M Y'),
                        'status' => $m->status_label,
                    ];
                })
        ];
    }

    private function getHeartRateData($patient)
    {
        $mesures = $patient->mesures()
            ->where('type', 'FREQUENCE_CARDIAQUE')
            ->latest()
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $last = $mesures->last();

        return [
            'current_frequence' => [
                'value' => optional($last)->heart_rate_bpm,
                'unit' => 'bpm',
                'date' => optional($last)->created_at?->format('d M Y'),
                'status' => optional($last)->status_label,
            ],

            'chart_frequence' => [
                'labels' => $mesures->map(
                    fn($m) => $m->created_at->format('d M')
                ),
                'values' => $mesures->pluck('heart_rate_bpm')
            ],

            'history_frequence' => $mesures->sortByDesc('created_at')
                ->values()
                ->map(function ($m) {
                    return [
                        'value' => $m->heart_rate_bpm,
                        'unit' => 'bpm',
                        'date' => $m->created_at->format('d M Y'),
                        'status' => $m->status_label
                    ];
                })
        ];
    }

    private function getGlycemieData($patient)
    {
        $mesures = $patient->mesures()
            ->where('type', 'GLYCEMIE')
            ->latest()
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $last = $mesures->last();

        return [
            'current_glycemie' => [
                'value' => optional($last)->glycemia_mmol,
                'unit' => 'mmol/L',
                'date' => optional($last)->created_at?->format('d M Y'),
                'status' => optional($last)->status_label,
                'is_fasting' => optional($last)->is_fasting,
            ],

            'chart_glycemie' => [
                'labels' => $mesures->map(
                    fn($m) => $m->created_at->format('d M')
                ),
                'values' => $mesures->pluck('glycemia_mmol')
            ],

            'history_glycemie' => $mesures->sortByDesc('created_at')
                ->values()
                ->map(function ($m) {
                    return [
                        'value' => $m->glycemia_mmol,
                        'unit' => 'mmol/L',
                        'date' => $m->created_at->format('d M Y'),
                        'status' => $m->status_label,
                        'is_fasting' => $m->is_fasting
                    ];
                })
        ];
    }

    private function getPoidsData($patient)
    {
        $mesures = $patient->mesures()
            ->where('type', 'POIDS_IMC')
            ->whereNotNull('weight_kg')
            ->latest()
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $last = $mesures->last();

        return [
            'current_poids' => [
                'value' => optional($last)->weight_kg,
                'unit' => 'kg',
                'date' => optional($last)->created_at?->format('d M Y'),
                'status' => optional($last)->status_label,
            ],

            'chart_poids' => [
                'labels' => $mesures->map(
                    fn($m) => $m->created_at->format('d M')
                ),
                'values' => $mesures->pluck('weight_kg')
            ],

            'history_poids' => $mesures->sortByDesc('created_at')
                ->values()
                ->map(function ($m) {
                    return [
                        'value' => $m->weight_kg,
                        'unit' => 'kg',
                        'date' => $m->created_at->format('d M Y'),
                        'status' => $m->status_label
                    ];
                })
        ];
    }

    private function getImcData($patient)
    {
        $mesures = $patient->mesures()
            ->where('type', 'POIDS_IMC')
            ->whereNotNull('imc')
            ->latest()
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $last = $mesures->last();

        return [
            'current_imc' => [
                'value' => optional($last)->imc,
                'unit' => 'kg/m²',
                'date' => optional($last)->created_at?->format('d M Y'),
                'status' => optional($last)->status_label,
            ],

            'chart_imc' => [
                'labels' => $mesures->map(
                    fn($m) => $m->created_at->format('d M')
                ),
                'values' => $mesures->pluck('imc')
            ],

            'history_imc' => $mesures->sortByDesc('created_at')
                ->values()
                ->map(function ($m) {
                    return [
                        'value' => $m->imc,
                        'unit' => 'kg/m²',
                        'date' => $m->created_at->format('d M Y'),
                        'status' => $m->status_label
                    ];
                })
        ];
    }



    public function generate(Request $request, Patient $patient)
    {
        $start = $request->start_date;
        $end = $request->end_date;

        $mesures = $patient->mesures()
            ->whereBetween('created_at', [
                $start . ' 00:00:00',
                $end . ' 23:59:59'
            ])
            ->get();

        return response()->json([
            'periode' => [
                'start_date' => $start,
                'end_date' => $end,
                'total_mesures' => $mesures->count(),
            ],

            'resume_global' => $this->resumeGlobal($mesures),

            'pression_arterielle' => $this->pressionArterielle($mesures),

            'frequence_cardiaque' => $this->frequenceCardiaque($mesures),

            'glycemie' => $this->glycemie($mesures),

            'poids' => $this->poids($mesures),

            'imc' => $this->imc($mesures),
        ]);
    }

    private function resumeGlobal($mesures)
    {
        return [

            'normales' => $mesures
                ->where('status_label', 'Normal')
                ->count(),

            'attention' => $mesures
                ->where('status_label', 'Attention')
                ->count(),

            'elevees' => $mesures
                ->filter(function ($m) {
                    return in_array(
                        $m->status_label,
                        ['Élevé', 'Critique']
                    );
                })
                ->count(),
        ];
    }

    private function trend($values)
    {
        if ($values->count() < 2) {
            return 'STABLE';
        }

        $first = $values->first();
        $last = $values->last();

        if ($last > $first) {
            return 'HAUSSE';
        }

        if ($last < $first) {
            return 'BAISSE';
        }

        return 'STABLE';
    }

    private function pressionArterielle($mesures)
    {
        $data = $mesures
            ->where('type', 'PRESSION_ARTERIELLE')
            ->sortBy('created_at')
            ->values();

        if ($data->isEmpty()) {
            return null;
        }

        $last = $data->last();

        return [

            'count' => $data->count(),

            'average_systolic' =>
            round($data->avg('systolic')),

            'average_diastolic' =>
            round($data->avg('diastolic')),

            'last_measure' =>
            $last->systolic . '/' . $last->diastolic,

            'trend' => $this->trend(
                $data->pluck('systolic')
            ),

            'status' => [
                'normales' =>
                $data->where('status_label', 'Normal')->count(),

                'attention' =>
                $data->where('status_label', 'Attention')->count(),

                'elevees' =>
                $data->where('status_label', 'Élevé')->count(),
            ]
        ];
    }

    private function frequenceCardiaque($mesures)
    {
        $data = $mesures
            ->where('type', 'FREQUENCE_CARDIAQUE')
            ->sortBy('created_at')
            ->values();

        if ($data->isEmpty()) {
            return null;
        }

        $last = $data->last();

        return [

            'count' => $data->count(),

            'average' =>
            round($data->avg('heart_rate_bpm'), 1),

            'last' =>
            $last->heart_rate_bpm,

            'min' =>
            $data->min('heart_rate_bpm'),

            'max' =>
            $data->max('heart_rate_bpm'),

            'trend' =>
            $this->trend(
                $data->pluck('heart_rate_bpm')
            ),

            'status' => [
                'normales' =>
                $data->where('status_label', 'Normal')->count(),

                'attention' =>
                $data->where('status_label', 'Attention')->count(),

                'elevees' =>
                $data->where('status_label', 'Élevé')->count(),
            ]
        ];
    }

    private function glycemie($mesures)
    {
        $data = $mesures
            ->where('type', 'GLYCEMIE')
            ->sortBy('created_at')
            ->values();

        if ($data->isEmpty()) {
            return null;
        }

        $last = $data->last();

        return [

            'count' => $data->count(),

            'average' =>
            round($data->avg('glycemia_mmol'), 2),

            'last' =>
            $last->glycemia_mmol,

            'min' =>
            $data->min('glycemia_mmol'),

            'max' =>
            $data->max('glycemia_mmol'),

            'trend' =>
            $this->trend(
                $data->pluck('glycemia_mmol')
            ),

            'status' => [
                'normales' =>
                $data->where('status_label', 'Normal')->count(),

                'attention' =>
                $data->where('status_label', 'Attention')->count(),

                'elevees' =>
                $data->where('status_label', 'Élevé')->count(),
            ]
        ];
    }

    private function poids($mesures)
    {
        $data = $mesures
            ->whereNotNull('weight_kg')
            ->sortBy('created_at')
            ->values();

        if ($data->isEmpty()) {
            return null;
        }

        $last = $data->last();

        return [

            'count' => $data->count(),

            'average' =>
            round($data->avg('weight_kg'), 1),

            'last' =>
            $last->weight_kg,

            'min' =>
            $data->min('weight_kg'),

            'max' =>
            $data->max('weight_kg'),

            'trend' =>
            $this->trend(
                $data->pluck('weight_kg')
            ),

            'status' => [
                'normales' =>
                $data->where('status_label', 'Normal')->count(),

                'attention' =>
                $data->where('status_label', 'Attention')->count(),

                'elevees' =>
                $data->where('status_label', 'Élevé')->count(),
            ]
        ];
    }

    private function imc($mesures)
    {
        $data = $mesures
            ->whereNotNull('imc')
            ->sortBy('created_at')
            ->values();

        if ($data->isEmpty()) {
            return null;
        }

        $last = $data->last();

        return [

            'count' => $data->count(),

            'average' =>
            round($data->avg('imc'), 2),

            'last' =>
            $last->imc,

            'min' =>
            $data->min('imc'),

            'max' =>
            $data->max('imc'),

            'trend' =>
            $this->trend(
                $data->pluck('imc')
            ),

            'status' => [
                'normales' =>
                $data->where('status_label', 'Normal')->count(),

                'attention' =>
                $data->where('status_label', 'Attention')->count(),

                'elevees' =>
                $data->where('status_label', 'Élevé')->count(),
            ]
        ];
    }


    public function latest($id)
    {
        $patient = Patient::with([
            'mesures' => function ($query) {
                $query->latest();
            },
            'rappels' => function ($query) {
                $query->latest();
            }
        ])->findOrFail($id);

        // Dernière pression artérielle
        $latestPressure = $patient->mesures()
            ->where('type', 'PRESSION_ARTERIELLE')
            ->latest()
            ->first();

        // Dernière glycémie
        $latestGlycemia = $patient->mesures()
            ->where('type', 'GLYCEMIE')
            ->latest()
            ->first();

        // Dernier rappel
        $latestRappel = $patient->rappels()
            ->latest()
            ->first();

        return response()->json([
            'latest_pressure' => $latestPressure ? [
                'systolic' => $latestPressure->systolic,
                'diastolic' => $latestPressure->diastolic,
                'date' => $latestPressure->created_at,
            ] : null,

            'latest_glycemia' => $latestGlycemia ? [
                'value' => $latestGlycemia->glycemia_mmol,
                'is_fasting' => $latestGlycemia->is_fasting,
                'date' => $latestGlycemia->created_at,
            ] : null,

            'latest_rappel' => $latestRappel ? [
                'message' => $latestRappel->message,
                'sent_at' => $latestRappel->sent_at,
            ] : null,
        ]);
    }
}
