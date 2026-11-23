<?php

namespace App\Support;

/**
 * Valores monetários circulam sempre em centavos (int) para não depender de ponto flutuante.
 */
final class Money
{
    public static function format(int $cents): string
    {
        $sign = $cents < 0 ? '-' : '';

        return $sign.'R$ '.number_format(abs($cents) / 100, 2, ',', '.');
    }
}
