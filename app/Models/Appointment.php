<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointments';

    protected $fillable = [
        'reference',
        'vaccine_id',
        'pharmacy_id',
        'user_id',
        'patient_name',
        'patient_phone',
        'patient_email',
        'appointment_date',
        'status',
        'notes',
        'confirmed_at',
        'cancelled_at',
    ];

    protected $primaryKey = 'id_appointment';
}
