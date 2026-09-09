<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lottery;
use App\Models\TicketPurchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->filled('from') ? $request->from : now()->startOfMonth()->toDateString();
        $to   = $request->filled('to')   ? $request->to   : now()->toDateString();

        $summary = TicketPurchase::select(
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw('SUM(quantity) as total_tickets'),
            DB::raw('SUM(CASE WHEN status = "approved" THEN quantity ELSE 0 END) as approved'),
            DB::raw('SUM(CASE WHEN status = "rejected" THEN quantity ELSE 0 END) as rejected'),
            DB::raw('SUM(CASE WHEN status = "pending"  THEN quantity ELSE 0 END) as pending'),
            DB::raw('SUM(CASE WHEN status = "approved" THEN total_price ELSE 0 END) as revenue'),
        )->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])->first();

        $byLottery = TicketPurchase::select(
                'lottery_id',
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(quantity) as total_tickets'),
                DB::raw('SUM(CASE WHEN status = "approved" THEN total_price ELSE 0 END) as revenue'),
                DB::raw('SUM(CASE WHEN status = "approved" THEN quantity ELSE 0 END) as approved'),
                DB::raw('SUM(CASE WHEN status = "rejected" THEN quantity ELSE 0 END) as rejected'),
            )
            ->with('lottery:id,name')
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
            ->groupBy('lottery_id')
            ->get();

        $dailySales = TicketPurchase::approved()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(quantity) as count'),
                DB::raw('SUM(total_price) as revenue')
            )
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $lotteries = Lottery::orderBy('name')->get(['id', 'name']);

        return view('admin.reports.index', compact('summary', 'byLottery', 'dailySales', 'from', 'to', 'lotteries'));
    }
}
