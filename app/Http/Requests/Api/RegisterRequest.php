<?php

namespace App\Http\Requests\Api;

use App\Rules\ValidCpf;
use App\Support\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (is_string($this->input('cpf'))) {
            $this->merge(['cpf' => Cpf::digits($this->input('cpf'))]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'cpf' => ['required', 'string', new ValidCpf, 'unique:users,cpf'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'device_name' => ['sometimes', 'string', 'max:100'],
        ];
    }
}
