<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaccineRestriction extends Model
{
    protected $table = 'vaccine_restrictions';

    protected $fillable = [
        'vaccine_id',
        'restriction_type',
        'reason',
    ];

    protected $primaryKey = 'id_restriction';
}
