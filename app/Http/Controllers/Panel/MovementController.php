<?php

namespace App\Http\Controllers\Panel;

use App\Http\Requests\Banking\AmountRequest;
use App\Services\Banking\Ledger;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;

/**
 * Depósito e saque simulados a partir do painel.
 */
class MovementController extends PanelController
{
    public function deposit(AmountRequest $request, Ledger $ledger): RedirectResponse
    {
        $transaction = $ledger->deposit($this->account($request), $request->integer('amount'), $request->validated('description'));

        return back()->with('success', 'Depósito de '.Money::format($transaction->amount).' realizado.');
    }

    public function withdraw(AmountRequest $request, Ledger $ledger): RedirectResponse
    {
        $transaction = $ledger->withdraw($this->account($request), $request->integer('amount'), $request->validated('description'));

        return back()->with('success', 'Saque de '.Money::format($transaction->amount).' realizado.');
    }
}
