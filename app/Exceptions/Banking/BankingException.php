<?php

namespace App\Exceptions\Banking;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Erro de regra de negócio bancária. Na API vira JSON com um código estável;
 * no painel volta para o formulário como erro de validação.
 */
abstract class BankingException extends RuntimeException
{
    abstract public function errorCode(): string;

    public function status(): int
    {
        return 422;
    }

    public function field(): string
    {
        return 'amount';
    }

    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => $this->getMessage(),
                'code' => $this->errorCode(),
            ], $this->status());
        }

        return back()->withErrors([$this->field() => $this->getMessage()])->withInput();
    }
}
