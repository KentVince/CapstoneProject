<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Welcome extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';
    protected static string $view = 'filament.pages.welcome';
    protected static ?string $slug = 'welcome';
    protected static ?string $title = 'Welcome';

    /**
     * Only show this page for panel_user (default users)
     */
    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        
        // Show only for panel users
        return $user->hasRole('panel_user');
    }

    /**
     * Restrict access to panel users only
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        
        return $user->hasRole('panel_user');
    }
}
