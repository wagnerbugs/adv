<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProcessMovement extends Model
{
    use HasFactory;

    protected $filable = [
        'process_id',
        'code',
        'name',
        'description',
        'date',
        'complements',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }
}
