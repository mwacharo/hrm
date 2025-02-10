<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define the units data
        $units = [
            [
                'name' => 'Kenya',
                'phone' => '+2541212234',
                'address' => 'Nairobi',
            ],
            [
                'name' => 'Uganda',
                'phone' => '+25611033443444',
                'address' => 'Kampala',
            ],
            [
                'name' => 'Tanzania',
                'phone' => '+255123234344',
                'address' => 'Dar es salaam',
            ],
          
        ];

        // Insert the data into the 'units' table
        DB::table('units')->insert($units);
    }
}
