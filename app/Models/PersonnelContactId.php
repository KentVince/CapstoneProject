<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonnelContactId extends Model
{
    use HasFactory;

    protected $guarded = ['farmer_id', 'created_at', 'updated_at',];
}
