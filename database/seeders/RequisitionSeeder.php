<?php

namespace Database\Seeders;

use App\Models\Requisition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RequisitionSeeder extends Seeder
{
  public function run()
  {
      $requisition = Requisition::create([
          'user_id' => 1,
          'department_id' => 2,
          'status' => 'pending',
          'special_instructions' => 'Urgent request for office supplies',
          'budgeted_expenses' => 1500,
          'funds_available' => true,
      ]);

      $requisition->items()->createMany([
          [
              'name' => 'Printer Paper',
              'description' => 'A4 size printer paper (pack of 500 sheets)',
              'quantity' => 10,
              'unit_cost' => 5,
              'total_cost' => 50,
          ],
          [
              'name' => 'Stapler',
              'description' => 'Heavy-duty stapler',
              'quantity' => 2,
              'unit_cost' => 20,
              'total_cost' => 40,
          ],
      ]);
  }
}
