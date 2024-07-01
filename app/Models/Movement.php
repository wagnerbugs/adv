<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movement extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_id',
        'organ',
        'process',
        'judicial_class',
        'judicial_event',
        'authors',
        'defendants',
        'event_date',
    ];

    public function email(): BelongsTo
    {
        return $this->belongsTo(Email::class);
    }
}
