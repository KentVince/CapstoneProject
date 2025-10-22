<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SoilAnalysis;

class SoilAnalysisController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'farmer_id'      => 'required|integer',
            'farm_id'        => 'nullable|integer',
            'farm_name'      => 'nullable|string',
            'crop_variety'   => 'nullable|string',
            'soil_type'      => 'nullable|string',
            'date_collected' => 'nullable|date',
            'location'       => 'nullable|string',
            'ref_no'         => 'nullable|string',
            'submitted_by'   => 'nullable|string',
            'date_submitted' => 'nullable|string',
            'date_analyzed'  => 'nullable|string',
            'lab_no'         => 'nullable|string',
            'field_no'       => 'nullable|string',
            'ph_level'       => 'nullable|numeric',
            'nitrogen'       => 'nullable|numeric',
            'phosphorus'     => 'nullable|numeric',
            'potassium'      => 'nullable|numeric',
            'organic_matter' => 'nullable|numeric',
            'recommendation' => 'nullable|string',
        ]);

        $analysis = SoilAnalysis::create($data);

        return response()->json([
            'message' => '✅ Soil analysis saved successfully!',
            'soil_analysis_id' => $analysis->id,
        ]);
    }
}
