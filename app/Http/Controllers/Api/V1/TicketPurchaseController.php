<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TicketPurchaseRequest;
use App\Models\Lottery;
use App\Models\TicketPurchase;
use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketPurchaseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $purchases = $request->user()
            ->ticketPurchases()
            ->with(['lottery:id,name,draw_date', 'lotteryNumber:id,ticket_purchase_id,number,generated_at'])
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $purchases->map(fn($p) => $this->purchaseResource($p)),
            'meta'    => [
                'current_page' => $purchases->currentPage(),
                'last_page'    => $purchases->lastPage(),
                'total'        => $purchases->total(),
            ],
        ]);
    }

    public function show(Request $request, TicketPurchase $ticketPurchase): JsonResponse
    {
        if ($ticketPurchase->user_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => __('auth.unauthorized')], 403);
        }

        $ticketPurchase->load(['lottery', 'lotteryNumber']);

        return response()->json([
            'success' => true,
            'data'    => $this->purchaseResource($ticketPurchase),
        ]);
    }

    public function store(TicketPurchaseRequest $request): JsonResponse
    {
        $lottery  = Lottery::findOrFail($request->lottery_id);
        $quantity = max(1, (int) $request->input('quantity', 1));

        if (!$lottery->isActive()) {
            return response()->json([
                'success' => false,
                'message' => __('tickets.lottery_not_active'),
            ], 422);
        }

        // Check enough tickets remain
        if ($lottery->max_tickets) {
            $remaining = $lottery->max_tickets - $lottery->soldTicketsCount();
            if ($remaining <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => __('tickets.lottery_sold_out'),
                ], 422);
            }
            if ($quantity > $remaining) {
                return response()->json([
                    'success' => false,
                    'message' => "Only {$remaining} ticket(s) remaining for this lottery.",
                ], 422);
            }
        }

        // Check transaction ID uniqueness
        if (TicketPurchase::where('transaction_id', $request->transaction_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This transaction ID has already been submitted.',
                'errors'  => ['transaction_id' => ['This transaction ID has already been used.']],
            ], 422);
        }

        // Store screenshot privately
        $path       = $request->file('screenshot')->store('screenshots', 'private');
        $totalPrice = $lottery->ticket_price * $quantity;

        // One purchase record = one transaction (covers all tickets in this payment)
        $purchase = TicketPurchase::create([
            'user_id'         => $request->user()->id,
            'lottery_id'      => $lottery->id,
            'ticket_price'    => $lottery->ticket_price,
            'quantity'        => $quantity,
            'total_price'     => $totalPrice,
            'payment_method'  => $request->payment_method,
            'transaction_id'  => $request->transaction_id,
            'screenshot_path' => $path,
            'status'          => 'pending',
        ]);

        // Notify user
        UserNotification::create([
            'user_id' => $request->user()->id,
            'type'    => 'ticket_submitted',
            'title'   => __('notifications.ticket_submitted_title'),
            'message' => $quantity > 1
                ? "{$quantity} tickets submitted for {$lottery->name}. Total: ETB " . number_format($totalPrice, 2)
                : __('notifications.ticket_submitted_message', ['lottery' => $lottery->name]),
            'data'    => ['ticket_purchase_id' => $purchase->id],
        ]);

        return response()->json([
            'success' => true,
            'message' => $quantity > 1
                ? "{$quantity} tickets submitted successfully."
                : __('tickets.submitted_successfully'),
            'data'    => [
                'id'             => $purchase->id,
                'status'         => $purchase->status,
                'transaction_id' => $purchase->transaction_id,
                'quantity'       => $purchase->quantity,
                'ticket_price'   => $purchase->ticket_price,
                'total_price'    => $purchase->total_price,
            ],
        ], 201);
    }

    private function purchaseResource(TicketPurchase $p): array
    {
        return [
            'id'               => $p->id,
            'lottery'          => $p->lottery ? ['id' => $p->lottery->id, 'name' => $p->lottery->name, 'draw_date' => $p->lottery->draw_date?->toIso8601String()] : null,
            'ticket_price'     => $p->ticket_price,
            'quantity'         => $p->quantity ?? 1,
            'total_price'      => $p->total_price ?? $p->ticket_price,
            'payment_method'   => $p->payment_method,
            'transaction_id'   => $p->transaction_id,
            'status'           => $p->status,
            'rejection_reason' => $p->rejection_reason,
            'lottery_number'   => $p->lotteryNumber?->number,
            'reviewed_at'      => $p->reviewed_at?->toIso8601String(),
            'created_at'       => $p->created_at->toIso8601String(),
        ];
    }
}
