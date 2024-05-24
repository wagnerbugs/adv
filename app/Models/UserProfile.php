<?php

namespace App\Models;

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
        'is_lawyer',
        'gender',
        'birth_date',
        'phones',
        'marital_status',
        'education_level',
        'documents',
        'contract',
        'employment_type',
        'cbos',
        'attachments',
        'salaries',
        'hire_date',
        'termination_date',
        'annotations',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'gender' => GenderEnum::class,
            'phones' => 'array',
            'marital_status' => MaritalStatusEnum::class,
            'education_level' => EducationLevelEnum::class,
            'documents' => 'array',
            'employment_type' => EmploymentTypeEnum::class,
            'cbos' => 'array',
            'attachments' => 'array',
            'salaries' => 'array',
            'annotations' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(UserAddress::class, 'user_id', 'user_id');
    }
}
