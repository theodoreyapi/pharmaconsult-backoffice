<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamants extends Model
{
    protected $table = 'medicaments';

    protected $fillable = [
        'code_cip',
        'name',
        'notice',
        'price',
        'principe_actif',
        'medicament_picture',
    ];

    protected $primaryKey = 'id_medicament';
}
