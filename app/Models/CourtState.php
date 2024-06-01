<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourtState extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'court',
        'state',
        'description',
        'url',
        'is_active',
    ];
}
