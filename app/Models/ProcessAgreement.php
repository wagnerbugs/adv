<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProcessAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'processes',
        'user_id',
        'dealer',
        'company',
        'phones',
        'emails',
        'notes',
        'amount',
    ];

    protected $casts = [
        'phones' => 'array',
        'emails' => 'array',
    ];

    public function process(): BelongsToMany
    {
        return $this->belongsToMany(Process::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
