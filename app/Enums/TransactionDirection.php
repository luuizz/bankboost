<?php

namespace App\Enums;

enum TransactionDirection: string
{
    case Credit = 'credit';
    case Debit = 'debit';

    public function label(): string
    {
        return match ($this) {
            self::Credit => 'Entrada',
            self::Debit => 'Saída',
        };
    }
}
