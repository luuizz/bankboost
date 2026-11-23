<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TransferMethod;
use App\Exceptions\Banking\DestinationAccountNotFound;
use App\Http\Controllers\Controller;
use App\Http\Requests\Banking\TransferRequest;
use App\Http\Resources\TransferResource;
use App\Models\Account;
use App\Models\Transfer;
use App\Services\Banking\Ledger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferController extends Controller
{
    public function store(TransferRequest $request, Ledger $ledger): JsonResponse
    {
        $destination = Account::query()
            ->where('agency', $request->validated('agency'))
            ->where('number', $request->validated('number'))
            ->first() ?? throw new DestinationAccountNotFound;

        $transfer = $ledger->transfer(
            $request->user()->account()->firstOrFail(),
            $destination,
            $request->integer('amount'),
            TransferMethod::Internal,
            $request->validated('description'),
            $request->idempotencyKey(),
        );

        return self::receipt($transfer);
    }

    /**
     * Comprovante: visível só para quem enviou ou recebeu.
     */
    public function show(Request $request, Transfer $transfer): TransferResource
    {
        abort_unless($transfer->involves($request->user()->account()->firstOrFail()), 404);

        return new TransferResource($transfer);
    }

    public static function receipt(Transfer $transfer): JsonResponse
    {
        return (new TransferResource($transfer))
            ->response()
            ->setStatusCode($transfer->replayed ? 200 : 201)
            ->header('Idempotent-Replayed', $transfer->replayed ? 'true' : 'false');
    }
}
