<?php

namespace App\Http\Controllers;

use App\Models\Policy;

class CompanyController extends Controller
{
    public function companyProfile()
    {
        return view("company.profile");
    }

    public function companyPolicies()
    {
        $policies = Policy::get();
        return view("company.policies", compact('policies'));
    }

    public function companyTree()
    {
        return view("company.organisation_tree");
    }
}
