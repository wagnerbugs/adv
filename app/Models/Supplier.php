<?php

namespace App\Models;

use App\Enums\ClientTypeEnum;
use App\Enums\TypeOfBankAccountEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'document',
        'name',
        'phone',
        'phones',
        'email',
        'emails',
        'website',
        'websites',
        'contacts',
        'attachments',
        'annotations',
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
        'type' => ClientTypeEnum::class,
        'phones' => 'array',
        'emails' => 'array',
        'websites' => 'array',
        'contacts' => 'array',
        'attachments' => 'array',
        'annotations' => 'array',
        'type_account_bank' => TypeOfBankAccountEnum::class,
        'addresses' => 'array',
    ];
}
