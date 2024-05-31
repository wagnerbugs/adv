<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StateCourt extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'abbreviation', 'description', 'url', 'is_active'];
}
