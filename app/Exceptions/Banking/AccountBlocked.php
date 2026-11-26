<?php

namespace App\Exceptions\Banking;

class AccountBlocked extends BankingException
{
    public function __construct(string $message = 'A conta está bloqueada para movimentações.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return 'account_blocked';
    }

    public function field(): string
    {
        return 'account';
    }
}
