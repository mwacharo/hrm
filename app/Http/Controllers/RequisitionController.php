<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequisitionRequest;
use App\Http\Requests\UpdateRequisitionRequest;
use App\Models\Requisition;

class RequisitionController extends Controller
{

    public function index()
    {
        //


        $userId = auth()->id();

        // dd($userId);


        return view('requisitions.requisitions',compact('userId'));

    }

  
}
