<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lottery;
use App\Models\TicketPurchase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── KPI stats ──────────────────────────────────────────────
        $stats = [
            'pending_count'    => TicketPurchase::pending()->count(),
            'approved_count'   => (int) TicketPurchase::approved()->sum('quantity'),
            'rejected_count'   => TicketPurchase::rejected()->count(),
            'total_sales'      => (float) TicketPurchase::approved()->sum('total_price'),
            'active_lotteries' => Lottery::active()->count(),
            'total_users'      => User::whereDoesntHave('roles')->count(),
            'expenses_month'   => \App\Models\Expense::approved()->thisMonth()->sum('amount'),
            'expenses_pending' => \App\Models\Expense::pending()->count(),
        ];

        // ── Recent activity ────────────────────────────────────────
        $recentTickets = TicketPurchase::with(['user', 'lottery'])
            ->latest()
            ->limit(8)
            ->get();

        $recentUsers = User::whereDoesntHave('roles')
            ->latest()
            ->limit(5)
            ->get();

        // ── Sales over last 30 days (line chart) ───────────────────
        $salesByDay = TicketPurchase::approved()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as total'))
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill in missing days with 0
        $salesLabels = [];
        $salesData   = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $salesLabels[] = now()->subDays($i)->format('M d');
            $salesData[]   = (float) ($salesByDay[$d]->total ?? 0);
        }

        // ── Lottery comparison chart data ──────────────────────────
        $lotteryChart = Lottery::withCount([
                'ticketPurchases as total_count',
                'ticketPurchases as pending_count'  => fn ($q) => $q->where('status', 'pending'),
                'ticketPurchases as rejected_count' => fn ($q) => $q->where('status', 'rejected'),
            ])
            ->withSum(['ticketPurchases as approved_count' => fn ($q) => $q->where('status', 'approved')], 'quantity')
            ->withSum(['ticketPurchases as total_revenue'  => fn ($q) => $q->where('status', 'approved')], 'total_price')
            ->orderByDesc('total_count')
            ->limit(6)
            ->get();

        $lotteryNames    = $lotteryChart->pluck('name')->map(fn($n) => strlen($n) > 20 ? substr($n,0,18).'…' : $n)->values()->toArray();
        $lotteryPending  = $lotteryChart->pluck('pending_count')->values()->toArray();
        $lotteryApproved = $lotteryChart->pluck('approved_count')->map(fn($v) => (int)($v ?? 0))->values()->toArray();
        $lotteryRejected = $lotteryChart->pluck('rejected_count')->values()->toArray();
        $lotteryRevenue  = $lotteryChart->pluck('total_revenue')->map(fn($v) => (float)($v ?? 0))->values()->toArray();

        // ── Tickets by lottery (sidebar bars) ─────────────────────
        $ticketsByLottery = TicketPurchase::select('lottery_id', DB::raw('SUM(quantity) as total'))
            ->with('lottery:id,name')
            ->where('status', 'approved')
            ->groupBy('lottery_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'stats',
            'recentTickets',
            'ticketsByLottery',
            'recentUsers',
            'salesLabels',
            'salesData',
            'lotteryNames',
            'lotteryPending',
            'lotteryApproved',
            'lotteryRejected',
            'lotteryRevenue',
            'lotteryChart',
        ));
    }
}
