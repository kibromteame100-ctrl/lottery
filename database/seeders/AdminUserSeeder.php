<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@lottery.local'],
            [
                'name'             => 'Super Admin',
                'email'            => 'admin@lottery.local',
                'password'         => Hash::make('Admin@1234'),
                'preferred_locale' => 'en',
                'theme_preference' => 'light',
                'status'           => 'active',
            ]
        );
        $superAdmin->assignRole('super-admin');

        // Payment Reviewer
        $reviewer = User::firstOrCreate(
            ['email' => 'reviewer@lottery.local'],
            [
                'name'             => 'Payment Reviewer',
                'email'            => 'reviewer@lottery.local',
                'password'         => Hash::make('Reviewer@1234'),
                'preferred_locale' => 'en',
                'theme_preference' => 'light',
                'status'           => 'active',
            ]
        );
        $reviewer->assignRole('payment-reviewer');

        // Report Viewer
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@lottery.local'],
            [
                'name'             => 'Report Viewer',
                'email'            => 'viewer@lottery.local',
                'password'         => Hash::make('Viewer@1234'),
                'preferred_locale' => 'en',
                'theme_preference' => 'light',
                'status'           => 'active',
            ]
        );
        $viewer->assignRole('report-viewer');

        $this->command->info('Admin users seeded:');
        $this->command->info('  super-admin:      admin@lottery.local / Admin@1234');
        $this->command->info('  payment-reviewer: reviewer@lottery.local / Reviewer@1234');
        $this->command->info('  report-viewer:    viewer@lottery.local / Viewer@1234');
    }
}
