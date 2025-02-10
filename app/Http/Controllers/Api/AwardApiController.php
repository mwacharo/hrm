<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Award;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AwardApiController extends Controller
{
    public function index()
    {
        $awards = Award::with('user', 'awardType')->get();

        $formattedAwards = $awards->map(function ($award) {
            return [
                'id' => $award->id,
                'name' => $award->awardType->name,
                'target' => $award->awardType->target,
                'description' => $award->awardType->description,
                'status' => $award->awardType->status,
                'reward' => $award->awardType->reward,
                'employee' => $award->user ? $award->user->firstname . " " . $award->user->lastname : null,
                'department' => $award->user ? $award->user->department->name : null,
                'award_type' => $award->awardType ? $award->awardType->name : null,
                'period' => $award->period,
                'points' => $award->points,
                'created_at' => $award->created_at,
                'updated_at' => $award->updated_at,
            ];
        });

        return response()->json($formattedAwards, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period' => 'required',
            'employee_id' => 'required|integer|exists:users,id',
            'award_type_id' => 'required|integer|exists:award_types,id',
            'points' => 'required',
            'notes' => 'nullable'

        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $award = Award::create([
            'user_id' => $request->employee_id,
            'award_type_id' => $request->award_type_id,
            'notes' => $request->notes,
            'points' => $request->points,
            'period' => $request->period,
        ]);

        return response()->json($award, 201);
    }

    public function update(Request $request, Award $award)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'award_date' => 'required|date',
            'employee_id' => 'required|integer|exists:employees,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $award->update([
            'name' => $request->name,
            'description' => $request->description,
            'award_date' => $request->award_date,
            'employee_id' => $request->employee_id,
        ]);

        return response()->json($award, 200);
    }

    public function destroy(Award $award)
    {
        $award->delete();
        return response()->json(null, 204);
    }
}
