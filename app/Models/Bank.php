<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'compe',
        'ispb',
        'document',
        'long_name',
        'short_name',
        'url',
    ];
}
