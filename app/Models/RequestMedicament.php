<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestMedicament extends Model
{
    protected $table = 'request_medicament';

    protected $fillable = [
        'medicament_id',
        'status',
        'username',
        'comment',
        'medicament_name',
        'photo',
    ];

    protected $primaryKey = 'id_request';
}
