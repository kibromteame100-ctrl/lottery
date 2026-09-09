<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectTicketRequest;
use App\Models\TicketPurchase;
use App\Services\TicketApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function __construct(private readonly TicketApprovalService $approvalService) {}

    public function index(Request $request)
    {
        $query = TicketPurchase::with(['user', 'lottery', 'reviewer', 'lotteryNumber'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('lottery_id')) {
            $query->where('lottery_id', $request->lottery_id);
        }

        $tickets = $query->paginate(20)->withQueryString();

        $lotteries = \App\Models\Lottery::orderBy('name')->get(['id', 'name']);

        return view('admin.tickets.index', compact('tickets', 'lotteries'));
    }

    public function show(TicketPurchase $ticket)
    {
        $ticket->load(['user', 'lottery', 'reviewer', 'lotteryNumbers']);
        return view('admin.tickets.show', compact('ticket'));
    }

    public function approve(TicketPurchase $ticket)
    {
        try {
            $this->approvalService->approve($ticket, auth()->id());
            return redirect()->route('admin.tickets.show', $ticket)
                ->with('success', __('tickets.approved_successfully'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(RejectTicketRequest $request, TicketPurchase $ticket)
    {
        try {
            $this->approvalService->reject($ticket, auth()->id(), $request->validated('reason'));
            return redirect()->route('admin.tickets.show', $ticket)
                ->with('success', __('tickets.rejected_successfully'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function screenshot(TicketPurchase $ticket)
    {
        // Only admins can view screenshots; file is stored privately
        if (!$ticket->screenshot_path || !Storage::disk('private')->exists($ticket->screenshot_path)) {
            abort(404);
        }

        $mime = Storage::disk('private')->mimeType($ticket->screenshot_path);
        $content = Storage::disk('private')->get($ticket->screenshot_path);

        return response($content, 200)->header('Content-Type', $mime);
    }
}
