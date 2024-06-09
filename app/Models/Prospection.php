<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use App\Enums\ProspectionStatusEnum;
use App\Enums\ProspectionReactionEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prospection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'cnpj',
        'cpf',
        'process',
        'complements',
        'attachments',
        'annotations',
        'status',
        'reaction',
    ];

    protected $casts = [
        'complements' => 'array',
        'attachments' => 'array',
        'annotations' => 'array',
        'status' => ProspectionStatusEnum::class,
        'reaction' => ProspectionReactionEnum::class,
    ];


    protected static function booted(): void
    {
        static::created(function ($prospection) {
            ProspectionCompany::create([
                'prospection_id' => $prospection->id,
            ]);

            ProspectionIndividual::create([
                'prospection_id' => $prospection->id,
            ]);

            Log::info('User created ' . $prospection->name . '. By ' . auth()->user()->name);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): HasOne
    {
        return $this->hasOne(ProspectionCompany::class);
    }

    public function individual(): HasOne
    {
        return $this->hasOne(ProspectionIndividual::class);
    }

    public function processes(): HasMany
    {
        return $this->hasMany(ProspectionProcess::class);
    }
}
