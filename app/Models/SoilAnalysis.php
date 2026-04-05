<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoilAnalysis extends Model
{
    use HasFactory;

    protected $table = 'soil_analysis'; // ✅ matches your migration

    protected $fillable = [
        'sample_id',
        'farmer_id',
        'farm_name',
        'farm_id',
        'crop_variety',
        'soil_type',
        'analysis_type',
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
        'lab_file',
        'recommendation',
        'ai_diagnosis',
        'ai_farmer_summary',
        'ai_key_concerns',
        'ai_priority_actions',
        'ai_soil_remarks',
        'ai_organic_alternatives',
        'ai_practices',
        'ai_monitoring_plan',
        'ai_expected_outcomes',
        'ai_reminders',
        'validation_status',
        'expert_comments',
        'validated_by',
        'validated_at',
        'farmer_reply',
        'farmer_reply_date',
        'admin_viewed_at',
    ];

    protected $casts = [
        'date_collected' => 'date',
        'date_submitted' => 'date',
        'date_analyzed' => 'date',
        'validated_at' => 'datetime',
        'ph_level' => 'float',
        'nitrogen' => 'float',
        'phosphorus' => 'float',
        'potassium' => 'float',
        'organic_matter' => 'float',
        'farmer_reply_date' => 'datetime',
        'admin_viewed_at' => 'datetime',
        'lab_file'                => 'array',
        'ai_key_concerns'         => 'array',
        'ai_priority_actions'     => 'array',
        'ai_soil_remarks'         => 'array',
        'ai_organic_alternatives' => 'array',
        'ai_practices'            => 'array',
        'ai_monitoring_plan'      => 'array',
        'ai_reminders'            => 'array',
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

    /**
     * Relationship: SoilAnalysis validated by a User (Expert)
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by', 'id');
    }

    /**
     * Conversation thread for this soil analysis.
     */
    // public function conversations()
    // {
    //     return $this->hasMany(SoilAnalysisConversation::class)->orderBy('created_at', 'asc');
    // }

    /**
     * Additional expert comments from multiple experts.
     */
    public function expertComments()
    {
        return $this->hasMany(SoilAnalysisExpertComment::class)->orderBy('created_at', 'asc');
    }
}
