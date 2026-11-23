<?php

/*
 * Regras do banco simulado. Todos os valores em centavos.
 */
return [

    'agency' => '0001',

    'limits' => [
        // Valor mínimo e máximo de uma operação
        'min_amount' => 1,
        'max_deposit' => (int) env('BANKING_MAX_DEPOSIT', 10_000_000),       // R$ 100.000,00
        'max_withdrawal' => (int) env('BANKING_MAX_WITHDRAWAL', 500_000),    // R$ 5.000,00
        'max_transfer' => (int) env('BANKING_MAX_TRANSFER', 5_000_000),      // R$ 50.000,00

        // Soma de transferências e Pix enviados no dia (fuso da aplicação)
        'daily_outgoing' => (int) env('BANKING_DAILY_OUTGOING', 2_000_000),  // R$ 20.000,00
    ],

    'pix' => [
        'max_keys_per_account' => 5,
    ],

];
