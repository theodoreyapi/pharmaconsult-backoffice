<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Operations extends Model
{
    protected $table = 'operations';

    protected $fillable = [
        'amount',
        'reason',
        'type_operation',
        'username',
        'designation',
        'description',
        'name_of_second_party',
        'number_of_second_party',
    ];

    protected $primaryKey = 'id_operation';
}
