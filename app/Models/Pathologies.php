<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pathologies extends Model
{
    protected $table = 'pathologies';

    protected $fillable = [
        'name',
        'code',
    ];

    protected $primaryKey = 'id_pathologie';

    public function patientPathologies()
    {
        return $this->hasMany(
            PatientPathologies::class,
            'pathologie_id',
            'id_pathologie'
        );
    }
}
