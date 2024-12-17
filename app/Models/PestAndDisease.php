<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PestAndDisease extends Model
{
    use HasFactory;

    // Define the table name (if not following Laravel's default conventions)
    protected $table = 'pest_and_disease';

     // Explicitly define the primary key
     protected $primaryKey = 'case_id';

     // If the primary key is not auto-incrementing
     public $incrementing = true;
 
     // Specify the key type (integer)
     protected $keyType = 'int';

     

    // Mass-assignable fields
    protected $fillable = [
        'farmer_id',
        'farm_id',
        'date_detected',
        'type',
        'name',
        'severity',
        'image_url',
        'diagnosis_result',
        'recommended_treatment',
        'treatment_status',
    ];

    /**
     * Relationship: PestAndDisease belongs to a Farmer
     */
    public function farmer()
    {
        return $this->belongsTo(Farmer::class, 'farmer_id', 'id');
    }

    /**
     * Relationship: PestAndDisease belongs to a Farm
     */
    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id', 'id');
    }
}
