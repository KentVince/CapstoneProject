<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Lab404\Impersonate\Models\Impersonate;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;


class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPanelShield, Impersonate;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    // public function farmer() : HasOne {
    //     return $this->hasOne(Farmer::class, 'id', 'farmer_id');
    // }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(['super_admin', 'admin', 'panel_user']);
    }


    public function farmer()
    {
        return $this->belongsTo(Farmer::class, 'farmer_id');
    }

    public function agriculturalProfessional()
    {
        return $this->hasOne(AgriculturalProfessional::class);
    }

    /**
     * Check if user is an agricultural professional
     */
    public function isAgriculturalProfessional(): bool
    {
        return $this->agriculturalProfessional !== null;
    }

    /**
     * Check if user is a farmer
     */
    public function isFarmer(): bool
    {
        return $this->farmer_id !== null;
    }
}
