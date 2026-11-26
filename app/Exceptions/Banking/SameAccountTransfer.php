<?php

namespace App\Exceptions\Banking;

class SameAccountTransfer extends BankingException
{
    public function __construct(string $message = 'Não é possível transferir para a própria conta.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return 'same_account';
    }

    public function field(): string
    {
        return 'destination';
    }
}
