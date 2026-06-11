<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Abonnements extends Model
{
    protected $table = 'abonnements';

    protected $fillable = [
        'plan_name',
        'last_name',
        'price',
        'billing_cycle',
        'start_date',
        'renewal_date',
        'status',
        'max_patients',
        'max_messages_per_month',
        'max_campaigns',
        'max_team_members',
        'pharmacy_id',
    ];

    protected $primaryKey = 'id_abonnement';
}
