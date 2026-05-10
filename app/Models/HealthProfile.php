<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthProfile extends Model
{
    protected $primaryKey = 'id_profile';

    protected $fillable = [
        'user_id', 'name', 'profile_type', 'relation',
        'animal_type', 'gender', 'birth_date',
        'is_frequent_traveler', 'is_active',
    ];

    protected $casts = [
        'birth_date'           => 'date',
        'is_frequent_traveler' => 'boolean',
        'is_active'            => 'boolean',
    ];

    protected $table = 'health_profiles';
}
