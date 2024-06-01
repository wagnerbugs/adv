<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourtDistrict extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'court',
        'district',
        'description',
        'type',
        'classification',
        'is_active',
    ];
}
