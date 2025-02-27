<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class AssetImport implements ToCollection, WithHeadingRow
{
    public $importedData = [];

    // Define location-to-unit mapping
    private $unitMapping = [
        'hq'       => 1,
        'uganda'   => 2,
        'tanzania' => 3,
    ];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Normalize the keys to lowercase
            $row = array_change_key_case($row->toArray(), CASE_LOWER);

            // Map location to unit_id
            $location = strtolower(trim($row['location'] ?? ''));
            $unitId = $this->unitMapping[$location] ?? null;

            // Ensure `purchase_date` is in YYYY-MM-DD format
            $purchaseDate = $row['purchase_date'] ?? null;
            if ($purchaseDate && preg_match('/\d{2}\.\d{2}\.\d{4}/', $purchaseDate)) {
                try {
                    $purchaseDate = Carbon::createFromFormat('d.m.Y', $purchaseDate)->format('Y-m-d');
                } catch (\Exception $e) {
                    $purchaseDate = null; // Skip invalid date
                }
            }

            // Store each row's data safely
            $this->importedData[] = [
                'name'           => $row['name'] ?? null,
                'description'    => $row['description'] ?? null,
                'category'       => $row['category'] ?? null,
                'issuance_date'  => $row['issuance_date'] ?? null,
                'office_id'      => $row['office_id'] ?? null,
                'department_id'  => $row['department_id'] ?? null,
                'serial_no'      => $row['serial_no'] ?? null,
                'condition'      => $row['condition'] ?? 'Unknown',
                'warranty'       => $row['warranty'] ?? null,
                'purchase_cost'  => $row['purchase_cost'] ?? 0,
                'purchase_date'  => $purchaseDate, // ✅ Formatted correctly
                'issued_to'      => $row['issued_to'] ?? null, // Fixed key issue
                'issued_by'      => $row['issued_by'] ?? null,
                'repair_cost'    => $row['repair_cost'] ?? 0, // Ensured numeric default
                'is_assigned'    => $row['is_assigned'] ?? 0, // Ensured numeric default
                'comment'        => $row['comment'] ?? '',
                'unit_id'        => $unitId,
            ];
        }
    }

    public function getImportedData()
    {
        return $this->importedData;
    }
}
