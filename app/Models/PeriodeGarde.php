<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodeGarde extends Model
{
    protected $table = 'periode_garde';

    protected $fillable = [
        'date_debut',
        'date_fin',
        'date_miseajour',
        'name',
    ];

    protected $primaryKey = 'id_garde';
}
