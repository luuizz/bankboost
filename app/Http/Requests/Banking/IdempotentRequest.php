<?php

namespace App\Http\Requests\Banking;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Lê o cabeçalho Idempotency-Key, que torna seguro repetir um envio.
 */
abstract class IdempotentRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->hasHeader('Idempotency-Key')) {
            $this->merge(['idempotency_key' => $this->header('Idempotency-Key')]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function idempotencyRules(): array
    {
        return [
            'idempotency_key' => ['nullable', 'string', 'max:100', 'regex:/^[A-Za-z0-9_\-:.]+$/'],
        ];
    }

    public function idempotencyKey(): ?string
    {
        return $this->validated('idempotency_key');
    }
}
