<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    use HasFactory;
    protected $table = 'farms';
    protected $guarded = ['farmer_id', 'created_at', 'updated_at',];



    public function farmer()
    {
        return $this->belongsTo(Farmer::class, 'farmer_id'); // 'farmer_id' is the foreign key in the farms table
    }


}
