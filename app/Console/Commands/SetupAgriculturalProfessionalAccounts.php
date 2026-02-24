<?php

namespace App\Console\Commands;

use App\Models\AgriculturalProfessional;
use App\Models\User;
use App\Services\AgriculturalProfessionalAuthService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetupAgriculturalProfessionalAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup-professional-accounts {--all : Setup accounts for all professionals without users} {--email= : Setup account for a specific email} {--fix-passwords : Reset all professional user passwords to defaults}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Setup user accounts for agricultural professionals with default passwords';

    protected $authService;

    public function __construct()
    {
        parent::__construct();
        $this->authService = new AgriculturalProfessionalAuthService();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('all')) {
            return $this->setupAllProfessionals();
        }

        if ($this->option('email')) {
            return $this->setupProfessionalByEmail($this->option('email'));
        }

        if ($this->option('fix-passwords')) {
            return $this->resetAllPasswords();
        }

        // Interactive mode
        return $this->interactiveSetup();
    }

    /**
     * Setup accounts for all professionals without users
     */
    protected function setupAllProfessionals(): int
    {
        $professionals = AgriculturalProfessional::whereNull('user_id')->get();

        if ($professionals->isEmpty()) {
            $this->info('✓ All agricultural professionals already have user accounts.');
            return self::SUCCESS;
        }

        $this->info("Setting up accounts for {$professionals->count()} agricultural professionals...\n");

        $successCount = 0;
        $failCount = 0;

        foreach ($professionals as $professional) {
            $defaultPassword = $this->authService->generateDefaultPassword($professional);
            
            try {
                $user = User::create([
                    'name' => trim(
                        $professional->firstname . ' ' .
                        ($professional->middlename ? $professional->middlename . ' ' : '') .
                        $professional->lastname
                    ),
                    'email' => $professional->email_add,
                    'password' => Hash::make($defaultPassword),
                ]);

                $professional->update(['user_id' => $user->id]);
                $successCount++;

                $this->line("✓ {$professional->app_no} ({$professional->email_add})");
                $this->line("  Default Password: {$defaultPassword}\n");
            } catch (\Exception $e) {
                $failCount++;
                $this->error("✗ Failed to create user for {$professional->app_no}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info("Setup completed: {$successCount} successful, {$failCount} failed.");

        return $failCount === 0 ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Setup account for a specific professional by email
     */
    protected function setupProfessionalByEmail(string $email): int
    {
        $professional = AgriculturalProfessional::where('email_add', $email)->first();

        if (!$professional) {
            $this->error("Agricultural professional with email '{$email}' not found.");
            return self::FAILURE;
        }

        if ($professional->user_id) {
            $this->warn("This professional already has a user account (User ID: {$professional->user_id}).");
            
            if (!$this->confirm('Do you want to reset the password?')) {
                return self::SUCCESS;
            }
        }

        $defaultPassword = $this->authService->generateDefaultPassword($professional);

        try {
            if (!$professional->user_id) {
                $user = User::create([
                    'name' => trim(
                        $professional->firstname . ' ' .
                        ($professional->middlename ? $professional->middlename . ' ' : '') .
                        $professional->lastname
                    ),
                    'email' => $professional->email_add,
                    'password' => Hash::make($defaultPassword),
                ]);

                $professional->update(['user_id' => $user->id]);
                $this->info("✓ User account created successfully.");
            } else {
                // Reset password
                $professional->user->update([
                    'password' => Hash::make($defaultPassword),
                ]);
                $this->info("✓ Password reset successfully.");
            }

            $this->newLine();
            $this->info("Application No.: {$professional->app_no}");
            $this->info("Email: {$professional->email_add}");
            $this->info("Default Password: {$defaultPassword}");

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to setup account: " . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Reset all professional user passwords to defaults
     */
    protected function resetAllPasswords(): int
    {
        $professionals = AgriculturalProfessional::whereNotNull('user_id')->get();

        if ($professionals->isEmpty()) {
            $this->info('No agricultural professionals with user accounts found.');
            return self::SUCCESS;
        }

        if (!$this->confirm("Reset passwords for {$professionals->count()} professionals?")) {
            $this->info('Operation cancelled.');
            return self::SUCCESS;
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($professionals as $professional) {
            $defaultPassword = $this->authService->generateDefaultPassword($professional);

            try {
                $professional->user->update([
                    'password' => Hash::make($defaultPassword),
                ]);
                $successCount++;
                $this->line("✓ {$professional->app_no} - {$defaultPassword}");
            } catch (\Exception $e) {
                $failCount++;
                $this->error("✗ Failed to reset password for {$professional->app_no}");
            }
        }

        $this->newLine();
        $this->info("Password reset completed: {$successCount} successful, {$failCount} failed.");

        return $failCount === 0 ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Interactive setup mode
     */
    protected function interactiveSetup(): int
    {
        $this->info('Agricultural Professional Account Setup\n');

        $choice = $this->choice(
            'What would you like to do?',
            [
                'Setup accounts for all professionals without users',
                'Setup account for specific professional',
                'Reset passwords for all professionals',
                'Exit',
            ]
        );

        return match ($choice) {
            'Setup accounts for all professionals without users' => $this->setupAllProfessionals(),
            'Setup account for specific professional' => $this->setupProfessionalByEmail($this->ask('Enter professional email:')),
            'Reset passwords for all professionals' => $this->resetAllPasswords(),
            default => self::SUCCESS,
        };
    }
}
