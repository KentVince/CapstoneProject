<?php

namespace App\Models;

use App\Models\PersonnelContactId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Farmer extends Model
{
    use HasFactory;
    protected $table = 'farmers';
    protected $guarded = [];



    public function user() : BelongsTo 
    {
        return $this->belongsTo(User::class, 'farmer_id');    
        // return $this->belongsTo(User::class);
    }

    public function contactId() : HasOne
    {    
        return $this->hasOne(PersonnelContactId::class);
    }

}
