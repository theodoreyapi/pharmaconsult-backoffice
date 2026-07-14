<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $primaryKey = 'id_rendez_vous';

    protected $fillable = [
        'patient_id',
        'pharmacy_id',
        'planning_id',
        'date',
        'heure',
        'mesures_types',
        'status',
        'mesure_liee_id',
        'notes',
    ];

    protected $casts = [
        'date'          => 'date',
        'mesures_types' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id_patient');
    }

    public function planning()
    {
        return $this->belongsTo(Planning::class, 'planning_id');
    }
}
