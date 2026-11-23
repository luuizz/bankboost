<?php

namespace Tests\Concerns;

use App\Models\Account;
use App\Models\User;
use App\Services\Banking\Ledger;
use Laravel\Sanctum\Sanctum;

trait InteractsWithBank
{
    protected function accountWith(int $balance = 0, array $userAttributes = []): Account
    {
        $account = User::factory()->withAccount()->create($userAttributes)->account;

        if ($balance > 0) {
            app(Ledger::class)->deposit($account, $balance);
        }

        return $account->refresh();
    }

    protected function actingAsOwner(Account $account): Account
    {
        Sanctum::actingAs($account->user);

        return $account;
    }
}
