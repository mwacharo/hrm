<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Unit;
use App\Models\Office;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserLoginDetailsMail;

class EmployeesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $password = Str::random(8);

        // Perform lookup to get IDs based on names
        $unitId = Unit::where('name', $row['unit'])->value('id');
        $officeId = Office::where('name', $row['office'])->value('id');
        $departmentId = Department::where('name', $row['department'])->value('id');
        $designationId = Designation::where('name', $row['designation'])->value('id');

        $user = new User([
            'firstname' => $row['first_name'],
            'lastname' => $row['last_name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'unit_id' => $unitId,
            'office_id' => $officeId,
            'department_id' => $departmentId,
            'designation_id' => $designationId,
            'password' => bcrypt($password),
            'is_enabled' => true,
        ]);

        // Save the user
        $user->save();

        // Send login details through email
        Mail::to($user->email)->send(new UserLoginDetailsMail($user, $password));

        return $user;
    }
}
