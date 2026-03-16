<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assurances extends Model
{
    protected $table = 'assurances';

    protected $fillable = [
        'description',
        'name',
        'assurance_picture',
    ];

    protected $primaryKey = 'id_assurance';
}
