<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProspectionProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'prospection_id',
        'process',
        'process_number',
        'process_digit',
        'process_year',
        'court_code',
        'court_state_code',
        'court_district_code',
        'process_api_id',
        'class_code',
        'class_name',
        'class_description',
        'nature',
        'active_pole',
        'passive_pole',
        'rule',
        'article',
        'last_modification_date',
        'grade',
        'publish_date',
        'movements',
        'secrecy_level',
        'judging_code',
        'judging_name',
        'subjects',
    ];

    public function prospection(): BelongsTo
    {
        return $this->belongsTo(Prospection::class);
    }
}
