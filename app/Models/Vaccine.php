<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaccine extends Model
{
    protected $table = 'vaccines';

    protected $fillable = [
        'name',
        'slug',
        'short_name',
        'description',
        'public_price',
        'vaccine_type',
        'currency',
        'important_info',
        'is_active',
        'target_species',
        'targeted_disease',
        'administration_mode',
        'scientific_type',
        'protected_against',
        'target_public',
        'source_url',
        'validation_status',
    ];

    protected $primaryKey = 'id_vaccine';

    // app/Models/Vaccine.php

    public function categories()
    {
        return $this->belongsToMany(
            Categorie::class,
            'vaccine_category',  // table pivot
            'vaccine_id',        // FK vers vaccines
            'category_id',       // FK vers categories
            'id_vaccine',        // PK locale
            'id_categorie'       // PK distante
        );
    }

    public function restrictions()
    {
        return $this->hasMany(VaccineRestriction::class, 'vaccine_id', 'id_vaccine');
    }

    public function equivalents()
    {
        return $this->hasMany(VaccineEquivalent::class, 'vaccine_id', 'id_vaccine');
    }

    public function schedules()
    {
        return $this->hasMany(VaccineSchedule::class, 'vaccine_id', 'id_vaccine');
    }
}
