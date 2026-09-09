<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\TicketPurchase;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;

class TicketApprovalService
{
    public function __construct(
        private readonly LotteryNumberGenerator $numberGenerator
    ) {}

    /**
     * Approve a pending ticket purchase.
     * Wrapped in a DB transaction: either both the approval and number generation succeed, or neither does.
     *
     * @throws \RuntimeException|\Throwable
     */
    public function approve(TicketPurchase $purchase, int $adminId): TicketPurchase
    {
        if (!$purchase->isPending()) {
            throw new \RuntimeException(__('tickets.already_processed'));
        }

        return DB::transaction(function () use ($purchase, $adminId) {
            // Lock the row to prevent concurrent approvals
            $purchase = TicketPurchase::lockForUpdate()->findOrFail($purchase->id);

            if (!$purchase->isPending()) {
                throw new \RuntimeException(__('tickets.already_processed'));
            }

            $quantity = $purchase->quantity ?? 1;

            // Generate one unique lottery number per ticket quantity
            $lotteryNumbers = [];
            for ($i = 0; $i < $quantity; $i++) {
                $lotteryNumbers[] = $this->numberGenerator->generate($purchase, $i);
            }

            $purchase->update([
                'status'      => 'approved',
                'reviewed_by' => $adminId,
                'reviewed_at' => now(),
            ]);

            $numbersList = collect($lotteryNumbers)->pluck('number')->implode(', ');

            // Notify the user
            UserNotification::create([
                'user_id' => $purchase->user_id,
                'type'    => 'ticket_approved',
                'title'   => __('notifications.ticket_approved_title'),
                'message' => $quantity > 1
                    ? "Your {$quantity} tickets have been approved. Your lottery numbers: {$numbersList}"
                    : __('notifications.ticket_approved_message', ['number' => $lotteryNumbers[0]->number]),
                'data' => [
                    'ticket_purchase_id' => $purchase->id,
                    'lottery_numbers'    => collect($lotteryNumbers)->pluck('number'),
                ],
            ]);

            // Audit log
            AuditLog::record(
                action: 'ticket_approved',
                entityType: TicketPurchase::class,
                entityId: $purchase->id,
                oldValues: ['status' => 'pending'],
                newValues: [
                    'status'          => 'approved',
                    'lottery_numbers' => collect($lotteryNumbers)->pluck('number'),
                    'reviewed_by'     => $adminId,
                ],
                adminId: $adminId,
            );

            return $purchase->fresh(['lotteryNumbers', 'user', 'lottery']);
        });
    }

    /**
     * Reject a pending ticket purchase.
     *
     * @throws \RuntimeException
     */
    public function reject(TicketPurchase $purchase, int $adminId, string $reason): TicketPurchase
    {
        if (!$purchase->isPending()) {
            throw new \RuntimeException(__('tickets.already_processed'));
        }

        return DB::transaction(function () use ($purchase, $adminId, $reason) {
            $purchase = TicketPurchase::lockForUpdate()->findOrFail($purchase->id);

            if (!$purchase->isPending()) {
                throw new \RuntimeException(__('tickets.already_processed'));
            }

            $purchase->update([
                'status'           => 'rejected',
                'rejection_reason' => $reason,
                'reviewed_by'      => $adminId,
                'reviewed_at'      => now(),
            ]);

            UserNotification::create([
                'user_id' => $purchase->user_id,
                'type'    => 'ticket_rejected',
                'title'   => __('notifications.ticket_rejected_title'),
                'message' => __('notifications.ticket_rejected_message', [
                    'reason' => $reason,
                ]),
                'data' => [
                    'ticket_purchase_id' => $purchase->id,
                    'rejection_reason'   => $reason,
                ],
            ]);

            AuditLog::record(
                action: 'ticket_rejected',
                entityType: TicketPurchase::class,
                entityId: $purchase->id,
                oldValues: ['status' => 'pending'],
                newValues: [
                    'status'           => 'rejected',
                    'rejection_reason' => $reason,
                    'reviewed_by'      => $adminId,
                ],
                adminId: $adminId,
            );

            return $purchase->fresh(['user', 'lottery']);
        });
    }
}
