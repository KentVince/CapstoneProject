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
    protected $guarded = ['name'];



    // protected $fillable = [

    //     'app_no',
    //     'crop',
    //     'funding_source',
    //     'date_of_application',
    //     'lastname',
    //     'firstname',
    //     'middlename',
    //     'purok',
    //     'barangay',
    //     'municipality',
    //     'province',
    //     'phone_no',
    //     'sex',
    //     'birthdate',
    //     'age',
    //     'civil_status',
    //     'pwd',
    //     'ip',
    //     'bank_name',
    //     'bank_account_no',
    //     'bank_branch',
    //     'spouse',
    //     'primary_beneficiaries',
    //     'primary_beneficiaries_age',
    //     'primary_beneficiaries_relationship',
    //     'secondary_beneficiaries',
    //     'secondary_beneficiaries_age',
    //     'secondary_beneficiaries_relationship',
    //     'assignee',
    //     'reason_assignment',
    //     'user_id',

    //  ];



    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
        // return $this->belongsTo(User::class);
    }

    public function soilAnalyses()
{
    return $this->hasMany(SoilAnalysis::class, 'farmer_id', 'id');
}

public function pestAndDiseases()
{
    return $this->hasMany(PestAndDisease::class, 'farmer_id', 'id');
}

public function farms()
{
    return $this->hasMany(Farm::class, 'farmer_id', 'id');
}

public function farm(): HasOne
{
    return $this->hasOne(Farm::class, 'farmer_id', 'id');
}

}
