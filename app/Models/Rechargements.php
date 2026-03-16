<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rechargements extends Model
{
    protected $table = 'rechargements';

    protected $fillable = [
        'channels',
        'client_transaction_id',
        'code',
        'currency',
        'description',
        'id_transaction',
        'message',
        'montant',
        'notify_url',
        'payment_method',
        'phone',
        'prefix',
        'status',
        'treatment_status',
        'username',
    ];

    protected $primaryKey = 'id_rechargement';
}
