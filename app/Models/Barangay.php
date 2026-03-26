<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangay extends Model
{
    use HasFactory;

    protected $table = 'barangays';
    protected $fillable = ['barangay', 'mun_filter','code', 'name'];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }


    public function farmers()
    {
        return $this->hasMany(Farmer::class, 'barangay', 'code');
    }


}
