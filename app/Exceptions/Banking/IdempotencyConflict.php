<?php

namespace App\Exceptions\Banking;

class IdempotencyConflict extends BankingException
{
    public function __construct(string $message = 'Esta chave de idempotência já foi usada com outros dados.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return 'idempotency_conflict';
    }

    public function status(): int
    {
        return 409;
    }

    public function field(): string
    {
        return 'idempotency_key';
    }
}
