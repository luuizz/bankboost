<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TransactionDirection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Banking\StatementRequest;
use App\Http\Resources\TransactionResource;
use App\Support\Money;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StatementController extends Controller
{
    /**
     * Extrato paginado com filtros e totais do período filtrado.
     */
    public function index(StatementRequest $request): AnonymousResourceCollection
    {
        $account = $request->user()->account()->firstOrFail();
        $filters = $request->filters();

        $totals = $account->transactions()
            ->filter($filters)
            ->selectRaw('direction, SUM(amount) AS total')
            ->groupBy('direction')
            ->pluck('total', 'direction');

        $credits = (int) ($totals[TransactionDirection::Credit->value] ?? 0);
        $debits = (int) ($totals[TransactionDirection::Debit->value] ?? 0);

        $transactions = $account->transactions()
            ->with('transfer:id,uuid')
            ->filter($filters)
            ->latest()
            ->latest('id')
            ->paginate($request->integer('per_page', 20))
            ->withQueryString();

        return TransactionResource::collection($transactions)->additional([
            'summary' => [
                'credits' => $credits,
                'debits' => $debits,
                'net' => $credits - $debits,
                'net_formatted' => Money::format($credits - $debits),
                'balance' => $account->balance,
                'balance_formatted' => Money::format($account->balance),
            ],
        ]);
    }
}
