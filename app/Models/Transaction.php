<?php

namespace App\Models;

use App\Enums\TransactionDirection;
use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use LogicException;

#[Fillable([
    'account_id', 'transfer_id', 'type', 'direction', 'amount',
    'balance_after', 'description', 'counterparty_name',
])]
class Transaction extends Model
{
    use HasUuids;

    /**
     * Lançamentos são imutáveis: correções viram um novo lançamento.
     */
    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Lançamentos não podem ser alterados.'));
        static::deleting(fn () => throw new LogicException('Lançamentos não podem ser removidos.'));
    }

    /**
     * @return list<string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * @return BelongsTo<Account, $this>
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * @return BelongsTo<Transfer, $this>
     */
    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }

    public function signedAmount(): int
    {
        return $this->direction === TransactionDirection::Credit ? $this->amount : -$this->amount;
    }

    /**
     * Filtros do extrato. Datas no formato Y-m-d, valores em centavos.
     *
     * @param  Builder<self>  $query
     * @param  array{type?: list<string>|string|null, direction?: string|null, from?: string|null, to?: string|null, min_amount?: int|string|null, max_amount?: int|string|null, search?: string|null}  $filters
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $types = $filters['type'] ?? null;

        if (is_string($types)) {
            $types = explode(',', $types);
        }

        $query
            ->when($types, fn (Builder $q) => $q->whereIn('type', $types))
            ->when($filters['direction'] ?? null, fn (Builder $q, string $direction) => $q->where('direction', $direction))
            ->when($filters['from'] ?? null, fn (Builder $q, string $from) => $q->where('created_at', '>=', Carbon::parse($from)->startOfDay()))
            ->when($filters['to'] ?? null, fn (Builder $q, string $to) => $q->where('created_at', '<=', Carbon::parse($to)->endOfDay()))
            ->when(isset($filters['min_amount']), fn (Builder $q) => $q->where('amount', '>=', (int) $filters['min_amount']))
            ->when(isset($filters['max_amount']), fn (Builder $q) => $q->where('amount', '<=', (int) $filters['max_amount']))
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(function (Builder $q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                        ->orWhere('counterparty_name', 'like', "%{$search}%");
                });
            });
    }

    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'direction' => TransactionDirection::class,
            'amount' => 'integer',
            'balance_after' => 'integer',
        ];
    }
}
