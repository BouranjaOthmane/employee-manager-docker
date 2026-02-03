<?php

// database/seeders/VacationSeeder.php
namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Vacation;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class VacationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $employees = Employee::all();

        foreach ($employees as $employee) {
            foreach (range(1, rand(0, 3)) as $i) {

                $start = $faker->dateTimeBetween('-6 months', '+2 months');
                $end   = (clone $start)->modify('+'.rand(2,10).' days');

                Vacation::create([
                    'employee_id' => $employee->id,
                    'start_date'  => $start,
                    'end_date'    => $end,
                    'type'        => $faker->randomElement(['paid', 'unpaid', 'sick']),
                    'reason'      => $faker->sentence,
                    'status'      => $faker->randomElement(['pending', 'approved', 'rejected']),
                ]);
            }
        }
    }
}

