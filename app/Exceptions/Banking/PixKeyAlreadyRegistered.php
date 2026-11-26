<?php

namespace App\Exceptions\Banking;

class PixKeyAlreadyRegistered extends BankingException
{
    public function __construct(string $message = 'Esta chave Pix já está cadastrada.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return 'pix_key_already_registered';
    }

    public function field(): string
    {
        return 'key';
    }
}
