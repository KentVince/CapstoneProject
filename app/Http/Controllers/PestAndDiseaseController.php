<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PestAndDisease;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PestAndDiseaseController extends Controller
{
    //

      /**
     * Generate a QR code for a specific pest and disease case.
     */
    public function generateQrCode($id)
    {
        $case = PestAndDisease::findOrFail($id);

        // Data to encode in the QR code
        $qrData = [
            'Case ID' => $case->id,
            'Severity' => $case->severity,
            'Date Detected' => $case->date_detected,
            'Location' => $case->location, // Adjust based on your schema
        ];

        // Generate QR Code
        $qrCode = QrCode::size(300)
            ->format('png')
            ->generate(json_encode($qrData));

        // Return the QR code as a response
        return response($qrCode)->header('Content-Type', 'image/png');
    }
}
