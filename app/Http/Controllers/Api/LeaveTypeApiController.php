<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeApiController extends Controller
{

    public function index()
    {
        $leaveTypes = LeaveType::all();

        return response()->json(['leaveTypes' => $leaveTypes]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'days' => 'required|integer',
            'notes' => 'nullable|string|max:255', // Validation rule for 'comment'
        ]);

        $leaveType = LeaveType::create([
            'name' => $validatedData['name'],
            'days' => $validatedData['days'],
            'comment' => $validatedData['notes'], // Access 'comment' instead of 'notes'
        ]);

        return response()->json([
            'message' => 'Leave type created successfully',
            'createdLeaveType' => $leaveType,
        ], 201);
    }


    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();

        return response()->json(['message' => 'Leave Type deleted successfully']);
    }
}
