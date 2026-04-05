<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

use App\Services\QrCodeService;


class Farmer extends Model
{
    use HasFactory;

    protected $table = 'farmers';

    protected $fillable = [
        'app_no',
        'rsbsa_no',
        'qr_code',
        'user_type',
        'agency',
        'last_name',
        'first_name',
        'middle_name',
        'ext_name',
        'farmer_address_prk',
        'farmer_address_bgy',
        'farmer_address_mun',
        'farmer_address_prv',
        'birthday',
        'gender',
        'contact_num',
        'email_add',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'farmer_id');
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

    public function barangayData()
    {
        return $this->belongsTo(Barangay::class, 'farmer_address_bgy', 'code');
    }

    public function municipalityData()
    {
        return $this->belongsTo(Municipality::class, 'farmer_address_mun', 'code');
    }

    public function getBarangayNameAttribute(): ?string
    {
        return optional($this->barangayData)->barangay;
    }

    public function getMunicipalityNameAttribute(): ?string
    {
        return optional($this->municipalityData)->municipality;
    }

    /*
    |--------------------------------------------------------------------------
    | QR Code Auto-Generation (Endroid)
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::saved(function ($farmer) {
            if (! $farmer->app_no) {
                return;
            }

            $filePath = "farmers_qr/{$farmer->app_no}.png";

            try {
                QrCodeService::generate($farmer->app_no, $filePath);
                $farmer->updateQuietly(['qr_code' => $filePath]);
                Log::info("QR generated successfully for Farmer {$farmer->app_no}");
            } catch (\Throwable $th) {
                Log::warning("QR generation failed for Farmer [{$farmer->id}] {$farmer->app_no}: {$th->getMessage()}");
            }
        });
    }
}
