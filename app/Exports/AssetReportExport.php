<?php

namespace App\Exports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetReportExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Asset::with('issuedBy','issuedTo')->select(
            'name',
            'category',
            'serial_no',
            'purchase_cost',
            'condition',
            'issued_to',
            // 'issued_by',
            'warranty',
            'purchase_cost'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Asset Name',
            'Category',
            'Serial Number',
            'Purchase Cost',
            'Condition',
            'Issued To',
            // 'Issued By', 
            'Warranty',
            'Purchase Cost',
        ];
    }

    public function map($asset): array
    {
        return [
            $asset->name,
            $asset->category,
            $asset->serial_no,
            $asset->purchase_cost,
            $asset->condition,
            $asset->issued_to ?$asset->issuedTo->firstname . ' ' . $asset->issuedTo->lastname : 'N/A', 
            // $asset->issuedBy ? $asset->issuedBy->firstname . ' ' . $asset->issuedBy->lastname : 'N/A', 
            $asset->warranty,
            $asset->purchase_cost,
        ];
    }
}