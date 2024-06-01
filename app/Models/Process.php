<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Process extends Model
{
    use HasFactory;

    protected $filable = [
        'client_id',
        'process',
        'process_number',
        'process_digit',
        'process_year',
        'court_code',
        'court_state_code',
        'court_disctric_code',
        'class_code',
        'class_name',
        'class_description',
        'nature',
        'active_pole',
        'passive_pole',
        'rule',
        'article',
        'publish_date',
        'secrecy_level',
        'movements',
        'subjects',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
