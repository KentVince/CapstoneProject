<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    use HasFactory;

    protected $fillable = ['citymunDesc'];

    public function barangays()
    {
        return $this->hasMany(Barangay::class);
    }

}
