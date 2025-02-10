<?php

namespace App\Imports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AttendanceImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Map Excel columns to the Attendance model fields
        return new Attendance([
            'attendance_date' => $row['attendance_date'],
            'employee_id' => $row['Employee_ID'],
            'clock_in_time' => $row['Clock_In_Time'],
            'clock_out_time' => $row['Clock_Out_Time'],
            // Add more fields as needed
        ]);
    }
}
