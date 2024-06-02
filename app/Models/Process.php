<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Process extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'process',
        'process_number',
        'process_digit',
        'process_year',
        'court_code',
        'court_state_code',
        'court_disctric_code',
        'class_code',
        'class_name',
        'class_description',
        'nature',
        'active_pole',
        'passive_pole',
        'rule',
        'article',
        'publish_date',
        'last_modification_date',
        'secrecy_level',
        'movements',
        'subjects',
    ];

    protected function casts(): array
    {
        return [
            'movements' => 'array',
            'subjects' => 'array',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(ProcessMovement::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(ProcessSubject::class);
    }
}
