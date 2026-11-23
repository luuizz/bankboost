<?php

namespace App\Http\Controllers\Panel;

use App\Enums\TransferMethod;
use App\Exceptions\Banking\DestinationAccountNotFound;
use App\Http\Requests\Banking\TransferRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\TransferResource;
use App\Models\Account;
use App\Models\Transfer;
use App\Services\Banking\Ledger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransferController extends PanelController
{
    public function create(Request $request): Response
    {
        return Inertia::render('Transfer', [
            'account' => AccountResource::make($this->account($request))->resolve($request),
            'limits' => config('banking.limits'),
        ]);
    }

    public function store(TransferRequest $request, Ledger $ledger): RedirectResponse
    {
        $destination = Account::query()
            ->where('agency', $request->validated('agency'))
            ->where('number', $request->validated('number'))
            ->first() ?? throw new DestinationAccountNotFound;

        $transfer = $ledger->transfer(
            $this->account($request),
            $destination,
            $request->integer('amount'),
            TransferMethod::Internal,
            $request->validated('description'),
            $request->idempotencyKey(),
        );

        return redirect()->route('panel.receipts.show', $transfer)->with('success', 'Transferência realizada.');
    }

    public function show(Request $request, Transfer $transfer): Response
    {
        $account = $this->account($request);

        abort_unless($transfer->involves($account), 404);

        return Inertia::render('Receipt', [
            'transfer' => TransferResource::make($transfer)->resolve($request),
            'sent' => $transfer->source_account_id === $account->id,
        ]);
    }
}
