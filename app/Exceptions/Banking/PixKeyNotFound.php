<?php

namespace App\Exceptions\Banking;

class PixKeyNotFound extends BankingException
{
    public function __construct(string $message = 'Chave Pix não encontrada.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return 'pix_key_not_found';
    }

    public function status(): int
    {
        return 404;
    }

    public function field(): string
    {
        return 'key';
    }
}
