<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

class PerformanceController extends Controller
{
    private function getUserID()
    {
        return auth()->user()->id;
    }

    public function telesalesReport()
    {
        $user_id = $this->getUserID();

        return view('performance.telesales_report', ['user_id' => $user_id]);
    }

    public function awards()
    {
        $user_id = $this->getUserID();

        return view('performance.awards', ['user_id' => $user_id]);
    }

    public function awardTypes()
    {
        $user_id = $this->getUserID();

        return view('performance.award_types', ['user_id' => $user_id]);

    }

    public function appraisals()
    {
        $user_id = $this->getUserID();

        return view('performance.appraisals', ['user_id' => $user_id]);

    }

    public function performanceEvaluations()
    {
        $user_id = $this->getUserID();

        Log::info('performanceEvaluations method called');
        return view('performance.performance_evaluations', ['user_id' => $user_id]);
        // return view('performance.appraisals', ['user_id' => $user_id]);


    }
}
