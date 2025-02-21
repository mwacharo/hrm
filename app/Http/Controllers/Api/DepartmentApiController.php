<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\HodDepartment;
use Illuminate\Http\Request;

class DepartmentApiController extends Controller
{
    // public function index()
    // {
    //     $departments = Department::with(['users','hods'])->get()->map(function ($department) {
    //         return [
    //             'id' => $department->id,
    //             'name' => $department->name,
    //             // 'hod' => $department->hod ? $department->hod->firstname . ' ' . $department->hod->lastname : 'Null',
    //             // 'manager' => $department->manager ? $department->manager->firstname . ' ' . $department->manager->lastname : 'Null',
    //             'users' => $department->users->map(function ($user) {
    //                 return [
    //                     'id' => $user->id,
    //                     'name' => $user->firstname . ' ' . $user->lastname,
    //                     'email' => $user->email,
    //                 ];
    //             })->toArray(),
    //         ];
    //     });

    //     return response()->json(['departments' => $departments]);
    // }


    public function index()
    {
        $departments = Department::with(['users', 'hods'])->get()->map(function ($department) {
            return [
                'id' => $department->id,
                'name' => $department->name,
                'users' => $department->users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->firstname . ' ' . $user->lastname,
                        'email' => $user->email,
                    ];
                })->toArray(),
                'hods' => $department->hods->map(function ($hod) {
                    return [
                        'id' => $hod->id,
                        'name' => $hod->firstname . ' ' . $hod->lastname,
                        'email' => $hod->email,
                    ];
                })->toArray(),
                'hods_display' => $department->hods->map(fn($hod) => $hod->firstname . ' ' . $hod->lastname)->join(', '), // Fix here
            ];
        });
    
        return response()->json(['departments' => $departments]);
    }
    

    public function store(Request $request)
    {

        $this->validate($request, ['name' => 'required|max:100',
        'hod_id' => 'required|exists:users,id']
    
       
    );

        Department::create($request->all());

        return response()->json(['Success' => 'Department Created Successifully']);
    }

   

    
    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'hod_id' => 'required|exists:users,id', // Ensure HOD user exists
        ]);
    
        // Update department name
        $department->update([
            'name' => $request->input('name'),
            'updated_at' => now(),
        ]);
    
        // Check if the HOD department record exists
        $hodDepartment = HodDepartment::where('department_id', $department->id)->first();
    
        if (!$hodDepartment) {
            // If it doesn't exist, create a new record
            HODDepartment::create([
                'user_id' => $request->input('hod_id'), // HOD User ID
                'department_id' => $department->id,
            ]);
        } else {
            // If it exists, update the HOD
            $hodDepartment->update([
                'user_id' => $request->input('hod_id'),
            ]);
        }
    
        return response()->json(['message' => 'Department and HOD updated successfully'], 200);
    }
    

    public function destroy(Department $department)
    {
        $department->delete();

        return response()->json(['message' => 'Department deleted successfully']);
    }
}
