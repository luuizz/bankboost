<?php

namespace App\Exceptions\Banking;

class AmountOutOfRange extends BankingException
{
    public function __construct(string $message = 'Valor fora do limite permitido para esta operação.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return 'amount_out_of_range';
    }
}
