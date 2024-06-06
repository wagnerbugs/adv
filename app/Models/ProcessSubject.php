<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcessSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_detail_id',
        'code',
        'name',
        'description',
        'rule',
        'article',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(ProcessDetail::class);
    }
}
