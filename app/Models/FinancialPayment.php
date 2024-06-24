<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'process_id',
        'amount',
        'entry_amount',
        'entry_payment_method',
        'entry_date',
        'installments',
        'installment_amount',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function installments(): HasMany
    {
        return $this->hasMany(FinancialPaymentInstallment::class);
    }
}
