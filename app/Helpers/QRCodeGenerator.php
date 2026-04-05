<?php
namespace App\Helpers;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class QRCodeGenerator
{
    /**
     * Generate a QR code and save it to a public directory.
     *
     * @param string $data The data to encode in the QR code.
     * @param int $size The size of the QR code.
     * @return string The URL of the generated QR code.
     */
    public static function generate(string $data, int $size = 300): string
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($data)
            ->size($size)
            ->build();

        // Save QR Code to public storage
        $filePath = 'qr-codes/' . uniqid() . '.png';
        $result->saveToFile(public_path($filePath));
        
        // Return the public URL of the QR Code
        return asset($filePath);
    }
}
