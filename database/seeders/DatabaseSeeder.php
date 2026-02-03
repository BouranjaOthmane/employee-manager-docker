<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    // database/seeders/DatabaseSeeder.php
    public function run(): void
    {
        $this->call([
            PositionSeeder::class,
            EmployeeSeeder::class,
            EmployeeDocumentSeeder::class,
            VacationSeeder::class,
            SalarySeeder::class,
            HolidaySeeder::class,
        ]);
    }
}
