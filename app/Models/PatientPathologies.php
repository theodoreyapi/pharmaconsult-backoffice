<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientPathologies extends Model
{
    protected $table = 'patient_pathologies';

    protected $fillable = [
        'patient_id',
        'pathologie_id',
        'priority',
        'status',
        'start_date',
        'doctor_name',
        'notes',
    ];

    protected $primaryKey = 'id_patient_pathologie';

    public function pathologie()
    {
        return $this->belongsTo(
            Pathologies::class,
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
