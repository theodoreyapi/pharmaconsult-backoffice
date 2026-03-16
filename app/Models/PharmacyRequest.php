<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyRequest extends Model
{
    protected $table = 'pharmacy_request';

    protected $fillable = [
        'pharmacy_id',
        'request_medicament_id',
        'status',
    ];

    protected $primaryKey = 'id_pharmacy_request';
}
