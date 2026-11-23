<?php

namespace App\Http\Controllers\Panel;

use App\Http\Resources\AccountResource;
use App\Http\Resources\TransactionResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends PanelController
{
    public function __invoke(Request $request): Response
    {
        $account = $this->account($request);

        $monthTotals = $account->transactions()
            ->where('created_at', '>=', now()->startOfMonth())
            ->selectRaw('direction, SUM(amount) AS total')
            ->groupBy('direction')
            ->pluck('total', 'direction');

        return Inertia::render('Dashboard', [
            'account' => AccountResource::make($account)->resolve($request),
            'recent' => TransactionResource::collection(
                $account->transactions()->with('transfer:id,uuid')->latest()->latest('id')->limit(6)->get()
            )->resolve($request),
            'month' => [
                'credits' => (int) ($monthTotals['credit'] ?? 0),
                'debits' => (int) ($monthTotals['debit'] ?? 0),
            ],
            'pixKeys' => $account->pixKeys()->count(),
        ]);
    }
}
