<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoilAnalysis extends Model
{
    use HasFactory;

    protected $table = 'soil_analysis'; // ✅ matches your migration

    protected $fillable = [
        'farmer_id',
        'farm_id',
        'farm_name',
        'crop_variety',
        'soil_type',
        'date_collected',
        'location',
        'ref_no',
        'submitted_by',
        'date_submitted',
        'date_analyzed',
        'lab_no',
        'field_no',
        'ph_level',
        'nitrogen',
        'phosphorus',
        'potassium',
        'organic_matter',
        'recommendation',
    ];

    protected $casts = [
        'date_collected' => 'date',
        'date_submitted' => 'date',
        'date_analyzed' => 'date',
        'ph_level' => 'float',
        'nitrogen' => 'float',
        'phosphorus' => 'float',
        'potassium' => 'float',
        'organic_matter' => 'float',
    ];

    // Optional relationships
    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
