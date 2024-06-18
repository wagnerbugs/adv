<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_id',
        'user_id',
        'message',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
