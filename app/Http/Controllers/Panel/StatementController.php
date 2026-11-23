<?php

namespace App\Http\Controllers\Panel;

use App\Enums\TransactionType;
use App\Http\Requests\Banking\StatementRequest;
use App\Http\Resources\TransactionResource;
use Inertia\Inertia;
use Inertia\Response;

class StatementController extends PanelController
{
    public function __invoke(StatementRequest $request): Response
    {
        $account = $this->account($request);
        $filters = $request->filters();

        $totals = $account->transactions()
            ->filter($filters)
            ->selectRaw('direction, SUM(amount) AS total')
            ->groupBy('direction')
            ->pluck('total', 'direction');

        $transactions = $account->transactions()
            ->with('transfer:id,uuid')
            ->filter($filters)
            ->latest()
            ->latest('id')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return Inertia::render('Statement', [
            'transactions' => TransactionResource::collection($transactions)->toResponse($request)->getData(true),
            'filters' => [
                'type' => $filters['type'] ?? [],
                'direction' => $filters['direction'] ?? '',
                'from' => $filters['from'] ?? '',
                'to' => $filters['to'] ?? '',
                'min_amount' => $filters['min_amount'] ?? null,
                'max_amount' => $filters['max_amount'] ?? null,
                'search' => $filters['search'] ?? '',
            ],
            'summary' => [
                'credits' => (int) ($totals['credit'] ?? 0),
                'debits' => (int) ($totals['debit'] ?? 0),
            ],
            'types' => collect(TransactionType::cases())->map(fn (TransactionType $type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ]),
        ]);
    }
}
