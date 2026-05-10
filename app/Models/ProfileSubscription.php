<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileSubscription extends Model
{
    protected $primaryKey = 'id_subscription';

    protected $fillable = [
        'profile_id', 'user_id', 'amount', 'currency',
        'status', 'start_date', 'end_date',
        'payment_reference', 'paid_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'paid_at'    => 'datetime',
        'amount'     => 'float',
    ];

    protected $table = 'profile_subscriptions';
}
