<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('source_account_id')->constrained('accounts')->restrictOnDelete();
            $table->foreignId('destination_account_id')->constrained('accounts')->restrictOnDelete();
            $table->unsignedBigInteger('amount');
            $table->string('method', 20);
            $table->string('pix_key')->nullable();
            $table->string('description', 140)->nullable();
            $table->string('idempotency_key', 100)->nullable();
            $table->timestamps();

            $table->unique(['source_account_id', 'idempotency_key']);
            $table->index(['source_account_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
