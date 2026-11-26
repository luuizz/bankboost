<?php

namespace App\Enums;

enum FaqCategory: string
{
    case Account = 'conta';
    case Pix = 'pix';
    case Card = 'cartao';
    case Investments = 'investimentos';
    case Security = 'seguranca';
    case Business = 'pj';

    public function label(): string
    {
        return match ($this) {
            self::Account => 'Conta digital',
            self::Pix => 'Pix e transferências',
            self::Card => 'Cartão',
            self::Investments => 'Investimentos',
            self::Security => 'Segurança',
            self::Business => 'Conta PJ',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Account => 'device-mobile',
            self::Pix => 'qr-code',
            self::Card => 'credit-card',
            self::Investments => 'chart-line-up',
            self::Security => 'shield-check',
            self::Business => 'storefront',
        };
    }
}
