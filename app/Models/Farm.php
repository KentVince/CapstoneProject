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
        'farm_name',
        'farmer_address_bgy',
        'farmer_address_mun',
        'farmer_address_prv',
        'latitude',
        'longtitude',
        'crop_name',
        'crop_variety',
        'crop_area',
        'soil_type',
        'cropping',
        'farmworker',
        'verified_area',
        'status',
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
