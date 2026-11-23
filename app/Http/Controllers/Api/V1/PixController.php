<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PixKeyType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PixTransferRequest;
use App\Http\Resources\PixRecipientResource;
use App\Services\Banking\PixService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PixController extends Controller
{
    /**
     * Consulta o recebedor antes de confirmar o envio.
     */
    public function lookup(Request $request, PixService $pix): PixRecipientResource
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:255'],
            'key_type' => ['nullable', Rule::enum(PixKeyType::class)],
        ]);

        return new PixRecipientResource(
            $pix->find($data['key'], PixKeyType::tryFrom((string) ($data['key_type'] ?? '')))
        );
    }

    public function store(PixTransferRequest $request, PixService $pix): JsonResponse
    {
        $transfer = $pix->send(
            $request->user()->account()->firstOrFail(),
            $request->validated('key'),
            $request->integer('amount'),
            $request->validated('description'),
            $request->idempotencyKey(),
            $request->keyType(),
        );

        return TransferController::receipt($transfer);
    }
}
