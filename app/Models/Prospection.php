<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function individual(): HasOne
    {
        return $this->hasOne(ProspectionIndividual::class);
    }

    public function company(): HasOne
    {
        return $this->hasOne(ProspectionCompany::class);
    }

    public function process(): HasOne
    {
        return $this->hasOne(ProspectionProcess::class);
    }
}
