<?php

namespace App\Http\Controllers\Api;

use App\Models\Salary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SalaryApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salaries = Salary::with('user')->get();

        return response()->json(['salaries' => $salaries]);
    }


    public function store(Request $request)
    {
        //
    }

   
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salary $salary)
    {
        $salary->delete();

        return response()->json(['message' => 'Salary deleted successfully']);
    }
}
