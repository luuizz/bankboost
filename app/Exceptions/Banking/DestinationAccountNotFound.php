<?php

namespace App\Exceptions\Banking;

class DestinationAccountNotFound extends BankingException
{
    public function __construct(string $message = 'Conta de destino não encontrada.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return 'destination_not_found';
    }

    public function status(): int
    {
        return 404;
    }

    public function field(): string
    {
        return 'destination';
    }
}
