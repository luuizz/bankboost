<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\PixKeyResource;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Services\Banking\Ledger;
use App\Support\Cpf;
use App\Support\Money;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(AccountStatus::class)],
        ]);
        $search = trim($filters['search'] ?? '');
        $digits = Cpf::digits($search);

        $accounts = Account::query()
            ->with('user')
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($search !== '', function ($query) use ($search, $digits) {
                $query->where(function ($query) use ($search, $digits) {
                    $query->where('number', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search, $digits) {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->when($digits !== '', fn ($query) => $query->orWhere('cpf', 'like', "%{$digits}%"));
                        });
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString()
            ->through(fn (Account $account) => $this->summary($account));

        return Inertia::render('admin/customers/Index', [
            'accounts' => $accounts,
            'filters' => ['search' => $search, 'status' => $filters['status'] ?? ''],
        ]);
    }

    public function show(Request $request, Account $account, Ledger $ledger): Response
    {
        $account->load('user', 'pixKeys');

        return Inertia::render('admin/customers/Show', [
            'account' => [
                ...$this->summary($account),
                'blocked_reason' => $account->blocked_reason,
                'blocked_at' => $account->blocked_at?->toIso8601String(),
                'computed_balance' => $ledger->computedBalance($account),
            ],
            'pixKeys' => PixKeyResource::collection($account->pixKeys)->resolve($request),
            'transactions' => TransactionResource::collection(
                $account->transactions()->with('transfer:id,uuid')->latest()->latest('id')->paginate(15)
            )->toResponse($request)->getData(true),
        ]);
    }

    public function block(Request $request, Account $account): RedirectResponse
    {
        $data = $request->validate(['reason' => ['required', 'string', 'min:5', 'max:200']]);

        $account->block($data['reason']);

        return back()->with('success', 'Conta bloqueada.');
    }

    public function unblock(Account $account): RedirectResponse
    {
        $account->unblock();

        return back()->with('success', 'Conta desbloqueada.');
    }

    /**
     * @return array<string, mixed>
     */
    private function summary(Account $account): array
    {
        return [
            'id' => $account->id,
            'agency' => $account->agency,
            'number' => $account->number,
            'status' => $account->status->value,
            'balance' => $account->balance,
            'balance_formatted' => Money::format($account->balance),
            'created_at' => $account->created_at?->toIso8601String(),
            'holder' => [
                'name' => $account->user->name,
                'email' => $account->user->email,
                'cpf' => Cpf::format($account->user->cpf),
            ],
        ];
    }
}
