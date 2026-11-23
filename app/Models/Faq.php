<?php

namespace App\Models;

use App\Enums\FaqCategory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['category', 'question', 'answer', 'position', 'active'])]
class Faq extends Model
{
    /**
     * @param  Builder<self>  $query
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('active', true);
    }

    protected function casts(): array
    {
        return [
            'category' => FaqCategory::class,
            'active' => 'boolean',
            'position' => 'integer',
        ];
    }
}
