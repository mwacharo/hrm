<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define HRMIS permissions
        $permissions = [
            // User management
            'view_users', 'create_user', 'edit_user', 'delete_user',

            // Department management
            'view_departments', 'create_department', 'edit_department', 'delete_department',

            // Designation management
            'view_designations', 'create_designation', 'edit_designation', 'delete_designation',

            // Leave management
            'view_leave_applications', 'approve_leave_applications', 'reject_leave_applications',

            // Attendance management
            'view_attendance', 'record_attendance', 'edit_attendance', 'delete_attendance',

            // Payroll management
            'view_payroll', 'generate_payslip', 'approve_payslip', 'reject_payslip',

            // Performance management
            'view_performance_appraisals', 'conduct_appraisal', 'edit_appraisal', 'delete_appraisal',

            'view_admin_panel', 'view_employee_panel',
            'create_resource', 'edit_resource', 'delete_resource', 'view_employee_profile'
        ];

        // Create permissions only if they don't exist
        foreach ($permissions as $permission) {

            // Check if the permission already exists
            if (!Permission::where('name', $permission)->where('guard_name', 'web')->exists()) {
                // Permission doesn't exist, so create it
                Permission::create(['name' => $permission, 'guard_name' => 'web']);
            }
        }
    }
}
