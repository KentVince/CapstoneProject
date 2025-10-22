<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PestAndDisease;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PestAndDiseaseController extends Controller
{
    //

      /**
     * Generate a QR code for a specific pest and disease case.
     */
    // public function generateQrCode($id)
    // {
    //     $case = PestAndDisease::findOrFail($id);

    //     // Data to encode in the QR code
    //     $qrData = [
    //         'Case ID' => $case->id,
    //         'Severity' => $case->severity,
    //         'Date Detected' => $case->date_detected,
    //         'Location' => $case->location, // Adjust based on your schema
    //     ];

    //     // Generate QR Code
    //     $qrCode = QrCode::size(300)
    //         ->format('png')
    //         ->generate(json_encode($qrData));

    //     // Return the QR code as a response
    //     return response($qrCode)->header('Content-Type', 'image/png');
    // }

   // ✅ Save detection from Flutter app
public function store(Request $request)
{
    try {
        // Validate inputs
        $validated = $request->validate([
            'pest' => 'required|string|max:255',
            'confidence' => 'nullable|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'area' => 'nullable|string|max:255',
            'date_detected' => 'nullable|string|max:255',
            'severity' => 'nullable|string|max:255',
            'image' => 'nullable|file|image|max:5120', // 👈 expect "image" from Flutter
        ]);

        // Handle file upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'pest_' . time() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('pests', $filename, 'public'); // stored in storage/app/public/pests
        }

        // Save record in DB (store in "image_url" field)
        $pest = \App\Models\PestAndDisease::create([
            'pest' => $validated['pest'],
            'confidence' => $validated['confidence'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'area' => $validated['area'] ?? null,
            'date_detected' => $validated['date_detected'] ?? now(),
            'severity' => $validated['severity'] ?? 'Medium',
            'image_url' => $imagePath ? '/storage/' . $imagePath : null, // 👈 save path here
        ]);

        return response()->json([
            'message' => 'Detection saved successfully.',
            'data' => $pest
        ], 201);

    } catch (\Exception $e) {
        \Log::error('Error storing detection: ' . $e->getMessage());
        return response()->json([
            'error' => 'Failed to store detection',
            'details' => $e->getMessage()
        ], 500);
    }
}


    public function index()
    {
        $detections = PestAndDisease::latest()->get();
        return response()->json($detections);
    }


}
