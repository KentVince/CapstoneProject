<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PestAndDisease extends Model
{
    use HasFactory;

    // Define the table name (if not following Laravel's default conventions)
    protected $table = 'pest_and_disease';

     // Explicitly define the primary key
     protected $primaryKey = 'case_id';

     // If the primary key is not auto-incrementing
     public $incrementing = true;

     // Specify the key type (integer)
     protected $keyType = 'int';


     


    // Mass-assignable fields
    protected $fillable = [

        'farmer_id',
        'farm_id',
        'date_detected',
        'type',
        'name',
        'severity',
        'image_url',
        'diagnosis_result',
        'recommended_treatment',
        'treatment_status',
        'latitude',
        'longtitude',
    ];

    protected $casts = [
        'options' => 'array'
    ];

    /**
     * Relationship: PestAndDisease belongs to a Farmer
     */
    public function farmer()
    {
        return $this->belongsTo(Farmer::class, 'farmer_id', 'id');
    }

    /**
     * Relationship: PestAndDisease belongs to a Farm
     */
    public function farm()
    {
        return $this->belongsTo(Farm::class, 'farm_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(PestAndDiseaseCategory::class, 'category_id');
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            // Generate QR code data
            $qrCodeData = json_encode([
                'Case ID' => $model->case_id,
                'Severity' => $model->severity,
                'Date Detected' => $model->date_detected,
            ]);

            // Generate QR code and save to public disk
            $filePath = "qr-codes/{$model->case_id}.png";
            $qrCode = QrCode::format('png')->size(300)->generate($qrCodeData);
            Storage::disk('public')->put($filePath, $qrCode);

            // Save file path to qr_code field
            $model->qr_code = $filePath;
        });
    }

    

}
