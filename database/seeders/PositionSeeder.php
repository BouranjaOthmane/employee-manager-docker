<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $positions = [
            ['title' => 'HR Manager'],
            ['title' => 'Accountant'],
            ['title' => 'Technician'],
            ['title' => 'Sales Agent'],
            ['title' => 'IT Support'],
            ['title' => 'Supervisor'],
        ];

        Position::insert($positions);
    }
}
