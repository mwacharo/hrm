<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitApiController extends Controller
{
    public function index()
    {
        $branches = Unit::with('users', 'offices')->get();

        return response()->json(['branches' => $branches]);
    }

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required',
            'phone' => 'required',
        ]);

        Unit::create($validatedData);

        return response()->json(['Suceess' => 'Unit Created Successifully']);
    }

    public function update(Request $request, Unit $unit)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $unit->update($validatedData);

        return response()->json(['message' => 'Unit updated successfully', 'unit' => $unit], 200);
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return response()->json(['message' => 'Unit deleted successfully']);
    }
}
