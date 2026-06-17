<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conseils extends Model
{
    use HasFactory;
    protected $table = 'conseils';

    protected $fillable = [
        'type',
        'pathologie_id',
        'titre',
        'description'
    ];

    protected $primaryKey = 'id_conseil';
}
