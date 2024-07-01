<?php

namespace App\Models;

use App\Enums\ClientTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Process extends Model
{
    use HasFactory;

    protected $fillable = [
        'clients',
        'process',
        'process_number',
        'process_digit',
        'process_year',
        'court_code',
        'court_state_code',
        'court_district_code',
    ];

    protected $casts = [
        'clients' => 'array'
    ];

    public function client(): BelongsToMany
    {
        return $this->belongsToMany(Client::class);
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

    public function getNameAttribute()
    {
        if ($this->client->type === ClientTypeEnum::INDIVIDUAL) {
            return $this->individual->name;
        }

        if ($this->client->type === ClientTypeEnum::COMPANY) {
            return $this->company->company;
        }

        return null;
    }
}
