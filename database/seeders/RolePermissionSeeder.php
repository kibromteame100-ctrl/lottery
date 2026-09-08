<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Tickets
            'view tickets', 'approve tickets', 'reject tickets',

            // Lotteries
            'view lotteries', 'create lotteries', 'update lotteries', 'delete lotteries',

            // Users
            'view users', 'manage users', 'manage admins',

            // Expenses
            'view expenses', 'create expenses', 'update expenses', 'delete expenses',
            'approve expenses', 'reject expenses',

            // Reports & audit
            'view reports', 'view audit logs',

            // Settings
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ── Super Admin — everything ───────────────────────────────────────────
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->syncPermissions($permissions);

        // ── Payment Reviewer ───────────────────────────────────────────────────
        $reviewer = Role::firstOrCreate(['name' => 'payment-reviewer']);
        $reviewer->syncPermissions([
            'view tickets', 'approve tickets', 'reject tickets',
            'view lotteries',
            'view expenses', 'create expenses', 'update expenses',
        ]);

        // ── Report Viewer — read-only ──────────────────────────────────────────
        $reportViewer = Role::firstOrCreate(['name' => 'report-viewer']);
        $reportViewer->syncPermissions([
            'view tickets', 'view lotteries',
            'view users', 'view reports', 'view expenses',
        ]);

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
