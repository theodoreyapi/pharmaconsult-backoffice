<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    use HasFactory;

    protected $table = 'plannings';

    protected $primaryKey = 'id_planning';

    protected $fillable = [
        'patient_id',
        'pharmacy_id',
        'created_by',
        'frequency_type',
        'jours',
        'heure',
        'mesures_types',
        'rappel_avant',
        'canal',
        'notes',
        'status',
    ];

    protected $casts = [
        'jours'         => 'array',
        'mesures_types' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id_patient');
    }

    public function rendezVous()
    {
        return $this->hasMany(RendezVous::class, 'planning_id');
    }

    /**
     * Libellé lisible de la fréquence
     */
    public function getFrequencyLabelAttribute(): string
    {
        return match ($this->frequency_type) {
            '1x'    => '1 fois par semaine',
            '2x'    => '2 fois par semaine',
            'perso' => 'Planning personnalisé',
            default => $this->frequency_type,
        };
    }

    /**
     * Libellé lisible du rappel
     */
    public function getRappelAvantLabelAttribute(): string
    {
        return match ($this->rappel_avant) {
            '24H'   => 'la veille (24h)',
            '2H'    => '2 heures avant',
            '1H'    => '1 heure avant',
            '2J'    => '2 jours avant',
            'AUCUN' => 'aucun rappel',
            default => $this->rappel_avant,
        };
    }
}
