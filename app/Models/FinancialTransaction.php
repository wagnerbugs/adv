<?php

namespace App\Models;

use App\Enums\PaymentMethodEnum;
use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'financial_account_id',
        'financial_category_id',
        'status',
        'client_id',
        'process_id',
        'supplier_id',
        'employee_id',
        'note',
        'type',
        'payment_method',
        'amount',
        'modified_by',
        'transaction_date',
        'due_date',
        'payment_date',
        'is_active',
    ];

    protected $casts = [
        'status' => TransactionStatusEnum::class,
        'type' => TransactionTypeEnum::class,
        'payment_method' => PaymentMethodEnum::class,
    ];

    /**
     * Set the amount attribute.
     *
     * @param  float  $value
     * @return void
     */
    public function getAmountAttribute($value)
    {
        if ($this->attributes['type'] === TransactionTypeEnum::EXPENSE->value) {
            return -abs($value);
        }

        return abs($value);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FinancialCategory::class, 'financial_category_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function modifier()
    {
        return $this->belongsTo(User::class, 'modified_by');
    }

    public function files(): HasMany
    {
        return $this->hasMany(FinancialTransactionFile::class, 'financial_transaction_id');
    }
}
