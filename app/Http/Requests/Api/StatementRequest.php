<?php

namespace App\Http\Requests\Api;

use App\Enums\TransactionDirection;
use App\Enums\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StatementRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('type'))) {
            $this->merge(['type' => array_filter(explode(',', $this->input('type')))]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['nullable', 'array'],
            'type.*' => [Rule::in(TransactionType::values())],
            'direction' => ['nullable', Rule::enum(TransactionDirection::class)],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', Rule::when($this->filled('from'), 'after_or_equal:from')],
            'min_amount' => ['nullable', 'integer', 'min:0'],
            'max_amount' => ['nullable', 'integer', 'min:0', Rule::when($this->filled('min_amount'), 'gte:min_amount')],
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function filters(): array
    {
        return collect($this->validated())->except('per_page')->filter(fn ($value) => $value !== null && $value !== [])->all();
    }
}
