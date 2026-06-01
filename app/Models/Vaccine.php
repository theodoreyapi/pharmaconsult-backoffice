<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    protected $table = 'vaccines';

    protected $fillable = [
        'name',
        'slug',
        'short_name',
        'description',
        'public_price',
        'private_price_min',
        'private_price_max',
        'vaccine_type',
        'currency',
        'important_info',
        'is_active',
        'target_species',
        'targeted_disease',
        'administration_mode',
        'scientific_type',
        'protected_against',
        'target_public',
        'source_url',
        'validation_status',
    ];

    protected $primaryKey = 'id_vaccine';
}
