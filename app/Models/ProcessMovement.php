<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_detail_id',
        'code',
        'name',
        'description',
        'date',
        'complements',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(ProcessDetail::class);
    }
}
