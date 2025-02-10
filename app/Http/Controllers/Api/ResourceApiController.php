<?php

namespace App\Http\Controllers\Api;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class ResourceApiController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->input('name');

        $query = Asset::with('issuedTo', 'issuedBy', 'department', 'office');

        if ($name) {
            $query->where('name', $name);
        }

        $resources = $query->get();

        return response()->json(['resources' => $resources], 200);
    }


    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'comment' => 'nullable|string',
            'issuance_date' => 'required',
            'category' => 'required|string|in:Hardware,Software,Stationery,Furniture,Electronics',
            'purchase_date' => 'nullable|date',
            'office_id' => 'nullable|exists:offices,id',
            'department_id' => 'nullable|exists:departments,id',
            'serial_no' => 'required|string|unique:assets,serial_no|max:50',
            'condition' => 'nullable|string|max:255',
            'warranty' => 'nullable|string|max:255',
            'purchase_cost' => 'nullable|numeric|min:0',
            'issued_by' => 'nullable',
            'issued_to' => 'nullable',
        ];

        try {
            $validatedData = $request->validate($rules);

            if ($request->has('issued_to')) {
                $validatedData['is_assigned'] = true;
                $validatedData['issued_by'] = auth()->user()->id;
            } else {
                $validatedData['is_assigned'] = false;
            }

            $asset = Asset::create($validatedData);

            return response()->json(['message' => 'Asset created successfully', 'resource' => $asset], 201);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error creating asset: ' . $e->getMessage());

            // Return error response with error message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Asset $asset)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:Hardware,Software,Stationery,Furniture,Electronics',
            'purchase_date' => 'nullable|date',
            'office_id' => 'nullable|exists:offices,id',
            'department_id' => 'nullable|exists:departments,id',
            'serial_no' => 'required|string|max:50|unique:assets,serial_no,' . $asset->id,
            'condition' => 'nullable|string|max:255',
            'warranty' => 'nullable|string|max:255',
            'purchase_cost' => 'nullable|numeric|min:0',
            'issued_by' => 'nullable|exists:users,issued_by',
            'issued_to' => 'nullable|exists:users,issued_to'
        ];

        $validatedData = $request->validate($rules);

        if ($request->has('issued_to')) {
            $validatedData['is_assigned'] = true;
            $validatedData['issued_by'] = auth()->user()->id;
        } else {
            $validatedData['is_assigned'] = false;
        }

        $validatedData['issued_by'] = auth()->user()->id;

        $asset->update($validatedData);

        return response()->json(['message' => 'Asset updated successfully', 'asset' => $asset], 200);
    }

    public function destroy(Asset $asset)
    {
        if ($asset->user_id !== null) {
            return response()->json(['error' => 'Cannot delete an asset assigned to a user'], 403);
        }

        $asset->delete();

        return response()->json(['message' => 'Asset deleted successfully'], 200);
    }

}
