<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyAssurances extends Model
{
    protected $table = 'pharmacy_assurances';

    protected $fillable = [
        'assurance_id',
        'pharmacy_id',
    ];

    protected $primaryKey = 'id_pharmacy_assurance';
}
