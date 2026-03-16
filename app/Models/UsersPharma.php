<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class UsersPharma extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'users_pharma';

    protected $fillable = [
        'active',
        'email',
        'first_name',
        'last_name',
        'password',
        'phone_number',
        'role',
        'about_me',
        'country',
        'profile_picture',
        'username',
        'amount',
        'last_amount',
        'otp_code',
        'otp_expire_at',
        'otp_verified',
    ];

    protected $primaryKey = 'id_user';

    protected $hidden = [
        'password',
    ];
}
