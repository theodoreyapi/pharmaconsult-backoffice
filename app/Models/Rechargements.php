<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rechargements extends Model
{
    protected $table = 'rechargements';

    protected $fillable = [
        'transaction_id',
        'checkout_session_id',
        'currency',
        'montant',
        'payment_method',
        'status',
        'username',
    ];

    protected $primaryKey = 'id_rechargement';
}
