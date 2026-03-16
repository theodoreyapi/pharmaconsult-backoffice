<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class FcmToken extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'fcm_token';

    protected $fillable = [
        'token',
        'username',
    ];

    protected $primaryKey = 'id_fcm';
}
