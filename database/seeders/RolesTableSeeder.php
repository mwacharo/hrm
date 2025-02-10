<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $employeeRole = Role::create(['name' => 'employee']);

        $adminRole->givePermissionTo([
            'view_admin_panel',
            'create_resource',
            'edit_resource',
            'delete_resource'
        ]);

        $employeeRole->givePermissionTo('view_employee_panel');
    }
}
