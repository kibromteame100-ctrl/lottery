<?php

namespace Database\Seeders;

use App\Models\Lottery;
use Illuminate\Database\Seeder;

class LotterySeeder extends Seeder
{
    public function run(): void
    {
        $lotteries = [
            [
                'name'          => 'Grand New Year Lottery 2026',
                'description'   => 'Win big in the grand new year lottery draw.',
                'ticket_price'  => 50.00,
                'draw_date'     => now()->addDays(30),
                'status'        => 'active',
                'number_prefix' => 'LOT',
                'number_length' => 6,
                'max_tickets'   => 1000,
            ],
            [
                'name'          => 'Weekly Lucky Draw',
                'description'   => 'Weekly lottery with great prizes.',
                'ticket_price'  => 25.00,
                'draw_date'     => now()->addDays(7),
                'status'        => 'active',
                'number_prefix' => 'WLD',
                'number_length' => 6,
                'max_tickets'   => 500,
            ],
            [
                'name'          => 'Monthly Mega Jackpot',
                'description'   => 'Monthly mega jackpot for the biggest prize.',
                'ticket_price'  => 100.00,
                'draw_date'     => now()->addDays(60),
                'status'        => 'active',
                'number_prefix' => 'MMJ',
                'number_length' => 8,
                'max_tickets'   => null,
            ],
        ];

        foreach ($lotteries as $data) {
            Lottery::firstOrCreate(['name' => $data['name']], $data);
        }

        $this->command->info('Sample lotteries seeded.');
    }
}
