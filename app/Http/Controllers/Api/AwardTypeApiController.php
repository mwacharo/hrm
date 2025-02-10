<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AwardType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AwardTypeApiController extends Controller
{

    public function index()
    {
        $awardTypes = AwardType::all();
        return response()->json($awardTypes);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'reward' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'target' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $awardType = AwardType::create($request->all());
        return response()->json($awardType, 201);
    }

    public function update(Request $request, AwardType $awardType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'reward' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'boolean',
            'target' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $awardType->update($request->all());
        return response()->json($awardType);
    }

    public function destroy(AwardType $awardType)
    {
        $awardType->delete();
        return response()->json(['message' => 'Award type deleted successfully']);
    }
}
