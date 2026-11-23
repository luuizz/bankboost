<?php

namespace App\Support;

final class Cpf
{
    public static function digits(string $value): string
    {
        return preg_replace('/\D/', '', $value) ?? '';
    }

    public static function isValid(string $value): bool
    {
        $cpf = self::digits($value);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        return self::checkDigits(substr($cpf, 0, 9)) === substr($cpf, 9, 2);
    }

    public static function generate(): string
    {
        do {
            $base = '';

            for ($i = 0; $i < 9; $i++) {
                $base .= random_int(0, 9);
            }
        } while (preg_match('/^(\d)\1{8}$/', $base));

        return $base.self::checkDigits($base);
    }

    public static function format(string $value): string
    {
        $cpf = self::digits($value);

        return sprintf('%s.%s.%s-%s', substr($cpf, 0, 3), substr($cpf, 3, 3), substr($cpf, 6, 3), substr($cpf, 9, 2));
    }

    /**
     * Mostra só o miolo do CPF, como os bancos fazem na confirmação do Pix.
     */
    public static function mask(string $value): string
    {
        $cpf = self::digits($value);

        return sprintf('***.%s.%s-**', substr($cpf, 3, 3), substr($cpf, 6, 3));
    }

    private static function checkDigits(string $base): string
    {
        foreach ([10, 11] as $weight) {
            $sum = 0;

            foreach (str_split($base) as $index => $digit) {
                $sum += (int) $digit * ($weight - $index);
            }

            $rest = ($sum * 10) % 11;
            $base .= $rest === 10 ? 0 : $rest;
        }

        return substr($base, -2);
    }
}
