<?php

use App\Models\PestAndDisease;
use App\Helpers\QRCodeGenerator;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Controllers\PestAndDiseaseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('/admin/login');
});

Route::get('/test-qr', function () {
    $data = json_encode([
        'Case ID' => 1,
        'Severity' => 'High',
        'Date Detected' => '2024-12-21',
    ]);

    return QRCodeGenerator::generate($data);
});



// Route::get('/pest-and-disease/{id}/qrcode', function ($id) {
//     $case = PestAndDisease::where('case_id', $id)->firstOrFail(); // Use case_id to find the record

//     // Data to encode in the QR code
//     $qrData = [
//         'Case ID' => $case->case_id,
//         'Severity' => $case->severity,
//         'Date Detected' => $case->date_detected,
//         'Location' => $case->location, // Adjust based on your schema
//     ];

//     // Generate QR Code
//     return response(
//         QrCode::size(300)->format('png')->generate(json_encode($qrData))
//     )->header('Content-Type', 'image/png');
// })->name('pest-disease.qrcode');
