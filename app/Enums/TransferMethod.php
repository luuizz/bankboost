<?php

namespace App\Enums;

enum TransferMethod: string
{
    case Internal = 'internal';
    case Pix = 'pix';

    public function debitType(): TransactionType
    {
        return match ($this) {
            self::Internal => TransactionType::TransferOut,
            self::Pix => TransactionType::PixOut,
        };
    }

    public function creditType(): TransactionType
    {
        return match ($this) {
            self::Internal => TransactionType::TransferIn,
            self::Pix => TransactionType::PixIn,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Internal => 'Transferência entre contas',
            self::Pix => 'Pix',
        };
    }
}
