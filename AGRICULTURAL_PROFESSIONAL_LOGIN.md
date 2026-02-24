# Agricultural Professional Login System

## Overview

Agricultural professionals can now login to the web app using their email address from the `agricultural_professionals` table and a default password. When they login, their user account is automatically created in the system if it doesn't exist yet.

## How It Works

### 1. **Default Password Format**
The default password for each agricultural professional is generated based on their **Application Number (app_no)**:

```
Format: {app_no}@Ca_{YEAR}

Example: If app_no = "2024-001", password = "2024-001@Ca_2026"
```

### 2. **Automatic User Account Creation**
When an agricultural professional logs in for the first time:
- The system checks if they exist in the `agricultural_professionals` table
- If they don't have a linked user account, one is created automatically
- The account is saved to the `users` table with:
  - Email: from `agricultural_professionals.email_add`
  - Password: hashed version of the default password
  - Name: constructed from firstname, middlename, and lastname

### 3. **Login Process**
Professionals can login at the admin panel (`/admin/login`) with:
- **Login**: Their email address (from `email_add` field)
- **Password**: Their default password (app_no format)

## Setup Instructions

### Initial Account Setup

To setup accounts for all agricultural professionals without existing user accounts, run:

```bash
php artisan app:setup-professional-accounts --all
```

This command will:
- Find all agricultural professionals without `user_id`
- Create user accounts for them
- Display their default passwords
- Link them to the agricultural professional records

### Setup Single Professional Account

To setup an account for a specific professional by email:

```bash
php artisan app:setup-professional-accounts --email=john@example.com
```

### Reset All Professional Passwords

If you need to reset all passwords to their defaults:

```bash
php artisan app:setup-professional-accounts --fix-passwords
```

This will regenerate and update all passwords to their default format based on current app_no.

### Interactive Setup

To run the setup in interactive mode:

```bash
php artisan app:setup-professional-accounts
```

You'll be prompted with options to:
1. Setup accounts for all professionals without users
2. Setup account for a specific professional
3. Reset passwords for all professionals

## User Methods

### Check if Professional Has Account
```php
$professional = AgriculturalProfessional::find($id);
if ($professional->hasUserAccount()) {
    // Has a linked user account
}
```

### Check if User is Professional
```php
$user = Auth::user();
if ($user->isAgriculturalProfessional()) {
    // This is a professional user
}
```

### Get Professional from User
```php
$user = Auth::user();
$professional = $user->agriculturalProfessional;
```

### Verify Password
```php
$professional = AgriculturalProfessional::find($id);
if ($professional->canLoginWithPassword($password)) {
    // Password is correct
}
```

## Service Usage

You can also use the `AgriculturalProfessionalAuthService` directly:

```php
use App\Services\AgriculturalProfessionalAuthService;

$authService = new AgriculturalProfessionalAuthService();

// Authenticate a professional
$user = $authService->authenticate('email@example.com', 'password');

// Check if email is a professional email
$isProfessional = $authService->isProfessionalEmail('email@example.com');

// Get default password for a professional
$professional = AgriculturalProfessional::find($id);
$defaultPassword = $authService->generateDefaultPassword($professional);
```

## Database Changes

The system uses the existing columns in the `agricultural_professionals` table:
- **email_add**: Email address for login
- **user_id**: Foreign key to the `users` table
- **app_no**: Used to generate default password

No database migrations are required - the system works with your existing tables.

## Security Notes

1. **Default Password**: Make sure to communicate the default password format to your administrators
2. **First Login**: Encourage professionals to change their password after first login
3. **Email Uniqueness**: Each agricultural professional should have a unique email address
4. **Account Linking**: Accounts are linked via `user_id` - don't manually modify this without understanding the implications

## Troubleshooting

### Professional Can't Login
1. Check if `email_add` exists and matches exactly
2. Verify the password format: `app_no@Ca_2026`
3. Run `php artisan app:setup-professional-accounts --email=their@email.com` to ensure account is created
4. Check if the account was created in the `users` table

### Wrong Password
If the professional was created before the default password system, they may have a different password. Reset it:

```bash
php artisan app:setup-professional-accounts --email=their@email.com
```

And confirm when prompted to reset the password.

### Duplicate Email
If an email exists in both `users` and `agricultural_professionals`, make sure they're properly linked via `user_id`.

## Architecture

The implementation includes:

1. **AgriculturalProfessionalAuthService** (`app/Services/AgriculturalProfessionalAuthService.php`)
   - Handles authentication logic
   - Manages user account creation
   - Generates and verifies passwords

2. **Modified Login** (`app/Filament/Pages/Auth/Login.php`)
   - Intercepts email-based login attempts
   - Checks if email belongs to a professional
   - Uses custom authentication service if applicable
   - Falls back to standard admin authentication

3. **Setup Command** (`app/Console/Commands/SetupAgriculturalProfessionalAccounts.php`)
   - Bulk account creation
   - Password management
   - Interactive setup mode

4. **Model Methods**
   - `AgriculturalProfessional::hasUserAccount()`
   - `AgriculturalProfessional::canLoginWithPassword()`
   - `User::isAgriculturalProfessional()`
   - `User::isFarmer()`
