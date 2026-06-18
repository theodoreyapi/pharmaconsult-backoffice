<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaccineEquivalent extends Model
{
    protected $table = 'vaccine_equivalents';

    protected $fillable = [
        'vaccine_id',
        'name',
        'description',
        'price',
        'is_active',
    ];

    protected $primaryKey = 'id_equivalent';
}
