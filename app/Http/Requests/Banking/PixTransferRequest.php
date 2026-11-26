<?php

namespace App\Http\Requests\Banking;

use App\Enums\PixKeyType;
use Illuminate\Validation\Rule;

class PixTransferRequest extends IdempotentRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:255'],
            'key_type' => ['nullable', Rule::enum(PixKeyType::class)],
            'amount' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:140'],
            ...$this->idempotencyRules(),
        ];
    }

    public function keyType(): ?PixKeyType
    {
        return PixKeyType::tryFrom((string) $this->validated('key_type'));
    }
}
