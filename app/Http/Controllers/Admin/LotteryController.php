<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLotteryRequest;
use App\Http\Requests\Admin\UpdateLotteryRequest;
use App\Models\AuditLog;
use App\Models\Lottery;

class LotteryController extends Controller
{
    public function index()
    {
        $lotteries = Lottery::withCount([
            'ticketPurchases as approved_count' => fn($q) => $q->where('status', 'approved'),
        ])
        ->withSum(['ticketPurchases as tickets_sold' => fn($q) => $q->where('status', 'approved')], 'quantity')
        ->latest()->paginate(15);

        return view('admin.lotteries.index', compact('lotteries'));
    }

    public function create()
    {
        return view('admin.lotteries.create');
    }

    public function store(StoreLotteryRequest $request)
    {
        $lottery = Lottery::create($request->validated());

        AuditLog::record('lottery_created', Lottery::class, $lottery->id, [], $lottery->toArray());

        return redirect()->route('admin.lotteries.index')
            ->with('success', __('lotteries.created_successfully'));
    }

    public function show(Lottery $lottery)
    {
        $lottery->loadCount(['ticketPurchases'])
            ->loadSum(['ticketPurchases as pending_count'  => fn($q) => $q->where('status', 'pending')],  'quantity')
            ->loadSum(['ticketPurchases as approved_count' => fn($q) => $q->where('status', 'approved')], 'quantity')
            ->loadSum(['ticketPurchases as rejected_count' => fn($q) => $q->where('status', 'rejected')], 'quantity');

        $recentTickets = $lottery->ticketPurchases()
            ->with('user')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.lotteries.show', compact('lottery', 'recentTickets'));
    }

    public function edit(Lottery $lottery)
    {
        return view('admin.lotteries.edit', compact('lottery'));
    }

    public function update(UpdateLotteryRequest $request, Lottery $lottery)
    {
        $old = $lottery->toArray();
        $lottery->update($request->validated());

        AuditLog::record('lottery_updated', Lottery::class, $lottery->id, $old, $lottery->fresh()->toArray());

        return redirect()->route('admin.lotteries.index')
            ->with('success', __('lotteries.updated_successfully'));
    }

    public function destroy(Lottery $lottery)
    {
        if ($lottery->ticketPurchases()->whereIn('status', ['pending', 'approved'])->exists()) {
            return back()->with('error', __('lotteries.cannot_delete_active'));
        }

        AuditLog::record('lottery_deleted', Lottery::class, $lottery->id, $lottery->toArray(), []);
        $lottery->delete();

        return redirect()->route('admin.lotteries.index')
            ->with('success', __('lotteries.deleted_successfully'));
    }
}
