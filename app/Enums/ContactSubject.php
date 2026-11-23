<?php

namespace App\Enums;

enum ContactSubject: string
{
    case Account = 'conta';
    case Card = 'cartao';
    case Business = 'pj';
    case Press = 'imprensa';
    case Partnership = 'parceria';
    case Other = 'outro';

    public function label(): string
    {
        return match ($this) {
            self::Account => 'Conta e Pix',
            self::Card => 'Cartão de crédito',
            self::Business => 'Conta PJ',
            self::Press => 'Imprensa',
            self::Partnership => 'Parcerias',
            self::Other => 'Outro assunto',
        };
    }
}
