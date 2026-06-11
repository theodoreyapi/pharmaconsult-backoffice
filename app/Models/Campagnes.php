<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campagnes extends Model
{
    protected $table = 'campagnes';

    protected $fillable = [
        'name',
        'description',
        'status',
        'portee',
        'channel',
        'message_template',
        'scheduled_at',
        'started_at',
        'ended_at',
        'patients_count',
        'pathologie_id',
        'pharmacy_id',
        'created_by',
    ];

    protected $primaryKey = 'id_campagne';
}
