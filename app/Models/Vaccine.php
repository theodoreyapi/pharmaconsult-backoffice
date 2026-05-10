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
        'currency',
        'important_info',
        'is_active',
    ];

    protected $primaryKey = 'id_vaccine';
}
