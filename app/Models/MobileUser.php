<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class MobileUser extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'type',
        'app_no',
        'username',
        'password',
        'farm_name',
        'barangay',
        'contact_no',
        'email',
        'fcm_token',
        'farm_location',
        'farm_size',
    ];

    protected $hidden = ['password'];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class, 'farmer_id');
    }
}
