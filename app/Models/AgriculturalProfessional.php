<?php

namespace App\Models;

use App\Services\QrCodeService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AgriculturalProfessional extends Model
{
    use HasFactory;

    protected $table = 'agricultural_professionals';

    protected $fillable = [
        'app_no',
        'agency',
        'lastname',
        'firstname',
        'middlename',
        'sex',
        'birthdate',
        'age',
        'municipality',
        'barangay',
        'phone_no',
        'email_add',
        'qr_code',
        'user_id',
    ];

    protected $casts = [
        'birthdate' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if this professional has a user account
     */
    public function hasUserAccount(): bool
    {
        return $this->user_id !== null;
    }

    /**
     * Check if the professional can login with a specific password
     */
    public function canLoginWithPassword(string $password): bool
    {
        if (!$this->user) {
            return false;
        }

        return app(\App\Services\AgriculturalProfessionalAuthService::class)
            ->verifyPassword($this, $password);
    }

    protected static function booted()
    {
        static::saved(function ($professional) {
            if (! $professional->app_no) {
                return;
            }

            $filePath = "professionals_qr/{$professional->app_no}.png";

            try {
                QrCodeService::generate($professional->app_no, $filePath);

                $professional->updateQuietly(['qr_code' => $filePath]);

                Log::info("QR generated successfully for Professional {$professional->app_no}");
            } catch (\Throwable $th) {
                Log::warning("QR generation failed for Professional [{$professional->id}] {$professional->app_no}: {$th->getMessage()}");
            }
        });
    }
}
