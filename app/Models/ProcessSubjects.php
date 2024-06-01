<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessSubjects extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_id',
        'code',
        'name',
        'description',
        'rule',
        'article',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }
}
