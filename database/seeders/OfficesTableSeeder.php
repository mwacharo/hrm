<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OfficesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define the offices data
        $offices = [
            [
                'name' => 'Nairobi',
                'phone' => '1234567890',
                'unit_id' => 1,
            ],
            [
                'name' => 'Kampala',
                'phone' => '0987654321',
                'unit_id' => 2, // Set the unit ID
            ],
            // Add more offices as needed
        ];

        // Insert the data into the 'offices' table
        DB::table('offices')->insert($offices);
    }
}
