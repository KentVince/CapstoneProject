<?php

namespace App\Services;

use App\Models\User;
use App\Models\AgriculturalProfessional;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

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

        // Ensure the user has the agri_expert role and selected roles so they can access the Filament panel
        $this->ensureAgriExpertRole($user, $professional);

        return $user;
    }

    /**
     * Sync the user's roles to match what was selected on the professional record.
     * `agri_expert` is always included (required by User::canAccessPanel()). Any
     * roles not in the selection are removed so defaults like `panel_user` do
     * not stick around once specific roles are chosen for the professional.
     */
    protected function ensureAgriExpertRole(User $user, AgriculturalProfessional $professional = null): void
    {
        try {
            $selected = $professional && !empty($professional->roles)
                ? $professional->roles
                : [];

            $roles = array_values(array_unique(array_merge(['agri_expert'], $selected)));

            foreach ($roles as $name) {
                Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
            }

            $user->syncRoles($roles);
        } catch (\Throwable $e) {
            Log::error("Failed to sync roles for user {$user->id}: " . $e->getMessage());
        }
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

            // Assign agri_expert role and any selected roles so the professional can access the Filament panel
            $this->ensureAgriExpertRole($user, $professional);

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
