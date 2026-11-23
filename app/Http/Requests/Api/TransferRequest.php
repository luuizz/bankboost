<?php

namespace App\Http\Requests\Api;

/**
 * Transferência entre contas do Bankboost por agência e número.
 */
class TransferRequest extends IdempotentRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'agency' => ['required', 'string', 'size:4'],
            'number' => ['required', 'string', 'max:10'],
            'amount' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:140'],
            ...$this->idempotencyRules(),
        ];
    }
}
