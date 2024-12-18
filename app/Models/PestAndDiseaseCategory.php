<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PestAndDiseaseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', // Pest or Disease
        'name', // Name of the pest or disease
        'description', // Optional description
    ];

    public function pestAndDiseases()
    {
        return $this->hasMany(PestAndDisease::class, 'category_id');
    }
}
