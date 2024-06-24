<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinancialPaymentInstallment extends Model
{
    use HasFactory;

    protected $fillable = [
        'financial_payment_id',
        'number',
        'amount',
        'due_date',
        'status',
        'description',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(FinancialPayment::class);
    }
}
