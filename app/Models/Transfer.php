<?php

namespace App\Models;

use App\Enums\TransferMethod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'source_account_id', 'destination_account_id', 'amount', 'method',
    'pix_key', 'description', 'idempotency_key',
])]
class Transfer extends Model
{
    use HasUuids;

    /**
     * Marcado quando a resposta veio de uma chave de idempotência já usada.
     */
    public bool $replayed = false;

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
    public function source(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'source_account_id');
    }

    /**
     * @return BelongsTo<Account, $this>
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'destination_account_id');
    }

    /**
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function involves(Account $account): bool
    {
        return in_array($account->id, [$this->source_account_id, $this->destination_account_id], true);
    }

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'method' => TransferMethod::class,
        ];
    }
}
