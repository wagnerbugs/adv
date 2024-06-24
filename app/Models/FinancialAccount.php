<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'balance',
        'is_active',
        'is_bank',
        'type_account_bank',
        'bank_name',
        'bank_agency',
        'bank_account',
        'pix',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'financial_account_id');
    }
}
