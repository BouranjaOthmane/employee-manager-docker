<?php

// database/seeders/SalarySeeder.php
namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Salary;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class SalarySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        Employee::all()->each(function ($employee) use ($faker) {

            foreach (range(1, rand(3, 8)) as $i) {
                $base = $faker->numberBetween(3000, 9000);
                $bonus = $faker->numberBetween(0, 1000);
                $deduction = $faker->numberBetween(0, 500);

                Salary::create([
                    'employee_id' => $employee->id,
                    'month'       => Carbon::now()->subMonths(rand(0,12))->startOfMonth(),
                    'base_salary' => $base,
                    'bonus'       => $bonus,
                    'deduction'   => $deduction,
                    'net_salary'  => $base + $bonus - $deduction,
                    'note'        => $faker->optional()->sentence,
                ]);
            }
        });
    }
}

