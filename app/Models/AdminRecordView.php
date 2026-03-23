<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRecordView extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'record_type', 'record_id', 'viewed_at'];

    protected $casts = ['viewed_at' => 'datetime'];

    public static function markViewed(int $userId, string $recordType, int $recordId): void
    {
        static::firstOrCreate(
            ['user_id' => $userId, 'record_type' => $recordType, 'record_id' => $recordId],
            ['viewed_at' => now()]
        );
    }

    public static function hasViewed(int $userId, string $recordType, int $recordId): bool
    {
        return static::where('user_id', $userId)
            ->where('record_type', $recordType)
            ->where('record_id', $recordId)
            ->exists();
    }
}
