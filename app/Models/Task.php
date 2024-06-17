<?php

namespace App\Models;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusesEnum;
use Spatie\EloquentSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model implements Sortable
{
    use HasFactory, SortableTrait;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'color',
        'professionals',
        'process_id',
        'client_id',
        'starts_at',
        'ends_at',
        'priority',
        'order_columm',
        'is_private',
        'is_active',
        'is_urgent',
        'status',
        'deadline_at',
        'completed_at',
    ];

    protected $casts = [
        'professionals' => 'array',
        'priority' => TaskPriorityEnum::class,
        'status' => TaskStatusesEnum::class,
    ];

    public $sortable = [
        'order_column_name' => 'order_column',
        'sort_when_creating' => true,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
