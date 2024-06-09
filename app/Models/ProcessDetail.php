<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProcessDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_id',
        'process_api_id',
        'professionals',
        'judging_code',
        'judging_name',
        'class_code',
        'class_name',
        'class_description',
        'nature',
        'active_pole',
        'passive_pole',
        'rule',
        'article',
        'publish_date',
        'last_modification_date',
        'secrecy_level',
        'grade',
        'movements',
        'subjects',
        'attachments',
        'annotations',
    ];

    protected $casts = [
        'professionals' => 'array',
        'movements' => 'array',
        'subjects' => 'array',
        'attachments' => 'array',
        'annotations' => 'array',
    ];


    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(ProcessMovement::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(ProcessSubject::class);
    }

    public function chat(): HasMany
    {
        return $this->hasMany(ProcessDetailChat::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->wherePivotIn('priority', 'professionals');
    }
}
