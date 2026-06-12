<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rappels extends Model
{
    protected $table = 'rappels';

    protected $fillable = [
        'channel',
        'type',
        'message',
        'status',
        'sent_at',
        'patient_id',
        'pharmacien_id',
        'message_id',
    ];

    protected $primaryKey = 'id_rappel';

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id', 'id_patient');
    }
}
