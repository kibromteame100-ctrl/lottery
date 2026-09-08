<?php

namespace App\Policies;

use App\Models\TicketPurchase;
use App\Models\User;

class TicketPurchasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'payment-reviewer', 'report-viewer']);
    }

    public function view(User $user, TicketPurchase $ticket): bool
    {
        // Mobile user can only see their own tickets
        if (!$user->isAdmin()) {
            return $user->id === $ticket->user_id;
        }
        return true;
    }

    public function approve(User $user, TicketPurchase $ticket): bool
    {
        return $user->hasAnyRole(['super-admin', 'payment-reviewer'])
            && $ticket->isPending();
    }

    public function reject(User $user, TicketPurchase $ticket): bool
    {
        return $user->hasAnyRole(['super-admin', 'payment-reviewer'])
            && $ticket->isPending();
    }

    public function viewScreenshot(User $user, TicketPurchase $ticket): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return $user->id === $ticket->user_id;
    }
}
