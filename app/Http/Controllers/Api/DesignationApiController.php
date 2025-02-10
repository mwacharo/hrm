<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationApiController extends Controller
{
    public function index()
    {
        $designations = Designation::with('users')->get();

        return response()->json(['designations' => $designations]);
    }

    public function store(Request $request)
    {

        $this->validate($request, ['name' => 'required|max:100']);

        Designation::create($request->all());

        return response()->json(['Success' => 'Designation Created Successifully']);
    }

    public function update(Request $request, Designation $designation)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $designation->update($validatedData);

        return response()->json(['message' => 'Designation updated successfully', 'designation' => $designation], 200);
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();

        return response()->json(['message' => 'Designation deleted successfully']);
    }
}
