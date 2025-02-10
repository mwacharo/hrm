<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DesignationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define the designations data
        $designations = [
            [
                'name' => 'Manager',
            ],
            [
                'name' => 'Officer',
            ],
            [
                'name' => 'Assistant',
            ],
            [
                'name' => 'Intern',
            ],

        ];

        // Insert the data into the 'designations' table
        DB::table('designations')->insert($designations);
    }
}
