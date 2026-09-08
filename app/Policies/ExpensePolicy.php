<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;

class ExpensePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'payment-reviewer', 'report-viewer']);
    }

    public function view(User $user, Expense $expense): bool
    {
        return $user->hasAnyRole(['super-admin', 'payment-reviewer', 'report-viewer'])
            || $expense->created_by === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'payment-reviewer']);
    }

    public function update(User $user, Expense $expense): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
        // Creator can edit only pending expenses
        return $expense->created_by === $user->id && $expense->isPending();
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $user->hasRole('super-admin')
            || ($expense->created_by === $user->id && $expense->isPending());
    }

    public function approve(User $user, Expense $expense): bool
    {
        return $user->hasRole('super-admin') && $expense->isPending();
    }

    public function reject(User $user, Expense $expense): bool
    {
        return $user->hasRole('super-admin') && $expense->isPending();
    }
}
