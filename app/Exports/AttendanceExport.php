<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AttendanceExport implements FromView
{
    protected $attendances;

    public function __construct($attendances)
    {
        $this->attendances = $attendances;
    }

    public function view(): View
    {

        return view('reports.attendances-report-excel', [
            'attendances' => $this->attendances,
        ]);
    }

}
