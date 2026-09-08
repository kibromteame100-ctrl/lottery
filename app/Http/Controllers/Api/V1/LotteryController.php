<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Lottery;
use Illuminate\Http\JsonResponse;

class LotteryController extends Controller
{
    public function index(): JsonResponse
    {
        $lotteries = Lottery::active()
            ->withCount('ticketPurchases')
            ->orderBy('draw_date')
            ->get()
            ->map(fn($l) => $this->lotteryResource($l));

        return response()->json(['success' => true, 'data' => $lotteries]);
    }

    public function show(Lottery $lottery): JsonResponse
    {
        $lottery->loadCount([
            'ticketPurchases',
            'ticketPurchases as approved_count' => fn($q) => $q->where('status', 'approved'),
        ]);

        return response()->json(['success' => true, 'data' => $this->lotteryResource($lottery)]);
    }

    private function lotteryResource(Lottery $lottery): array
    {
        return [
            'id'            => $lottery->id,
            'name'          => $lottery->name,
            'description'   => $lottery->description,
            'ticket_price'  => $lottery->ticket_price,
            'draw_date'     => $lottery->draw_date?->toIso8601String(),
            'status'        => $lottery->status,
            'number_prefix' => $lottery->number_prefix,
            'tickets_sold'  => $lottery->ticket_purchases_count ?? 0,
            'max_tickets'   => $lottery->max_tickets,
            'remaining'     => $lottery->remainingTickets(),
        ];
    }
}
