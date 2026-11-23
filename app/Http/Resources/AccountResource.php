<?php

namespace App\Http\Resources;

use App\Models\Account;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Account
 */
class AccountResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'agency' => $this->agency,
            'number' => $this->number,
            'status' => $this->status->value,
            'balance' => $this->balance,
            'balance_formatted' => Money::format($this->balance),
            'holder' => $this->whenLoaded('user', fn () => [
                'name' => $this->user->name,
                'email' => $this->user->email,
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
