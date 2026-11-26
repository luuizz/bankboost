<?php

namespace App\Models;

use App\Enums\ContactSubject;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'email', 'subject', 'message'])]
class ContactMessage extends Model
{
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    protected function casts(): array
    {
        return [
            'subject' => ContactSubject::class,
            'read_at' => 'datetime',
        ];
    }
}
