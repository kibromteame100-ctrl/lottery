<?php

namespace App\Providers;

use App\Models\Expense;
use App\Models\Lottery;
use App\Models\TicketPurchase;
use App\Models\User;
use App\Policies\ExpensePolicy;
use App\Policies\LotteryPolicy;
use App\Policies\TicketPurchasePolicy;
use App\Policies\UserPolicy;
use App\Services\LotteryNumberGenerator;
use App\Services\TicketApprovalService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LotteryNumberGenerator::class);
        $this->app->singleton(TicketApprovalService::class);
    }

    public function boot(): void
    {
        Gate::policy(TicketPurchase::class, TicketPurchasePolicy::class);
        Gate::policy(Lottery::class,        LotteryPolicy::class);
        Gate::policy(Expense::class,        ExpensePolicy::class);
        Gate::policy(User::class,           UserPolicy::class);
    }
}
