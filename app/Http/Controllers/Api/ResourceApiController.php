<?php

namespace App\Http\Controllers\Api;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Imports\AssetImport;
use App\Models\AssetLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class ResourceApiController extends Controller
{
    public function index(Request $request)
    {
        $name = $request->input('name');

        // $query = Asset::with('issuedTo', 'issuedBy', 'department', 'office');

        if (Auth::user()->hasRole('admin')) {
            $query = Asset::with('issuedTo', 'issuedBy', 'department', 'office');
        } else {
            $query = Asset::with('issuedTo', 'issuedBy', 'department', 'office')
                ->where('issued_to', Auth::id()); // Fetch only assets issued to the logged-in user
        }

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
            'purchase_date' => 'nullable|date',
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
            Log::info('logAssetAction method called', [
                'asset_id' => $asset->id,
                // 'action' => $action,
                // 'user_id' => $userId
            ]);
            $this->logAssetAction($asset, 'created', json_encode($request->all()), auth()->id());

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
        Log::info('Update request received for asset ID: ' . $asset->id, ['request_data' => $request->all()]);

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
            'purchase_date' => 'nullable|date',
            'issued_by' => 'nullable|exists:users,id',
            'issued_to' => 'nullable|exists:users,id',
            'comment' => 'nullable|string',

        ];

        $validatedData = $request->validate($rules);
        Log::info('Validation successful', ['validated_data' => $validatedData]);

        if ($request->has('issued_to')) {
            $validatedData['is_assigned'] = true;
            $validatedData['issued_by'] = auth()->id();
        } else {
            $validatedData['is_assigned'] = false;
        }

        try {
            $asset->update($validatedData);
            Log::info('Asset updated successfully', ['updated_asset' => $asset]);
            $this->logAssetAction($asset, 'updated', json_encode($request->all()), auth()->id());
        } catch (\Exception $e) {
            Log::error('Error updating asset', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to update asset'], 500);
        }

        return response()->json(['message' => 'Asset updated successfully', 'asset' => $asset], 200);
    }


    public function destroy(Asset $asset)
    {
        // if ($asset->user_id !== null) {
        //     return response()->json(['error' => 'Cannot delete an asset assigned to a user'], 403);
        // }

        try {

            $assetDetails = $asset->toArray();

            log::info('Asset deletion request received', $assetDetails);
            $this->logAssetAction($asset, 'deleted', json_encode($assetDetails), auth()->id());

            $asset->delete();
            return response()->json(['message' => 'Asset deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting asset', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to delete asset'], 500);
        }
    }


    public function reassignAsset(Request $request, Asset $asset)
    {
        $rules = [
            'issued_to' => 'required|exists:users,id',
        ];

        $validatedData = $request->validate($rules);

        try {
            $asset->update([
                'issued_to' => $validatedData['issued_to'],
                'issued_by' => auth()->id(),

            ]);

            Log::info('Asset reassigned successfully', ['asset_id' => $asset->id, 'issued_to' => $validatedData['issued_to']]);

            $this->logAssetAction($asset, 'reassigned', json_encode($request->all()), auth()->id());

            return response()->json(['message' => 'Asset reassigned successfully', 'asset' => $asset], 200);
        } catch (\Exception $e) {
            Log::error('Error reassigning asset', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Failed to reassign asset'], 500);
        }
    }

    public function clearAsset(Request $request, $serial_number)
    {


        // dd($request->all());
        // Find asset by serial_number
        $asset = Asset::where('serial_no', $serial_number)->first();

        if (!$asset) {
            return response()->json(['message' => 'Asset not found'], 404);
        }

        try {
            // Clear assignment
            $asset->update([
                'issued_to' => null,
                'issued_by' => null,
                'comment' => $request->comment,

            ]);

            Log::info('Asset assignment cleared successfully', ['asset_id' => $asset->id]);

            // Log action
            $this->logAssetAction($asset, 'cleared', json_encode($asset->toArray()), auth()->id());

            return response()->json(['message' => 'Asset assignment cleared successfully', 'asset' => $asset], 200);
        } catch (\Exception $e) {
            Log::error('Error clearing asset assignment', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Failed to clear asset assignment'], 500);
        }
    }
    public function resourceLogs($id)
    {
        try {
            $logs = AssetLog::where('asset_id', $id)->with('user')->get();

            if ($logs->isEmpty()) {
                return response()->json(['message' => 'No logs found for this asset'], 404);
            }

            $formattedLogs = $logs->map(function ($log) {
                $details = json_decode($log->details, true);
                $issuedToFullName = 'Unknown User';

                // Get the issued_to username if it exists
                if (isset($details['issued_to'])) {
                    $issuedToUser = User::find($details['issued_to']);
                    if ($issuedToUser) {
                        $issuedToFullName = $issuedToUser->firstname . ' ' . $issuedToUser->lastname;
                    }
                }

                // Parse the details string to a proper PHP array
                $parsedDetails = $details;

                // Replace the numeric ID with the actual name
                if (isset($parsedDetails['issued_to'])) {
                    $parsedDetails['issued_to'] = $issuedToFullName;
                }

                if ($log->user) {
                    $fullName = $log->user->firstname . ' ' . $log->user->lastname;

                    return [
                        'user' => $fullName,
                        'action' => $log->action,
                        'details' => $parsedDetails,
                        'time' => $log->created_at->toDateTimeString(),
                    ];
                } else {
                    return [
                        'user' => 'Unknown User',
                        'action' => $log->action,
                        'details' => $parsedDetails,
                        'time' => $log->created_at->toDateTimeString(),
                    ];
                }
            });

            return response()->json(['logs' => $formattedLogs], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching asset logs', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Failed to fetch asset logs'], 500);
        }
    }
    public function logAssetAction($asset, $action, $details, $userId)
    {
        try {
            AssetLog::create([
                'asset_id' => $asset->id,
                'action' => $action,
                'details' => $details,
                'user_id' => $userId,
            ]);

            Log::info('Asset action logged successfully', [
                'asset_id' => $asset->id,
                'action' => $action,
                'details' => $details,
                'user_id' => $userId,
            ]);
        } catch (\Exception $e) {
            Log::error('Error logging asset action', ['error' => $e->getMessage()]);
        }
    }


    public function upload(Request $request)
    {
        Log::info('Upload request received', ['request_data' => $request->all()]);

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,ods|max:10240',
        ]);

        try {
            $import = new AssetImport();
            Excel::import($import, $request->file('file'));

            $importedData = $import->getImportedData(); // Get parsed data
            Log::info('File imported successfully', ['imported_data' => $importedData]);

            // return response()->json($importedData);

            $createdAssets = []; // Store created assets for response

            foreach ($importedData as $data) {
                // Ensure required fields exist before inserting
                if (!isset($data['serial_no'])) {
                    Log::warning('Skipping row due to missing serial_no', ['row_data' => $data]);
                    continue; // Skip invalid rows
                }

                // Check if asset with the same serial_no already exists
                if (Asset::where('serial_no', $data['serial_no'])->exists()) {
                    Log::warning('Skipping existing asset', ['serial_no' => $data['serial_no']]);
                    continue; // Skip existing assets
                }

                // Create Asset record
                $asset = Asset::create($data);
                $createdAssets[] = $asset;

                // Log asset creation
                Log::info('Asset created successfully', ['asset' => $asset]);
                $this->logAssetAction($asset, 'uploaded', json_encode($data), auth()->id());
            }

            return response()->json([
                'message' => 'Assets uploaded and created successfully',
                'created_assets' => $createdAssets,
            ], 200);
        } catch (\Exception $e) {
            Log::error('File processing error', ['error' => $e->getMessage()]);
            return response()->json([
                'error' => 'File processing error: ' . $e->getMessage()
            ], 500);
        }
    }
}
