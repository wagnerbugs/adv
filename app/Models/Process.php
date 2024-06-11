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
        'court_district_code',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(ProcessDetail::class);
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class, 'court_code', 'code');
    }

    public function courtState(): BelongsTo
    {
        return $this->belongsTo(CourtState::class, 'court_state_code', 'code');
    }

    public function courtDistrict(): BelongsTo
    {
        return $this->belongsTo(CourtDistrict::class, 'court_district_code', 'code');
    }

    public function chats(): HasMany
    {
        return $this->hasMany(ProcessChat::class);
    }
}
