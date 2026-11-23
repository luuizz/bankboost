<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\DB;

/**
 * Confere, numa única consulta, se o saldo de cada conta bate com a soma dos lançamentos.
 */
class LedgerAudit
{
    /**
     * @return list<array{account_id: int, number: string, balance: int, computed: int}>
     */
    public function divergences(): array
    {
        return DB::table('accounts')
            ->leftJoin('transactions', 'transactions.account_id', '=', 'accounts.id')
            ->groupBy('accounts.id', 'accounts.number', 'accounts.balance')
            ->select('accounts.id AS account_id', 'accounts.number', 'accounts.balance')
            ->selectRaw("COALESCE(SUM(CASE WHEN transactions.direction = 'credit' THEN transactions.amount ELSE -transactions.amount END), 0) AS computed")
            ->havingRaw("accounts.balance <> COALESCE(SUM(CASE WHEN transactions.direction = 'credit' THEN transactions.amount ELSE -transactions.amount END), 0)")
            ->get()
            ->map(fn ($row) => [
                'account_id' => (int) $row->account_id,
                'number' => $row->number,
                'balance' => (int) $row->balance,
                'computed' => (int) $row->computed,
            ])
            ->all();
    }

    /**
     * @return array{ok: bool, divergent: int, checked_at: string}
     */
    public function summary(): array
    {
        $divergent = count($this->divergences());

        return ['ok' => $divergent === 0, 'divergent' => $divergent, 'checked_at' => now()->toIso8601String()];
    }
}
