<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traitement extends Model
{
    protected $table = 'traitements';

    protected $fillable = [
        'medication_name',
        'dosage',
        'frequency_per_day',
        'quantity_delivered',
        'duration_days',
        'dispensed_at',
        'estimated_end_date',
        'status',
        'patient_id',
        'pathologie_id',
        'pharmacien_id',
    ];

    protected $primaryKey = 'id_traitement';

    public function pathologie()
    {
        return $this->belongsTo(
            PatientPathologies::class,
            'pathologie_id',
            'id_pathologie'
        );
    }

    public function patient()
    {
        return $this->belongsTo(
            Patient::class,
            'patient_id',
            'id_patient'
        );
    }
}
