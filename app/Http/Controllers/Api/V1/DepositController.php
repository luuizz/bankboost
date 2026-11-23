<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banking\AmountRequest;
use App\Http\Resources\TransactionResource;
use App\Services\Banking\Ledger;
use Illuminate\Http\JsonResponse;

/**
 * Depósito simulado (como se fosse um boleto já compensado).
 */
class DepositController extends Controller
{
    public function store(AmountRequest $request, Ledger $ledger): JsonResponse
    {
        $transaction = $ledger->deposit(
            $request->user()->account()->firstOrFail(),
            $request->integer('amount'),
            $request->validated('description'),
        );

        return (new TransactionResource($transaction))->response()->setStatusCode(201);
    }
}
