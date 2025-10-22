<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PestAndDisease;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Farmer;
use Illuminate\Support\Facades\Hash;

class MobileController extends Controller
{

    public function loginTest()
{
    $farmer = \App\Models\Farmer::with('farm')->first();
    return response()->json($farmer);
}


    public function login(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid username or password.'
            ], 401);
        }

        // ✅ Load farmer + farm relationship
        $farmer = Farmer::with('farm')
            ->where('id', $user->farmer_id)
            ->first();

        return response()->json([
            'user' => $user,
            'farmer' => $farmer,
        ], 200);
    }

    public function checkAppNo(Request $request)
    {
        $appNo = $request->app_no;

        // ✅ Load the farmer by app number + farm relationship
        $farmer = Farmer::with('farm')->where('app_no', $appNo)->first();

        if (!$farmer) {
            return response()->json(['message' => 'Application number not found'], 404);
        }

        $user = $farmer->user; // Assuming you have farmer->user() relation

        return response()->json([
            'farmer' => $farmer,
            'user' => $user,
        ]);
    }


}


