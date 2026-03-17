<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'description',
        'type_service',
        'duree',
        'status',
        'username',
        'valid_until',
        'module_id',
    ];

    protected $primaryKey = 'id_subscription';
}
