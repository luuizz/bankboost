<?php

namespace Database\Seeders;

use App\Enums\PixKeyType;
use App\Enums\TransferMethod;
use App\Models\User;
use App\Services\Banking\Ledger;
use App\Services\Banking\PixService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Duas contas de demonstração com movimentações. Senha: password
     */
    public function run(Ledger $ledger, PixService $pix): void
    {
        $ana = User::factory()->withAccount()->create([
            'name' => 'Ana Souza',
            'email' => 'ana@bankboost.test',
        ])->account;

        $bruno = User::factory()->withAccount()->create([
            'name' => 'Bruno Lima',
            'email' => 'bruno@bankboost.test',
        ])->account;

        $pix->register($ana, PixKeyType::Email);
        $pix->register($bruno, PixKeyType::Cpf);
        $pix->register($bruno, PixKeyType::Random);

        $ledger->deposit($ana, 500_000, 'Salário');
        $ledger->deposit($bruno, 120_000, 'Depósito inicial');

        $ledger->transfer($ana->refresh(), $bruno->refresh(), 35_000, TransferMethod::Internal, 'Aluguel do mês');
        $pix->send($bruno->refresh(), 'ana@bankboost.test', 4_590, 'Pizza de sexta');
        $ledger->withdraw($ana->refresh(), 20_000, 'Saque no caixa');
    }
}
