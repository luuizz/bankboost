<?php

namespace App\Services\Banking;

use App\Enums\TransactionDirection;
use App\Enums\TransactionType;
use App\Enums\TransferMethod;
use App\Exceptions\Banking\AmountOutOfRange;
use App\Exceptions\Banking\DailyLimitExceeded;
use App\Exceptions\Banking\IdempotencyConflict;
use App\Exceptions\Banking\InsufficientFunds;
use App\Exceptions\Banking\SameAccountTransfer;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Único ponto que movimenta dinheiro.
 *
 * Toda operação roda numa transação do banco, trava as contas envolvidas
 * (SELECT ... FOR UPDATE, sempre na ordem do id para evitar deadlock) e grava
 * o lançamento junto com o novo saldo. Assim o saldo da conta é sempre igual
 * à soma dos lançamentos, mesmo com requisições simultâneas.
 */
class Ledger
{
    private const ATTEMPTS = 3;

    public function deposit(Account $account, int $amount, ?string $description = null): Transaction
    {
        $this->assertAmount($amount, config('banking.limits.max_deposit'));

        return DB::transaction(function () use ($account, $amount, $description) {
            $locked = $this->lock($account);
            $locked->ensureActive();

            return $this->post($locked, TransactionType::Deposit, $amount, $description ?? 'Depósito');
        }, self::ATTEMPTS);
    }

    public function withdraw(Account $account, int $amount, ?string $description = null): Transaction
    {
        $this->assertAmount($amount, config('banking.limits.max_withdrawal'));

        return DB::transaction(function () use ($account, $amount, $description) {
            $locked = $this->lock($account);
            $locked->ensureActive();

            if ($locked->balance < $amount) {
                throw new InsufficientFunds;
            }

            return $this->post($locked, TransactionType::Withdrawal, $amount, $description ?? 'Saque');
        }, self::ATTEMPTS);
    }

    public function transfer(
        Account $from,
        Account $to,
        int $amount,
        TransferMethod $method = TransferMethod::Internal,
        ?string $description = null,
        ?string $idempotencyKey = null,
        ?string $pixKey = null,
    ): Transfer {
        $this->assertAmount($amount, config('banking.limits.max_transfer'));

        if ($from->is($to)) {
            throw new SameAccountTransfer;
        }

        try {
            return DB::transaction(function () use ($from, $to, $amount, $method, $description, $idempotencyKey, $pixKey) {
                $accounts = Account::query()
                    ->whereKey([$from->id, $to->id])
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                /** @var Account $source */
                $source = $accounts[$from->id];
                /** @var Account $destination */
                $destination = $accounts[$to->id];

                // Com a conta de origem travada, a checagem não sofre corrida.
                if ($idempotencyKey && $existing = $this->replay($source, $idempotencyKey, $destination, $amount)) {
                    return $existing;
                }

                $source->ensureActive();
                $destination->ensureActive();

                if ($source->balance < $amount) {
                    throw new InsufficientFunds;
                }

                $this->assertDailyLimit($source, $amount);

                $transfer = Transfer::create([
                    'source_account_id' => $source->id,
                    'destination_account_id' => $destination->id,
                    'amount' => $amount,
                    'method' => $method,
                    'pix_key' => $pixKey,
                    'description' => $description,
                    'idempotency_key' => $idempotencyKey,
                ]);

                $source->loadMissing('user');
                $destination->loadMissing('user');

                $this->post($source, $method->debitType(), $amount, $description, $transfer, $destination->user->name);
                $this->post($destination, $method->creditType(), $amount, $description, $transfer, $source->user->name);

                return $transfer;
            }, self::ATTEMPTS);
        } catch (UniqueConstraintViolationException $e) {
            // Duas requisições com a mesma chave chegaram juntas: devolve a que venceu.
            if ($idempotencyKey && $existing = $this->replay($from, $idempotencyKey, $to, $amount)) {
                return $existing;
            }

            throw $e;
        }
    }

    /**
     * Saldo calculado só pelos lançamentos, para conferência.
     */
    public function computedBalance(Account $account): int
    {
        return (int) $account->transactions()
            ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'credit' THEN amount ELSE -amount END), 0) AS total")
            ->value('total');
    }

    private function lock(Account $account): Account
    {
        return Account::query()->whereKey($account->id)->lockForUpdate()->firstOrFail();
    }

    private function post(
        Account $account,
        TransactionType $type,
        int $amount,
        ?string $description = null,
        ?Transfer $transfer = null,
        ?string $counterparty = null,
    ): Transaction {
        $account->balance += $type->direction() === TransactionDirection::Credit ? $amount : -$amount;
        $account->save();

        return $account->transactions()->create([
            'transfer_id' => $transfer?->id,
            'type' => $type,
            'direction' => $type->direction(),
            'amount' => $amount,
            'balance_after' => $account->balance,
            'description' => $description,
            'counterparty_name' => $counterparty,
        ]);
    }

    private function replay(Account $source, string $key, Account $destination, int $amount): ?Transfer
    {
        $existing = Transfer::query()
            ->where('source_account_id', $source->id)
            ->where('idempotency_key', $key)
            ->first();

        if (! $existing) {
            return null;
        }

        if ($existing->destination_account_id !== $destination->id || $existing->amount !== $amount) {
            throw new IdempotencyConflict;
        }

        $existing->replayed = true;

        return $existing;
    }

    private function assertAmount(int $amount, int $max): void
    {
        if ($amount < config('banking.limits.min_amount') || $amount > $max) {
            throw new AmountOutOfRange;
        }
    }

    private function assertDailyLimit(Account $source, int $amount): void
    {
        $sentToday = (int) $source->outgoingTransfers()
            ->where('created_at', '>=', now()->startOfDay())
            ->sum('amount');

        if ($sentToday + $amount > config('banking.limits.daily_outgoing')) {
            throw new DailyLimitExceeded;
        }
    }
}
