<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements JWTSubject, FilamentUser
{
    use Notifiable;

    protected $fillable = [
        'email',
        'password',
        'name',
        'last_name',
        'birth_date',
        'gender',
        'phone',
        'role_id',
        'confirmed_at',
        'metadata',

        'avatar',
        'bio',
        'specialties',
        'accepts_personal_bookings',
    ];

    protected $casts = [
        'birth_date'    => 'date',
        'confirmed_at'  => 'datetime',
        'metadata'      => 'array',
        'specialties'   => 'array',
    ];

    protected $hidden = ['password'];
    
    public function canAccessFilament(): bool
    {
        return $this->isAdmin();
    }
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar
        ? asset('storage/' . $this->avatar)
        : null;
    }

    // ← JWT: Обязательные методы
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'email' => $this->email,
            'name' => $this->name,
            'role' => $this->role?->name,
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function forms(): HasMany
    {
        return $this->hasMany(Form::class, 'trainer_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'trainer_id');
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function hasRole(string $role): bool
    {
        return $this->role?->name === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}
