<?php

namespace App\Enums;

enum PostCategory: string
{
    case Money = 'dinheiro';
    case Investments = 'investimentos';
    case Security = 'seguranca';
    case Business = 'empreender';
    case News = 'novidades';

    public function label(): string
    {
        return match ($this) {
            self::Money => 'Finanças pessoais',
            self::Investments => 'Investimentos',
            self::Security => 'Segurança',
            self::Business => 'Empreender',
            self::News => 'Novidades',
        };
    }

    /**
     * Ícone Phosphor usado na capa gerada e nos filtros do blog.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Money => 'piggy-bank',
            self::Investments => 'chart-line-up',
            self::Security => 'shield-check',
            self::Business => 'storefront',
            self::News => 'megaphone',
        };
    }

    public function gradient(): string
    {
        return match ($this) {
            self::Money => 'from-brand-20 to-brand-30',
            self::Investments => 'from-emerald-600 to-teal-500',
            self::Security => 'from-ink-800 to-brand-10',
            self::Business => 'from-amber-500 to-orange-500',
            self::News => 'from-pink-500 to-brand-30',
        };
    }

    /**
     * @return array{label: string, icon: string, gradient: string}
     */
    public function meta(): array
    {
        return ['label' => $this->label(), 'icon' => $this->icon(), 'gradient' => $this->gradient()];
    }
}
