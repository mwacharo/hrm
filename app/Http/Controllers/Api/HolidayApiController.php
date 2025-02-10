<?php

namespace App\Http\Controllers\Api;

use App\Models\Holiday;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HolidayApiController extends Controller
{

    public function index()
    {
        $holidays = Holiday::all();

        return response()->json(['holidays' => $holidays]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
        ]);



        $holiday = Holiday::create([
            'name' => $validatedData['name'],
            'date' => $validatedData['date'],
            'is_recurring' => 1
        ]);

        return response()->json(['holiday' => $holiday], 201);
    }

    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
