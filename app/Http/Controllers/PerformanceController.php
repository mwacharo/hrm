<?php

namespace App\Http\Controllers;

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
}
