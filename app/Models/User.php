<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, HasRoles, Notifiable;

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function booted()
    {
        static::created(function ($user) {
            UserProfile::create([
                'user_id' => $user->id,
            ]);

            UserAddress::create([
                'user_id' => $user->id,
            ]);

            Log::info('User created '.$user->name.'. By '.auth()->user()->name);
        });

        static::updated(function ($user) {
            Log::info('User updated '.$user->name.'. By '.auth()->user()->name);
        });
    }

    /**
     * Check if the user can access the specified panel.
     *
     * @param  Panel  $panel  The panel to check access for.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasPermissionTo('access_admin_panel');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->profile->avatar === null) {
            return 'https://ui-avatars.com/api/?name='.str_replace(' ', '+', $this->name);
        }

        return Storage::url($this->profile->avatar);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(UserAddress::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(UserAttachment::class);
    }
}
