<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Enums\TransactionDirection;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Account;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Services\Admin\LedgerAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request, LedgerAudit $audit): Response
    {
        $since = now()->subDays(13)->startOfDay();

        // Volume de transferências e Pix por dia, preenchendo os dias sem movimento
        $daily = Transfer::query()
            ->where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) AS day, SUM(amount) AS total, COUNT(*) AS count')
            ->groupBy('day')
            ->get()
            ->keyBy(fn ($row) => Carbon::parse($row->day)->toDateString());

        $volume = collect(range(0, 13))->map(function (int $offset) use ($since, $daily) {
            $day = $since->copy()->addDays($offset)->toDateString();

            return [
                'date' => $day,
                'total' => (int) ($daily[$day]->total ?? 0),
                'count' => (int) ($daily[$day]->count ?? 0),
            ];
        });

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'customers' => Account::count(),
                'activeAccounts' => Account::where('status', AccountStatus::Active)->count(),
                'blockedAccounts' => Account::where('status', AccountStatus::Blocked)->count(),
                'deposits' => (int) Account::sum('balance'),
                'newCustomers' => Account::where('created_at', '>=', now()->subDays(30))->count(),
                'transfersToday' => Transfer::whereDate('created_at', today())->count(),
                'creditsMonth' => (int) Transaction::where('created_at', '>=', now()->startOfMonth())
                    ->where('direction', TransactionDirection::Credit)->sum('amount'),
                'publishedPosts' => Post::published()->count(),
                'unreadMessages' => ContactMessage::whereNull('read_at')->count(),
            ],
            'volume' => $volume,
            'audit' => $audit->summary(),
            'latest' => TransactionResource::collection(
                Transaction::with('account.user')->latest()->latest('id')->limit(8)->get()
            )->resolve($request),
        ]);
    }
}
