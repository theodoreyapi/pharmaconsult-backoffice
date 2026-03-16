<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'review';

    protected $fillable = [
        'commentaire',
        'evaluation',
        'username',
        'pharmacy_id',
    ];

    protected $primaryKey = 'id_review';
}
