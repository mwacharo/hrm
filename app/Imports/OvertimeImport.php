<?php


namespace App\Imports;


use App\Models\Overtime;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class OvertimeImport implements ToCollection, WithStartRow
{
    private $rows = 0;
    private $message = "";

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            try {
                $employee_id = $row[1];
                $date = Carbon::parse($row[2]);
                $hours = (float)$row[3];
                $description = $row[4];

                // Validate data before insertion
                // You can define your validation rules here

                $overtime_object = new Overtime();
                $overtime_created = $overtime_object->create([
                    'employee_id' => $employee_id,
                    'date' => $date,
                    'hours' => $hours,
                    'description' => $description,
                ]);

                if ($overtime_created) {
                    ++$this->rows;
                }
            } catch (\Exception $e) {
                // Log any errors or exceptions
               // \Log::error("Error importing row: " . $e->getMessage());
            }
        }
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function getErrorMessage(): string
    {
        return $this->message;
    }

    public function startRow(): int
    {
        return 4;
    }
}

