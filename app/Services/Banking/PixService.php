<?php

namespace App\Services\Banking;

use App\Enums\PixKeyType;
use App\Enums\TransferMethod;
use App\Exceptions\Banking\PixKeyAlreadyRegistered;
use App\Exceptions\Banking\PixKeyLimitReached;
use App\Exceptions\Banking\PixKeyNotFound;
use App\Models\Account;
use App\Models\PixKey;
use App\Models\Transfer;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;

class PixService
{
    public function __construct(private readonly Ledger $ledger) {}

    /**
     * CPF e e-mail usam os dados do titular; celular é informado; aleatória é gerada.
     */
    public function register(Account $account, PixKeyType $type, ?string $phone = null): PixKey
    {
        $account->loadMissing('user');

        if ($account->pixKeys()->count() >= config('banking.pix.max_keys_per_account')) {
            throw new PixKeyLimitReached;
        }

        $value = match ($type) {
            PixKeyType::Cpf => $account->user->cpf,
            PixKeyType::Email => $account->user->email,
            PixKeyType::Phone => (string) $phone,
            PixKeyType::Random => (string) Str::uuid(),
        };

        try {
            return $account->pixKeys()->create([
                'type' => $type,
                'key' => $type->normalize($value),
            ]);
        } catch (UniqueConstraintViolationException) {
            throw new PixKeyAlreadyRegistered;
        }
    }

    public function find(string $key, ?PixKeyType $type = null): PixKey
    {
        $type ??= PixKeyType::guess($key);

        $pixKey = PixKey::query()
            ->with('account.user')
            ->where('key', $type->normalize($key))
            ->first();

        if (! $pixKey) {
            throw new PixKeyNotFound;
        }

        return $pixKey;
    }

    public function send(
        Account $from,
        string $key,
        int $amount,
        ?string $description = null,
        ?string $idempotencyKey = null,
        ?PixKeyType $type = null,
    ): Transfer {
        $pixKey = $this->find($key, $type);

        return $this->ledger->transfer(
            $from,
            $pixKey->account,
            $amount,
            TransferMethod::Pix,
            $description,
            $idempotencyKey,
            $pixKey->key,
        );
    }
}
