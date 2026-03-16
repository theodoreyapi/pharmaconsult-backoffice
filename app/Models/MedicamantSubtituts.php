<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicamantSubtituts extends Model
{
    protected $table = 'medicament_substituts';

    protected $fillable = [
        'substitut_id',
        'medicament_id',
    ];

    protected $primaryKey = 'id_subtitut';
}
