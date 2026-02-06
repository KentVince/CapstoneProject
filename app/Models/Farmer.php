<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;

use Endroid\QrCode\Builder\BuilderRegistry;
use Endroid\QrCode\Builder\BuilderInterface;

use Filament\Notifications\Notification;

use App\Services\QrCodeService;


class Farmer extends Model
{
    use HasFactory;

    protected $table = 'farmers';
    protected $guarded = ['name'];
    protected $fillable = [
        'app_no',
        'user_type',
        'agency',
        'date_of_application',
        'funding_source',
        'lastname',
        'firstname',
        'middlename',
        'municipality',
        'barangay',
        'purok',
        'sex',
        'birthdate',
        'age',
        'civil_status',
        'spouse',
        'ip',
        'pwd',
        'phone_no',
        'email_add',
        'bank_name',
        'bank_account_no',
        'bank_branch',
        'primary_beneficiaries',
        'primary_beneficiaries_age',
        'primary_beneficiaries_relationship',
        'secondary_beneficiaries',
        'secondary_beneficiaries_age',
        'secondary_beneficiaries_relationship',
        'assignee',
        'reason_assignment',
        'crop',
        'province',
        'user_id',
        'qr_code',
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
    return $this->belongsTo(Barangay::class, 'barangay', 'code');
}

public function getBarangayNameAttribute(): ?string
{
    return optional($this->barangayData)->barangay;
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
            // 🧩 Generate and store the QR
            QrCodeService::generate($farmer->app_no, $filePath);

            // Save the relative path in the database
            $farmer->updateQuietly(['qr_code' => $filePath]);

            Log::info("✅ QR generated successfully for Farmer {$farmer->app_no}");
        } catch (\Throwable $th) {
            Log::warning("⚠ QR generation failed for Farmer [{$farmer->id}] {$farmer->app_no}: {$th->getMessage()}");
        }
    });
}
}
