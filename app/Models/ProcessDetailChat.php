<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessDetailChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_detail_id',
        'user_id',
        'message',
    ];

    public function detail(): BelongsTo
    {
        return $this->belongsTo(ProcessDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
