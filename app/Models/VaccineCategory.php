<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaccineCategory extends Model
{
    protected $table = 'vaccine_category';

    protected $fillable = [
        'vaccine_id',
        'category_id',
    ];

    protected $primaryKey = 'id_vaccine_category';
}
