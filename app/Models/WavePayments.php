<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class WavePayments extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'wave_payments';

    protected $fillable = [
        'aggregated_merchant_id',
        'amount',
        'business_name',
        'checkout_id',
        'checkout_status',
        'client_reference',
        'currency',
        'error_url',
        'last_payment_error',
        'payment_status',
        'success_url',
        'transaction_hash',
        'transaction_id',
        'wave_launch_url',
        'when_completed',
        'when_created',
        'when_expires',
    ];

    protected $primaryKey = 'id_wave';
}
