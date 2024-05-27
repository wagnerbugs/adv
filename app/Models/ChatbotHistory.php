<?php

namespace App\Models;

use App\Enums\ChatbotStepsEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'chatbot_user_id',
        'step',
    ];

    protected function casts(): array
    {
        return [
            'step' => ChatbotStepsEnum::class,
        ];
    }

    public function chatbotUser(): BelongsTo
    {
        return $this->belongsTo(ChatbotUser::class);
    }
}
