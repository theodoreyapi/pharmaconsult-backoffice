<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfert extends Model
{
    protected $table = 'transfert';

    protected $fillable = [
        'amount',
        'raison',
        'receiver_username',
        'sender_username',
        'execute_by',
    ];

    protected $primaryKey = 'id_transfert';
}
