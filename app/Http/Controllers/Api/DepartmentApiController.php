<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentApiController extends Controller
{
    public function index()
    {
        $departments = Department::with(['users'])->get()->map(function ($department) {
            return [
                'id' => $department->id,
                'name' => $department->name,
                // 'hod' => $department->hod ? $department->hod->firstname . ' ' . $department->hod->lastname : 'Null',
                // 'manager' => $department->manager ? $department->manager->firstname . ' ' . $department->manager->lastname : 'Null',
                'users' => $department->users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->firstname . ' ' . $user->lastname,
                        'email' => $user->email,
                    ];
                })->toArray(),
            ];
        });

        return response()->json(['departments' => $departments]);
    }

    public function store(Request $request)
    {

        $this->validate($request, ['name' => 'required|max:100']);

        Department::create($request->all());

        return response()->json(['Success' => 'Department Created Successifully']);
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $department->update([
            'name' => $request->input('name'),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Department updated successfully'], 200);
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return response()->json(['message' => 'Department deleted successfully']);
    }
}
