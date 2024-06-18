<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Court extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'is_active',
    ];

    public function processes(): HasMany
    {
        return $this->hasMany(Process::class, 'court_code', 'code');
    }
}
