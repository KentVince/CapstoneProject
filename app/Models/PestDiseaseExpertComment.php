<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PestDiseaseExpertComment extends Model
{
    protected $table = 'pest_disease_expert_comments';

    protected $fillable = [
        'pest_and_disease_id',
        'user_id',
        'message',
    ];

    public function pestAndDisease()
    {
        return $this->belongsTo(PestAndDisease::class, 'pest_and_disease_id', 'case_id');
    }

    public function expert()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
