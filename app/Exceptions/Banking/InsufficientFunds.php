<?php

namespace App\Exceptions\Banking;

class InsufficientFunds extends BankingException
{
    public function __construct(string $message = 'Saldo insuficiente para esta operação.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return 'insufficient_funds';
    }
}
