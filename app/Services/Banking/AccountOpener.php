<?php

namespace App\Services\Banking;

use App\Models\Account;
use App\Models\User;

class AccountOpener
{
    public function open(User $user): Account
    {
        return $user->account()->create([
            'agency' => config('banking.agency'),
            'number' => $this->nextNumber(),
        ]);
    }

    /**
     * Número com seis dígitos e dígito verificador (módulo 11), único no banco.
     */
    private function nextNumber(): string
    {
        do {
            $base = (string) random_int(100000, 999999);
            $number = $base.'-'.$this->checkDigit($base);
        } while (Account::where('number', $number)->exists());

        return $number;
    }

    private function checkDigit(string $base): string
    {
        $sum = 0;

        foreach (str_split(strrev($base)) as $index => $digit) {
            $sum += (int) $digit * ($index + 2);
        }

        $rest = 11 - ($sum % 11);

        return $rest >= 10 ? '0' : (string) $rest;
    }
}
