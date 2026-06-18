<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileVaccination extends Model
{
    protected $primaryKey = 'id_vaccination';

    protected $fillable = [
        'profile_id',
        'vaccine_id',
        'vaccine_name_free',
        'vaccination_date',
        'next_reminder_date',
        'center_type',
        'center_name',
        'certificate_image',
        'notes',
    ];

    protected $casts = [
        'vaccination_date'    => 'date',
        'next_reminder_date'  => 'date',
    ];

    protected $table = 'profile_vaccinations';

    public function vaccine()
    {
        return $this->belongsTo(Vaccine::class, 'vaccine_id', 'id_vaccine');
    }

    public function profile()
    {
        return $this->belongsTo(HealthProfile::class, 'profile_id', 'id_profile');
    }
}
