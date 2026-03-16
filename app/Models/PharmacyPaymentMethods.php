<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PharmacyPaymentMethods extends Model
{
    protected $table = 'pharmacy_payment_methods';

    protected $fillable = [
        'payment_method_id',
        'pharmacy_id',
    ];

    protected $primaryKey = 'id_pharmacy_payment_method';
}
