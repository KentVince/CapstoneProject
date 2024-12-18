<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Farmername extends Model
{
  

    use HasFactory;
    protected $guarded = ['farmer_id', 'created_at', 'updated_at',];

    public function farmer() : BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

}
