<?php

namespace App\Models;

use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'parent_id',
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'type' => TransactionTypeEnum::class,
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FinancialCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(FinancialCategory::class, 'parent_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'financial_category_id');
    }

    public static function getParentCategories()
    {
        return self::whereNull('parent_id')->pluck('name', 'id')->toArray();
    }

    public static function getHierarchicalOptions(int $type)
    {
        $categories = self::where('type', $type)
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        $options = [];
        foreach ($categories as $category) {
            $children = [];
            foreach ($category->children as $child) {
                $children[$child->id] = $child->name;
            }
            $options[$category->name] = $children;
        }

        return $options;
    }
}
