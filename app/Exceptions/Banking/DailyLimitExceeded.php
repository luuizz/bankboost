<?php

namespace App\Exceptions\Banking;

class DailyLimitExceeded extends BankingException
{
    public function __construct(string $message = 'Limite diário de transferências atingido.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return 'daily_limit_exceeded';
    }
}
