<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modules extends Model
{
    protected $table = 'modules';

    protected $fillable = [
        'description',
        'libelle',
    ];

    protected $primaryKey = 'id_module';
}
