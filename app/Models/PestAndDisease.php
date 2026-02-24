<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PestAndDisease extends Model
{
    use HasFactory;

    // Define the table name (if not following Laravel's default conventions)
    protected $table = 'pest_and_disease';

    // Explicitly define the primary key
    protected $primaryKey = 'case_id';

    // If the primary key is auto-incrementing
    public $incrementing = true;

    // Specify the key type (integer)
    protected $keyType = 'int';

       // Mass-assignable fields
    // protected $fillable = [
    //     'farmer_id',
    //     'farm_id',
    //     'date_detected',
    //     'type',
    //     'pest',
    //     'severity',
    //     'image_url',
    //     'diagnosis_result',
    //     'recommended_treatment',
    //     'treatment_status',
    //     'latitude',
    //     'longitude',
    //     'area',
    //     'confidence',
    // ];


    // ✅ Mass-assignable fields (QR code fields removed)



    protected $fillable = [
        'app_no',
        'expert_id',
        'farmer_id',
        'farm_id',
        'pest',
        'type',
        'confidence',
        'latitude',
        'longitude',
        'area',
        'date_detected',
        'severity',
        'image_path',
        'validation_status',
        'expert_comments',
        'ai_recommendation',
        'validated_by',
        'validated_at',
        'farmer_action',
        'farmer_action_date',
    ];

    protected $casts = [
        'options' => 'array',
        'validated_at' => 'datetime',
        'farmer_action_date' => 'datetime',
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

    /**
     * Relationship: PestAndDisease belongs to a Category
     */
    public function category()
    {
        return $this->belongsTo(PestAndDiseaseCategory::class, 'category_id');
    }

    /**
     * Relationship: PestAndDisease belongs to an Agricultural Professional (Expert)
     */
    public function expert()
    {
        return $this->belongsTo(AgriculturalProfessional::class, 'expert_id', 'id');
    }

    /**
     * Relationship: PestAndDisease validated by a User (Expert)
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by', 'id');
    }
}
