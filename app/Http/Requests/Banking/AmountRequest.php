<?php

namespace App\Http\Requests\Banking;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Depósito e saque simulados. Valor em centavos.
 */
class AmountRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'amount' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:140'],
        ];
    }
}
