<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banking\StorePixKeyRequest;
use App\Http\Resources\PixKeyResource;
use App\Models\PixKey;
use App\Services\Banking\PixService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class PixKeyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return PixKeyResource::collection(
            $request->user()->account()->firstOrFail()->pixKeys()->oldest()->get()
        );
    }

    public function store(StorePixKeyRequest $request, PixService $pix): JsonResponse
    {
        $key = $pix->register(
            $request->user()->account()->firstOrFail(),
            $request->type(),
            $request->validated('phone'),
        );

        return (new PixKeyResource($key))->response()->setStatusCode(201);
    }

    public function destroy(Request $request, PixKey $pixKey): Response
    {
        abort_unless($pixKey->account_id === $request->user()->account?->id, 404);

        $pixKey->delete();

        return response()->noContent();
    }
}
