<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Panel;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasRoles;

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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted()
    {
        static::created(function ($user) {
            UserProfile::create([
                'user_id' => $user->id,
            ]);

            UserAddress::create([
                'user_id' => $user->id,
            ]);

            Log::info('User created ' . $user->name . '. By ' . auth()->user()->name);
        });

        static::updated(function ($user) {
            Log::info('User updated ' . $user->name . '. By ' . auth()->user()->name);
        });
    }

    /**
     * Check if the user can access the specified panel.
     *
     * @param Panel $panel The panel to check access for.
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasPermissionTo('access_admin_panel');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(UserAddress::class);
    }
}
