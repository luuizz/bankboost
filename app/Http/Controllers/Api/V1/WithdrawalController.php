<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banking\AmountRequest;
use App\Http\Resources\TransactionResource;
use App\Services\Banking\Ledger;
use Illuminate\Http\JsonResponse;

/**
 * Saque simulado.
 */
class WithdrawalController extends Controller
{
    public function store(AmountRequest $request, Ledger $ledger): JsonResponse
    {
        $transaction = $ledger->withdraw(
            $request->user()->account()->firstOrFail(),
            $request->integer('amount'),
            $request->validated('description'),
        );

        return (new TransactionResource($transaction))->response()->setStatusCode(201);
    }
}
