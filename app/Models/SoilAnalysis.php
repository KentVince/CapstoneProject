<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SoilAnalysis extends Model
{
   

    use HasFactory,HasRoles,HasPanelShield;

    // Define the table name (optional if Laravel conventions are followed)
    protected $table = 'soil_analysis';

     // Explicitly define the primary key
     protected $primaryKey = 'soil_test_id';

     // If the primary key is not auto-incrementing
     public $incrementing = true;
 
     // Specify the key type (integer)
     protected $keyType = 'int';

    // Mass-assignable attributes
    protected $fillable = [
        'farmer_id',
        'farm_id',
        'ref_no',
        'submitted_by',
        'date_collected',
        'date_submitted',
        'date_analyzed',
        'lab_no',
        'field_no',
        'soil_type',
        'pH_level',
        'w_om',
        'p_ppm',
        'k_ppm',
        'wb_om',
        'wb_oc',
        'crop_variety',
        'nutrient_req_N',
        'nutrient_req_P2O3',
        'nutrient_req_K2O',
        'lime_req',
        'pH_preference',
        'organic_matter',
    ];

    /**
     * Relationship: SoilAnalysis belongs to a Farmer
     */
    public function farmer()
    {
        return $this->belongsTo(Farmer::class, 'farmer_id', 'id');
    }

    /**
     * Relationship: SoilAnalysis belongs to a Farm
     */
    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id', 'id');
    }
}
