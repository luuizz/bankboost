<?php

namespace App\Exceptions\Banking;

class PixKeyLimitReached extends BankingException
{
    public function __construct(string $message = 'Limite de chaves Pix por conta atingido.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return 'pix_key_limit_reached';
    }

    public function field(): string
    {
        return 'type';
    }
}
