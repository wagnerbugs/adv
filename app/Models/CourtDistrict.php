<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourtDistrict extends Model
{
    use HasFactory;

    protected $fillable = [
        'court',
        'state',
        'city',
        'service_number',
        'service_name',
        'district_code',
        'type',
        'unit',
        'phone',
        'email',
        'address',
        'latitude',
        'longitude',
        'is_active',
    ];

    public function processes(): HasMany
    {
        return $this->hasMany(Process::class, 'court_district_code', 'district_code');
    }
}
