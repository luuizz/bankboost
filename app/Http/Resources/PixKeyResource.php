<?php

namespace App\Http\Resources;

use App\Models\PixKey;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PixKey
 */
class PixKeyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type->value,
            'type_label' => $this->type->label(),
            'key' => $this->key,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
