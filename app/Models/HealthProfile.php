<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthProfile extends Model
{
    protected $primaryKey = 'id_profile';

    protected $fillable = [
        'user_id',
        'name',
        'profile_type',
        'relation',
        'animal_type',
        'gender',
        'birth_date',
        'is_frequent_traveler',
        'is_active',
        'is_pregnant',
        'is_traveler',
        'travel_destination',
        'is_health_worker',
        'is_immunocompromised',
    ];

    protected $casts = [
        'birth_date'           => 'date',
        'is_frequent_traveler' => 'boolean',
        'is_active'            => 'boolean',
        'is_pregnant'          => 'boolean',
        'is_traveler'          => 'boolean',
        'is_health_worker'     => 'boolean',
        'is_immunocompromised' => 'boolean',
    ];

    protected $table = 'health_profiles';
}
