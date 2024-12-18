<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory;
    protected $table = 'farms';



    protected $fillable = [

        'farmer_id',
        'name',
        'lot_hectare',
        'purok',
        'barangay',
        'municipality',
        'province',
        'north',
        'south',
        'east',
        'west',
        'variety',
        'planning_method',
        'date_of_sowing',
        'date_of_planning',
        'date_of_harvest',
        'population_density',
        'age_group',
        'no_of_hills',
        'land_category',
        'soil_type',
        'topography',
        'source_of_irrigation',
        'tenurial_status',


     ];

     public function soilAnalyses()
{
    return $this->hasMany(SoilAnalysis::class, 'farm_id', 'id');
}

public function pestAndDiseases()
{
    return $this->hasMany(PestAndDisease::class, 'farm_id', 'id');
}

public function farmer()
{
    return $this->belongsTo(Farmer::class, 'farmer_id', 'id');
}


}
