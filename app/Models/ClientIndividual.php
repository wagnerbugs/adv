<?php

namespace App\Models;

use App\Enums\EducationLevelEnum;
use App\Enums\GenderEnum;
use App\Enums\MaritalStatusEnum;
use App\Enums\TreatmentPronounEnum;
use App\Enums\TypeOfBankAccountEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientIndividual extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'name',
        'phone',
        'phones',
        'email',
        'emails',
        'website',
        'websites',
        'gender',
        'birth_date',
        'marital_status',
        'education_level',
        'documents',
        'father_name',
        'mother_name',
        'nationality',
        'birth_place',
        'workplace',
        'ocupation',
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
        'title' => TreatmentPronounEnum::class,
        'phones' => 'array',
        'emails' => 'array',
        'websites' => 'array',
        'gender' => GenderEnum::class,
        'marital_status' => MaritalStatusEnum::class,
        'education_level' => EducationLevelEnum::class,
        'documents' => 'array',
        'attachments' => 'array',
        'annotations' => 'array',
        'type_account_bank' => TypeOfBankAccountEnum::class,
        'addresses' => 'array',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
