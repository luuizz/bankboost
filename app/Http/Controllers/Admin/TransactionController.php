<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Banking\StatementRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Todos os lançamentos do banco, com os mesmos filtros do extrato do cliente.
 */
class TransactionController extends Controller
{
    public function __invoke(StatementRequest $request): Response
    {
        $filters = $request->filters();

        $transactions = Transaction::query()
            ->with(['account.user', 'transfer:id,uuid'])
            ->filter($filters)
            ->latest()
            ->latest('id')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('admin/Transactions', [
            'transactions' => TransactionResource::collection($transactions)->toResponse($request)->getData(true),
            'filters' => [
                'type' => $filters['type'] ?? [],
                'direction' => $filters['direction'] ?? '',
                'from' => $filters['from'] ?? '',
                'to' => $filters['to'] ?? '',
                'search' => $filters['search'] ?? '',
            ],
            'types' => collect(TransactionType::cases())->map(fn (TransactionType $type) => ['value' => $type->value, 'label' => $type->label()]),
        ]);
    }
}
