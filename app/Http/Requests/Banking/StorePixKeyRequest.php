<?php

namespace App\Http\Requests\Banking;

use App\Enums\PixKeyType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePixKeyRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(PixKeyType::class)],
            // Celular com DDD, com ou sem +55
            'phone' => [
                Rule::requiredIf(fn () => $this->input('type') === PixKeyType::Phone->value),
                'nullable',
                'string',
                'regex:/^\+?(55)?\s?\(?\d{2}\)?\s?9?\d{4}-?\d{4}$/',
            ],
        ];
    }

    public function type(): PixKeyType
    {
        return PixKeyType::from($this->validated('type'));
    }
}
