<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\AccountResource;
use App\Models\User;
use App\Services\Banking\AccountOpener;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Abre a conta e já devolve um token de acesso.
     */
    public function register(RegisterRequest $request, AccountOpener $opener): JsonResponse
    {
        $user = DB::transaction(function () use ($request, $opener) {
            $user = User::create($request->safe()->only(['name', 'email', 'cpf', 'password']));
            $opener->open($user);

            return $user;
        });

        $token = $user->createToken($request->validated('device_name', 'api'))->plainTextToken;

        return response()->json([
            'token' => $token,
            'account' => new AccountResource($user->account->load('user')),
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'E-mail ou senha incorretos.',
            ]);
        }

        return response()->json([
            'token' => $user->createToken($request->validated('device_name'))->plainTextToken,
        ]);
    }

    public function logout(Request $request): Response
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
