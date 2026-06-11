<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patients';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'gender',
        'birth_date',
        'city',
        'commune',
        'height_cm',
        'qr_code',
        'status',
        'active',
        'consent_suivi',
        'consent_whatsapp',
        'consent_sms',
        'consent_reseau',
        'pharmacy_id',
        'created_by',
        'user_id',
    ];

    protected $primaryKey = 'id_patient';

    public function mesures()
    {
        return $this->hasMany(Mesure::class, 'patient_id', 'id_patient');
    }

    public function pathologies()
    {
        return $this->hasMany(
            PatientPathologies::class,
            'patient_id',
            'id_patient'
        );
    }

    public function traitements()
    {
        return $this->hasMany(
            Traitement::class,
            'patient_id',
            'id_patient'
        );
    }

    public function pharmacie()
    {
        return $this->belongsTo(
            Pharmacy::class,
            'pharmacy_id',
            'id_pharmacy'
        );
    }

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class, 'pharmacy_id', 'id_pharmacy');
    }

    public function rappels()
    {
        return $this->hasMany(Rappels::class, 'patient_id', 'id_patient');
    }
}
