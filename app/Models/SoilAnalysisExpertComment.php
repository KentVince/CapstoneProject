<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoilAnalysisExpertComment extends Model
{
    protected $table = 'soil_analysis_expert_comments';

    protected $fillable = [
        'soil_analysis_id',
        'user_id',
        'message',
    ];

    public function soilAnalysis()
    {
        return $this->belongsTo(SoilAnalysis::class);
    }

    public function expert()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
