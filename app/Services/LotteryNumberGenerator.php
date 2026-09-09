<?php

namespace App\Services;

use App\Models\Lottery;
use App\Models\LotteryNumber;
use App\Models\TicketPurchase;
use Illuminate\Support\Str;

class LotteryNumberGenerator
{
    /**
     * Generate a unique lottery number for a given ticket purchase.
     * Must be called inside a database transaction.
     *
     * @throws \RuntimeException if a unique number cannot be generated after max attempts
     */
    public function generate(TicketPurchase $purchase, int $index = 0): LotteryNumber
    {
        // If a number already exists for this index, return it (idempotent)
        $existing = LotteryNumber::where('ticket_purchase_id', $purchase->id)
            ->orderBy('id')
            ->skip($index)
            ->first();

        if ($existing) {
            return $existing;
        }

        $lottery = $purchase->lottery;
        $number  = $this->createUniqueNumber($lottery);

        return LotteryNumber::create([
            'ticket_purchase_id' => $purchase->id,
            'lottery_id'         => $lottery->id,
            'number'             => $number,
            'generated_at'       => now(),
            'status'             => 'active',
        ]);
    }

    private function createUniqueNumber(Lottery $lottery): string
    {
        $maxAttempts = 20;

        for ($i = 0; $i < $maxAttempts; $i++) {
            $number = $this->formatNumber($lottery);

            // Use insertOrIgnore-style check — unique constraint on (lottery_id, number)
            $exists = LotteryNumber::where('lottery_id', $lottery->id)
                ->where('number', $number)
                ->exists();

            if (!$exists) {
                return $number;
            }
        }

        throw new \RuntimeException('Could not generate a unique lottery number after ' . $maxAttempts . ' attempts.');
    }

    private function formatNumber(Lottery $lottery): string
    {
        $year     = now()->format('Y');
        $sequence = str_pad(
            LotteryNumber::where('lottery_id', $lottery->id)->count() + 1,
            $lottery->number_length,
            '0',
            STR_PAD_LEFT
        );

        // Add slight randomness so retries produce different values
        $random = str_pad(random_int(0, 99), 2, '0', STR_PAD_LEFT);
        $sequence = substr($sequence . $random, 0, $lottery->number_length);

        return sprintf('%s-%s-%s', $lottery->number_prefix, $year, $sequence);
    }
}
