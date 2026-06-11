<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesure extends Model
{
    protected $table = 'mesures';

    protected $fillable = [
        'type',
        'systolic',
        'diastolic',
        'heart_rate_bpm',
        'glycemia_mmol',
        'is_fasting',
        'weight_kg',
        'height_cm',
        'imc',
        'comment',
        'status_label',
        'patient_id',
        'pharmacien_id',
    ];

    protected $primaryKey = 'id_mesure';
}
