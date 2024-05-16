<?php

namespace App\Models;

use App\Casts\Json;
use App\Enums\EducationLevelEnum;
use App\Enums\EmploymentTypeEnum;
use App\Enums\GenderEnum;
use App\Enums\MaritalStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gender',
        'birth_date',
        'phone',
        'contract',
        'employment_type',
        'cbo_code',
        'cbo_title',
        'cbo_description',
        'documents',
        'attachments',
        'marital_status',
        'education_level',
        'hire_date',
        'termination_date',
    ];

    protected function casts(): array
    {
        return [
            'gender' => GenderEnum::class,
            'phone' => 'array',
            'contract_type' => EmploymentTypeEnum::class,
            'documents' => 'array',
            'attachments' => 'array',
            'marital_status' => MaritalStatusEnum::class,
            'education_level' => EducationLevelEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
