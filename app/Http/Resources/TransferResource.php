<?php

namespace App\Http\Resources;

use App\Models\Transfer;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Comprovante da transferência.
 *
 * @mixin Transfer
 */
class TransferResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->resource->loadMissing('source.user', 'destination.user');

        return [
            'id' => $this->uuid,
            'method' => $this->method->value,
            'method_label' => $this->method->label(),
            'amount' => $this->amount,
            'amount_formatted' => Money::format($this->amount),
            'description' => $this->description,
            'pix_key' => $this->pix_key,
            'source' => [
                'name' => $this->source->user->name,
                'agency' => $this->source->agency,
                'number' => $this->source->number,
            ],
            'destination' => [
                'name' => $this->destination->user->name,
                'agency' => $this->destination->agency,
                'number' => $this->destination->number,
            ],
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
