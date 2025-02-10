<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LeaveExport implements FromView
{
    protected $leaves;

    public function __construct($leaves)
    {
        $this->leaves = $leaves;
    }

    public function view(): View
    {

        return view('reports.leaves-report-excel', [
            'leaves' => $this->leaves,
        ]);
    }

}
