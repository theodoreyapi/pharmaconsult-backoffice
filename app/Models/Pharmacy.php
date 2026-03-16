<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    protected $table = 'pharmacy';

    protected $fillable = [
        'address',
        'end_garde_date',
        'facade_image',
        'gps_coordinates',
        'name',
        'opening_hours',
        'owner_name',
        'phone_number',
        'start_garde_date',
        'commune_id',
        'whats_app_phone_number',
        'closing_hours',
    ];

    protected $primaryKey = 'id_pharmacy';
}
