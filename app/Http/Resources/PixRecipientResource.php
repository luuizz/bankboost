<?php

namespace App\Http\Resources;

use App\Models\PixKey;
use App\Support\Cpf;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Dados do recebedor exibidos antes de confirmar um Pix, com o documento mascarado.
 *
 * @mixin PixKey
 */
class PixRecipientResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->key,
            'type' => $this->type->value,
            'name' => $this->account->user->name,
            'document' => Cpf::mask($this->account->user->cpf),
            'bank' => config('app.name'),
            'agency' => $this->account->agency,
            'number' => '***'.substr($this->account->number, -3),
        ];
    }
}
