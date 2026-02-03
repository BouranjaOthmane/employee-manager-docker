<?php

// database/seeders/HolidaySeeder.php
namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        Holiday::insert([
            [
                'date'   => '2025-01-01',
                'name'   => 'New Year',
                'reason' => 'Public holiday',
            ],
            [
                'date'   => '2025-05-01',
                'name'   => 'Labour Day',
                'reason' => 'International workers day',
            ],
            [
                'date'   => '2025-07-30',
                'name'   => 'National Holiday',
                'reason' => 'National celebration',
            ],
        ]);
    }
}

