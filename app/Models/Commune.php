<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    protected $table = 'commune';

    protected $fillable = [
        'description',
        'name',
    ];

    protected $primaryKey = 'id_commune';
}
