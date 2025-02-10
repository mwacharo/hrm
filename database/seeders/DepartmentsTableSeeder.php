<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Human Resources', 'created_at' => now()],
            ['name' => 'Finance', 'created_at' => now()],
            ['name' => 'IT', 'created_at' => now()],
        ];

        foreach ($departments as $department) {
            DB::table('departments')->insert($department);
        }
    }
}
