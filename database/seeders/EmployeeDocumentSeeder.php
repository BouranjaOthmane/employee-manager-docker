<?php

// database/seeders/EmployeeDocumentSeeder.php
namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class EmployeeDocumentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $types = ['contract', 'cin', 'cnss', 'diploma'];

        Employee::all()->each(function ($employee) use ($faker, $types) {
            foreach (range(1, rand(1, 4)) as $i) {
                EmployeeDocument::create([
                    'employee_id' => $employee->id,
                    'type'        => $faker->randomElement($types),
                    'title'       => ucfirst($faker->word),
                    'file_path'   => 'employee-docs/fake_'.$faker->uuid.'.pdf',
                ]);
            }
        });
    }
}
