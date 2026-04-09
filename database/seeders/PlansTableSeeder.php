<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlansTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('plans')->insert([
            [
                'code' => 'FREE_TRIAL',
                'name' => 'Free Trial • 5 Minutes',
                'price_kes' => 0,
                'duration_minutes' => 5,
                'duration_days' => null,
                'max_devices' => 1,
                'rate_limit' => '5M/5M',
                'data_cap_mb' => 50,
            ],
            [
                'code' => 'KUMI',
                'name' => 'KUMI • 40-min Pass',
                'price_kes' => 10,
                'duration_minutes' => 40,
                'duration_days' => null,
                'max_devices' => 1,
                'rate_limit' => '5M/5M',
                'data_cap_mb' => 500,
            ],
            [
                'code' => 'MBAO',
                'name' => 'MBAO • 2-hour Social Pass',
                'price_kes' => 20,
                'duration_minutes' => 120,
                'duration_days' => null,
                'max_devices' => 1,
                'rate_limit' => '10M/10M',
                'data_cap_mb' => 2000,
            ],
            [
                'code' => 'DAILY_SOLO',
                'name' => 'Daily Solo • 1-day Pass',
                'price_kes' => 80,
                'duration_minutes' => null,
                'duration_days' => 1,
                'max_devices' => 1,
                'rate_limit' => '10M/10M',
                'data_cap_mb' => null,
            ],
            [
                'code' => 'DAILY_DUO',
                'name' => 'Daily Duo • 1-day Pass',
                'price_kes' => 140,
                'duration_minutes' => null,
                'duration_days' => 1,
                'max_devices' => 2,
                'rate_limit' => '10M/10M',
                'data_cap_mb' => null,
            ],
            [
                'code' => 'WEEKLY',
                'name' => 'Weekly • 7-day Pass',
                'price_kes' => 280,
                'duration_minutes' => null,
                'duration_days' => 7,
                'max_devices' => 1,
                'rate_limit' => '10M/10M',
                'data_cap_mb' => null,
            ],
            [
                'code' => 'MONTHLY',
                'name' => 'Monthly • 30-day Pass',
                'price_kes' => 720,
                'duration_minutes' => null,
                'duration_days' => 30,
                'max_devices' => 1,
                'rate_limit' => '10M/10M',
                'data_cap_mb' => null,
            ],
        ]);
    }
}
