<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', //
        'title', //
        'description', //
        'color', //
        'professionals', //
        'process_id', //
        'client_id', //
        'starts_at',
        'ends_at',
        'is_juridical',
        'is_private',
    ];

    protected $casts = [
        'professionals' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(EventTag::class, 'event_event_tag');
    }

    public function getTagColor()
    {
        return $this->tags->pluck('color')->toArray();
    }
}
