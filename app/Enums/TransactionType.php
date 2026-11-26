<?php

namespace App\Enums;

enum TransactionType: string
{
    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';
    case TransferIn = 'transfer_in';
    case TransferOut = 'transfer_out';
    case PixIn = 'pix_in';
    case PixOut = 'pix_out';

    public function label(): string
    {
        return match ($this) {
            self::Deposit => 'Depósito',
            self::Withdrawal => 'Saque',
            self::TransferIn => 'Transferência recebida',
            self::TransferOut => 'Transferência enviada',
            self::PixIn => 'Pix recebido',
            self::PixOut => 'Pix enviado',
        };
    }

    public function direction(): TransactionDirection
    {
        return match ($this) {
            self::Deposit, self::TransferIn, self::PixIn => TransactionDirection::Credit,
            self::Withdrawal, self::TransferOut, self::PixOut => TransactionDirection::Debit,
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
