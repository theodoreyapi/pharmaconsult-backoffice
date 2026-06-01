<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaccineSchedule extends Model
{
    protected $primaryKey = 'id_schedule';

    protected $fillable = [
        'vaccine_id',
        'min_age_months',
        'max_age_months',
        'age_label',
        'gender',
        'only_pregnant',
        'for_travelers',
        'travel_zone',
        'for_health_workers',
        'for_immunocompromised',
        'for_seniors',
        'is_booster',
        'booster_every_months',
        'dose_number',
        'important_note',
        'priority',
        'phase_name',
        'in_community',
        'exposed_to_vectors',
    ];

    protected $table = 'vaccine_schedules';
}
