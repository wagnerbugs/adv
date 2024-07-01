<?php

namespace App\Models;

use App\Enums\ClientTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'document',
    ];

    protected $casts = [
        'type' => ClientTypeEnum::class,
    ];

    protected static function booted()
    {
        static::created(function ($client) {

            if ($client->type === ClientTypeEnum::INDIVIDUAL) {
                $client->individual()->create();
            } elseif ($client->type === ClientTypeEnum::COMPANY) {
                $client->company()->create();
            }

            Log::info('Client created ' . $client->document . '. By ' . auth()->user()->name);
        });

        static::updated(function ($user) {
            Log::info('Client updated ' . $user->document . '. By ' . auth()->user()->name);
        });
    }

    public function individual(): HasOne
    {
        return $this->hasOne(ClientIndividual::class);
    }

    public function company(): HasOne
    {
        return $this->hasOne(ClientCompany::class);
    }

    public function processes(): HasMany
    {
        return $this->hasMany(Process::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(FinancialPayment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ClientAttachment::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ClientNote::class);
    }

    public function getNameAttribute()
    {
        if ($this->type === ClientTypeEnum::COMPANY) {
            return $this->company->company;
        }

        if ($this->type === ClientTypeEnum::INDIVIDUAL) {
            return $this->individual->name;
        }

        return null;
    }

    public function getIsActiveAttribute()
    {
        if ($this->type === ClientTypeEnum::INDIVIDUAL) {
            return $this->individual->is_active;
        }

        if ($this->type === ClientTypeEnum::COMPANY) {
            return $this->company->is_active;
        }

        return null;
    }

    public function setIsActiveAttribute($value)
    {
        if ($this->type === ClientTypeEnum::INDIVIDUAL) {
            $this->individual->is_active = $value;
            $this->individual->save();
        }

        if ($this->type === ClientTypeEnum::COMPANY) {
            $this->company->is_active = $value;
            $this->company->save();
        }
    }
}
