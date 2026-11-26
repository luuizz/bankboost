<?php

namespace App\Enums;

use App\Support\Cpf;
use Illuminate\Support\Str;

enum PixKeyType: string
{
    case Cpf = 'cpf';
    case Email = 'email';
    case Phone = 'phone';
    case Random = 'random';

    public function label(): string
    {
        return match ($this) {
            self::Cpf => 'CPF',
            self::Email => 'E-mail',
            self::Phone => 'Celular',
            self::Random => 'Chave aleatória',
        };
    }

    /**
     * Deixa a chave no formato em que é guardada e buscada.
     */
    public function normalize(string $value): string
    {
        return match ($this) {
            self::Cpf => Cpf::digits($value),
            self::Email => Str::lower(trim($value)),
            self::Phone => '+55'.Str::of(preg_replace('/\D/', '', $value))->replaceMatches('/^55(?=\d{10,11}$)/', '')->value(),
            self::Random => Str::lower(trim($value)),
        };
    }

    /**
     * Descobre o tipo de uma chave digitada livremente (usado na consulta e no envio).
     */
    public static function guess(string $value): self
    {
        $value = trim($value);

        return match (true) {
            Str::isUuid($value) => self::Random,
            filter_var($value, FILTER_VALIDATE_EMAIL) !== false => self::Email,
            str_starts_with($value, '+') => self::Phone,
            Cpf::isValid($value) => self::Cpf,
            default => self::Phone,
        };
    }
}
