<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Exceptions\Banking\AccountBlocked;
use Database\Factories\AccountFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'agency', 'number', 'status'])]
class Account extends Model
{
    /** @use HasFactory<AccountFactory> */
    use HasFactory;

    /**
     * O saldo não é preenchível em massa: só o Ledger altera.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'balance' => 0,
        'status' => 'active',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return HasMany<PixKey, $this>
     */
    public function pixKeys(): HasMany
    {
        return $this->hasMany(PixKey::class);
    }

    /**
     * @return HasMany<Transfer, $this>
     */
    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(Transfer::class, 'source_account_id');
    }

    public function isActive(): bool
    {
        return $this->status === AccountStatus::Active;
    }

    public function ensureActive(): void
    {
        if (! $this->isActive()) {
            throw new AccountBlocked;
        }
    }

    public function formattedNumber(): string
    {
        return "{$this->agency} / {$this->number}";
    }

    protected function casts(): array
    {
        return [
            'balance' => 'integer',
            'status' => AccountStatus::class,
        ];
    }
}
