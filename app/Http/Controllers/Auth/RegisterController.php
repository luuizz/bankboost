<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\Banking\AccountOpener;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(RegisterRequest $request, AccountOpener $opener): RedirectResponse
    {
        $user = DB::transaction(function () use ($request, $opener) {
            $user = User::create($request->safe()->only(['name', 'email', 'cpf', 'password']));
            $opener->open($user);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('panel.dashboard')->with('success', 'Conta aberta! Bem-vindo ao Bankboost.');
    }
}
