<?php

namespace App\Services;

use App\Models\User;
use App\Models\AgriculturalProfessional;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AgriculturalProfessionalAuthService
{
    /**
     * Authenticate an agricultural professional by email and create/update user account
     * 
     * @param string $email
     * @param string $password
     * @return User|null
     */
    public function authenticate(string $email, string $password): ?User
    {
        // Find agricultural professional by email_add
        $professional = AgriculturalProfessional::where('email_add', $email)->first();

        if (!$professional) {
            return null;
        }

        // If the professional doesn't have a user account, create one
        if (!$professional->user_id) {
            $user = $this->createUserForProfessional($professional, $password);
            if (!$user) {
                return null;
            }
            $professional->update(['user_id' => $user->id]);
        } else {
            $user = $professional->user;
        }

        // Verify the password
        if (!Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }

    /**
     * Generate default password for agricultural professional
     * Format: app_no@Ca_2026 (Using app_no as base)
     * 
     * @param AgriculturalProfessional $professional
     * @return string
     */
    public function generateDefaultPassword(AgriculturalProfessional $professional): string
    {
        $year = date('Y');
        return $professional->app_no . '@Ca_' . $year;
    }

    /**
     * Create a user account for an agricultural professional
     * 
     * @param AgriculturalProfessional $professional
     * @param string $password
     * @return User|null
     */
    protected function createUserForProfessional(AgriculturalProfessional $professional, string $password): ?User
    {
        try {
            // Build full name
            $fullName = trim(
                $professional->firstname . ' ' .
                ($professional->middlename ? $professional->middlename . ' ' : '') .
                $professional->lastname
            );

            $user = User::create([
                'name' => $fullName,
                'email' => $professional->email_add,
                'password' => Hash::make($password),
            ]);

            Log::info("User account created for agricultural professional: {$professional->app_no}");

            return $user;
        } catch (\Exception $e) {
            Log::error("Failed to create user for agricultural professional: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if email belongs to an agricultural professional
     * 
     * @param string $email
     * @return bool
     */
    public function isProfessionalEmail(string $email): bool
    {
        return AgriculturalProfessional::where('email_add', $email)->exists();
    }

    /**
     * Verify if a professional can login with the provided password
     * 
     * @param AgriculturalProfessional $professional
     * @param string $password
     * @return bool
     */
    public function verifyPassword(AgriculturalProfessional $professional, string $password): bool
    {
        if (!$professional->user) {
            return false;
        }

        return Hash::check($password, $professional->user->password);
    }
}
