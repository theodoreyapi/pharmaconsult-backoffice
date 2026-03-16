<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $table = 'pharmacien';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $primaryKey = 'id_pharmacien';
}
