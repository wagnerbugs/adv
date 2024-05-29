<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OcupationFamily extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
    ];
}
