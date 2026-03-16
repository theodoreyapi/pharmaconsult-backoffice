<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservationMedicament extends Model
{
    protected $table = 'reservation_medicament';

    protected $fillable = [
        'date_expiration',
        'date_reservation',
        'medicament_id',
        'pharmacy_id',
        'status',
        'user_name',
    ];

    protected $primaryKey = 'id_reservation';
}
