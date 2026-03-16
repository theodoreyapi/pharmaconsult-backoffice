<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publicite extends Model
{
    protected $table = 'publicite';

    protected $fillable = [
        'end_date',
        'image',
        'lien',
        'name',
        'price',
        'start_date',
        'status',
    ];

    protected $primaryKey = 'id_publicite';
}
