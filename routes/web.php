<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\LotteryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('admin.dashboard'));

// ── Admin Auth ────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('login',  [LoginController::class, 'showLogin'])->name('login');
        Route::post('login', [LoginController::class, 'login'])->name('login.post')
             ->middleware('throttle:10,1');
    });

    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // ── Protected ─────────────────────────────────────────────────────────────
    Route::middleware(['auth', 'admin.role'])->group(function () {

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // ── Tickets ───────────────────────────────────────────────────────────
        Route::prefix('tickets')->name('tickets.')->group(function () {
            Route::get('/',                        [TicketController::class, 'index'])->name('index');
            Route::get('{ticket}',                 [TicketController::class, 'show'])->name('show');
            Route::post('{ticket}/approve',        [TicketController::class, 'approve'])->name('approve');
            Route::post('{ticket}/reject',         [TicketController::class, 'reject'])->name('reject');
            Route::get('{ticket}/screenshot',      [TicketController::class, 'screenshot'])->name('screenshot');
        });

        // ── Lotteries ─────────────────────────────────────────────────────────
        Route::get('lotteries/create',             [LotteryController::class, 'create'])->name('lotteries.create');
        Route::get('lotteries/{lottery}/edit',     [LotteryController::class, 'edit'])->name('lotteries.edit');
        Route::resource('lotteries',               LotteryController::class)->names('lotteries');

        // ── Expenses ──────────────────────────────────────────────────────────
        Route::prefix('expenses')->name('expenses.')->group(function () {
            Route::get('/',                        [ExpenseController::class, 'index'])->name('index');
            Route::get('create',                   [ExpenseController::class, 'create'])->name('create');
            Route::post('/',                       [ExpenseController::class, 'store'])->name('store');
            Route::get('{expense}',                [ExpenseController::class, 'show'])->name('show');
            Route::get('{expense}/edit',           [ExpenseController::class, 'edit'])->name('edit');
            Route::put('{expense}',                [ExpenseController::class, 'update'])->name('update');
            Route::delete('{expense}',             [ExpenseController::class, 'destroy'])->name('destroy');
            Route::post('{expense}/approve',       [ExpenseController::class, 'approve'])->name('approve');
            Route::post('{expense}/reject',        [ExpenseController::class, 'reject'])->name('reject');
            Route::get('{expense}/receipt',        [ExpenseController::class, 'receipt'])->name('receipt');
        });

        // ── Mobile Users ──────────────────────────────────────────────────────
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/',                        [UserController::class, 'index'])->name('index');
            Route::get('{user}',                   [UserController::class, 'show'])->name('show');
            Route::patch('{user}/toggle-status',   [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('{user}',                [UserController::class, 'destroy'])->name('destroy');
        });

        // ── Admin Users (super-admin only) ────────────────────────────────────
        Route::prefix('admin-users')->name('users.')->middleware('permission:manage admins')->group(function () {
            Route::get('/',                        [UserController::class, 'adminIndex'])->name('admins');
            Route::get('create',                   [UserController::class, 'createAdmin'])->name('create-admin');
            Route::post('/',                       [UserController::class, 'storeAdmin'])->name('store-admin');
            Route::get('{user}/edit',              [UserController::class, 'editAdmin'])->name('edit-admin');
            Route::put('{user}',                   [UserController::class, 'updateAdmin'])->name('update-admin');
            Route::delete('{user}',                [UserController::class, 'destroyAdmin'])->name('destroy-admin');
        });

        // ── Reports ───────────────────────────────────────────────────────────
        Route::get('reports',                      [ReportController::class, 'index'])->name('reports.index');

        // ── Audit Logs ────────────────────────────────────────────────────────
        Route::prefix('audit-logs')->name('audit-logs.')->middleware('role:super-admin')->group(function () {
            Route::get('/',                        [AuditLogController::class, 'index'])->name('index');
            Route::get('{auditLog}',               [AuditLogController::class, 'show'])->name('show');
        });

        // ── Settings ──────────────────────────────────────────────────────────
        Route::prefix('settings')->name('settings.')->middleware('role:super-admin')->group(function () {
            Route::get('/',                        [SettingsController::class, 'index'])->name('index');
            Route::put('/',                        [SettingsController::class, 'update'])->name('update');
        });

        // ── Roles & Permissions (super-admin only) ────────────────────────────
        Route::prefix('settings/roles')->name('roles.')->middleware('role:super-admin')->group(function () {
            Route::get('/',                        [RoleController::class, 'index'])->name('index');
            Route::get('create',                   [RoleController::class, 'create'])->name('create');
            Route::post('/',                       [RoleController::class, 'store'])->name('store');
            Route::get('{role}/edit',              [RoleController::class, 'edit'])->name('edit');
            Route::put('{role}',                   [RoleController::class, 'update'])->name('update');
            Route::delete('{role}',                [RoleController::class, 'destroy'])->name('destroy');

            // Permissions sub-section
            Route::get('permissions',              [RoleController::class, 'permissions'])->name('permissions');
            Route::post('permissions',             [RoleController::class, 'storePermission'])->name('permissions.store');
            Route::delete('permissions/{permission}', [RoleController::class, 'destroyPermission'])->name('permissions.destroy');
        });

        // Locale / Theme
        Route::post('set-locale',                  [SettingsController::class, 'setLocale'])->name('set-locale');
        Route::post('set-theme',                   [SettingsController::class, 'setTheme'])->name('set-theme');
    });
});
