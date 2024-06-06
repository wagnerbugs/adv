<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourtState extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'court',
        'state',
        'description',
        'url',
        'is_active',
    ];

    public function processes(): HasMany
    {
        return $this->hasMany(Process::class, 'court_state_code', 'code');
    }
}
