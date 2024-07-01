<?php

namespace App\Models;

use App\Enums\TypeOfBankAccountEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ClientCompany extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'company',
        'fantasy_name',
        'share_capital',
        'company_size',
        'legal_nature',
        'type',
        'registration_status',
        'registration_date',
        'activity_start_date',
        'main_activity',
        'secondary_activities',
        'state_registrations',
        'partners',
        'phone',
        'phones',
        'email',
        'emails',
        'website',
        'websites',
        'contacts',
        'type_account_bank',
        'bank_name',
        'bank_agency',
        'bank_account',
        'pix',
        'zipcode',
        'street',
        'number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'longitude',
        'latitude',
        'addresses',
        'image',
        'is_active',
    ];

    protected $casts = [
        'secondary_activities' => 'array',
        'state_registrations' => 'array',
        'partners' => 'array',
        'phones' => 'array',
        'emails' => 'array',
        'websites' => 'array',
        'contacts' => 'array',
        'type_account_bank' => TypeOfBankAccountEnum::class,
        'addresses' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function processes(): HasManyThrough
    {
        return $this->hasManyThrough(Process::class, Client::class, 'id', 'client_id', 'client_id', 'id');
    }
}
