<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoyensPaiment extends Model
{
    protected $table = 'moyens_paiement';

    protected $fillable = [
        'description',
        'name',
        'payment_method_picture',
    ];

    protected $primaryKey = 'id_moyen_payment';
}
