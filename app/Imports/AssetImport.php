<?php

namespace App\Imports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;



class AssetImport implements ToModel, WithHeadingRow
{
    public $importedData = [];

    public function model(array $row)
    {
        // Store each row's data in the importedData property
        $this->importedData[] = $row;

        // Return a new Asset instance to be saved to the database
        return new Asset([
            'name'           => $row['name'],
            'description'    => $row['description'],
            'category'       => $row['category'],
            'issuance_date'  => $row['issuance_date'],
            'office_id'      => $row['office_id'],
            'department_id'  => $row['department_id'],
            'serial_no'      => $row['serial_no'],
            'condition'      => $row['condition'],
            'warranty'       => $row['warranty'],
            'purchase_cost'  => $row['purchase_cost'],
            'purchase_date'  => $row['purchase_date'],
            'issued_to'      => $row['issued_to'],
            'issued_by'      => $row['issued_by'],
            'repair_cost'    => $row['repair_cost'],
            'is_assigned'    => $row['is_assigned'],
            'comment'        => $row['comment'],
            'unit_id'        => $row['unit_id'],
        ]);
    }
}

