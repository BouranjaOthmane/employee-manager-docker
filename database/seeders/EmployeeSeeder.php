<?php

// database/seeders/EmployeeSeeder.php
namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $positions = Position::pluck('id')->toArray();

        foreach (range(1, 30) as $i) {
            Employee::create([
                'position_id' => $faker->randomElement($positions),
                'first_name'  => $faker->firstName,
                'last_name'   => $faker->lastName,
                'email'       => $faker->unique()->safeEmail,
                'phone'       => $faker->phoneNumber,
                'cin'         => strtoupper($faker->bothify('??######')),
                'cnss'        => $faker->numerify('######'),
                'hire_date'   => $faker->dateTimeBetween('-5 years', 'now'),
                'status'      => $faker->randomElement(['active', 'inactive']),
            ]);
        }
    }
}
