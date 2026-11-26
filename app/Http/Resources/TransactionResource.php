<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Transaction
 */
class TransactionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'direction' => $this->direction->value,
            'amount' => $this->amount,
            'amount_formatted' => Money::format($this->signedAmount()),
            'balance_after' => $this->balance_after,
            'description' => $this->description,
            'counterparty_name' => $this->counterparty_name,
            'transfer_id' => $this->whenLoaded('transfer', fn () => $this->transfer?->uuid),
            'account' => $this->whenLoaded('account', fn () => [
                'id' => $this->account->id,
                'number' => $this->account->number,
                'holder' => $this->account->relationLoaded('user') ? $this->account->user->name : null,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
