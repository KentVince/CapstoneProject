<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class QrCodeService
{
    public static function generate(string $data, string $filePath): ?string
    {
        try {
            $folder = dirname($filePath);
            if (!Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->makeDirectory($folder);
            }

            $qrCode = new QrCode(
                data: $data,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,
                margin: 10
            );

            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            Storage::disk('public')->put($filePath, $result->getString());

            return $filePath;
        } catch (\Throwable $th) {
            Log::warning("⚠ QR generation failed: {$th->getMessage()}");
            return null;
        }
    }
}
