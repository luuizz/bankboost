<?php

namespace App\Http\Controllers\Panel;

use App\Enums\PixKeyType;
use App\Http\Requests\Banking\PixTransferRequest;
use App\Http\Requests\Banking\StorePixKeyRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\PixKeyResource;
use App\Http\Resources\PixRecipientResource;
use App\Models\PixKey;
use App\Services\Banking\PixService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PixController extends PanelController
{
    public function index(Request $request): Response
    {
        $account = $this->account($request);

        return Inertia::render('Pix', [
            'account' => AccountResource::make($account)->resolve($request),
            'keys' => PixKeyResource::collection($account->pixKeys()->oldest()->get())->resolve($request),
            'keyTypes' => collect(PixKeyType::cases())->map(fn (PixKeyType $type) => [
                'value' => $type->value,
                'label' => $type->label(),
            ]),
            'maxKeys' => config('banking.pix.max_keys_per_account'),
        ]);
    }

    /**
     * Consulta usada pelo formulário antes de confirmar o envio (responde JSON).
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

    public function send(PixTransferRequest $request, PixService $pix): RedirectResponse
    {
        $transfer = $pix->send(
            $this->account($request),
            $request->validated('key'),
            $request->integer('amount'),
            $request->validated('description'),
            $request->idempotencyKey(),
            $request->keyType(),
        );

        return redirect()->route('panel.receipts.show', $transfer)->with('success', 'Pix enviado.');
    }

    public function storeKey(StorePixKeyRequest $request, PixService $pix): RedirectResponse
    {
        $key = $pix->register($this->account($request), $request->type(), $request->validated('phone'));

        return back()->with('success', "Chave {$key->type->label()} cadastrada.");
    }

    public function destroyKey(Request $request, PixKey $pixKey): RedirectResponse
    {
        abort_unless($pixKey->account_id === $this->account($request)->id, 404);

        $pixKey->delete();

        return back()->with('success', 'Chave removida.');
    }
}
