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
use Illuminate\Support\Facades\DB;


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


     /**
     * Change password for mobile app users
     */
    public function changePassword(Request $request)
    {
        try {
            // Validate the incoming request
            $validated = $request->validate([
                'username' => 'required|string',
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:6',
            ]);

            Log::info('Password change attempt', [
                'username' => $validated['username']
            ]);

            // Find user in mobile_users table by username
            $mobileUser = DB::table('mobile_users')
                ->where('username', $validated['username'])
                ->first();

            if (!$mobileUser) {
                Log::warning('User not found in mobile_users table', [
                    'username' => $validated['username']
                ]);
                
                return response()->json([
                    'message' => 'User not found.'
                ], 404);
            }

            // Verify current password
            if (!Hash::check($validated['current_password'], $mobileUser->password)) {
                Log::warning('Incorrect current password', [
                    'username' => $validated['username']
                ]);
                
                return response()->json([
                    'message' => 'Current password is incorrect.'
                ], 401);
            }

            // Update password in mobile_users table
            DB::table('mobile_users')
                ->where('username', $validated['username'])
                ->update([
                    'password' => Hash::make($validated['new_password']),
                    'updated_at' => now(),
                ]);

            Log::info('Password changed successfully', [
                'username' => $validated['username']
            ]);

            return response()->json([
                'message' => 'Password changed successfully.'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in change password', [
                'errors' => $e->errors()
            ]);
            
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error changing password', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'An error occurred while changing the password.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}


