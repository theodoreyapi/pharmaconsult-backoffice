<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParametresGeneraux extends Model
{
    protected $table = 'parametres_generaux';

    protected $fillable = [
        'contenu',
        'date_create',
        'libelle',
        'type',
    ];

    protected $primaryKey = 'id_politique';
}
