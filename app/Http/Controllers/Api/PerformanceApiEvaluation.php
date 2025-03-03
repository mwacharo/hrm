<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerformanceEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PerformanceApiEvaluation extends Controller
{
    //
    // store monthly performance evaluation
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            // 'evaluator_id' => 'required|integer',
            // 'department_id' => 'required|integer',
            // 'evaluation_date' => 'required|date',
            'attendance' => 'required|integer',
            'problems_solved' => 'required|integer',
            'reports_submitted' => 'required|integer',
            'knowledge_of_work' => 'required|integer',
            'team_work' => 'required|integer',
            'reliability_visibility' => 'required|integer',
            'productivity' => 'required|integer',
            'discipline' => 'required|integer',
            'quality_of_work' => 'required|integer',
            'communication' => 'required|integer',
            'total_score' => 'required|integer',
            'percentage' => 'required|numeric',
        ]);

        // Assuming you have a model named PerformanceEvaluation
        $performance = new PerformanceEvaluation($validatedData);
        $performance->save();

        return response()->json(['message' => 'Performance evaluation stored successfully'], 201);
    }

    // update monthly performance evaluation
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            // 'evaluator_id' => 'required|integer',
            // 'department_id' => 'required|integer',
            // 'evaluation_date' => 'required|date',
            'attendance' => 'required|integer',
            'problems_solved' => 'required|integer',
            'reports_submitted' => 'required|integer',
            'knowledge_of_work' => 'required|integer',
            'team_work' => 'required|integer',
            'reliability_visibility' => 'required|integer',
            'productivity' => 'required|integer',
            'discipline' => 'required|integer',
            'quality_of_work' => 'required|integer',
            'communication' => 'required|integer',
            'total_score' => 'required|integer',
            'percentage' => 'required|numeric',
        ]);

        // Assuming you have a model named EmployeePerformance
        $performance = PerformanceEvaluation::find($id);
        if (!$performance) {
            return response()->json(['message' => 'Performance evaluation not found'], 404);
        }
        $performance->update($validatedData);

        return response()->json(['message' => 'Performance evaluation updated successfully'], 200);
    }

    // delete monthly performance evaluation
    public function destroy($id)
    {
        $performance = PerformanceEvaluation::find($id);
        $performance->delete();

        return response()->json(['message' => 'Performance evaluation deleted successfully'], 200);
    }


    // get all monthly performance evaluations
    public function index(Request $request)
    {
        Log::info('Fetching performance evaluations', ['user_id' => $request->user()->id]);
        
        $user = $request->user();

        $role = $user->role;
        $isHod = $user->is_hod;
        $isHr = $user->is_hr; 
        $isManager = $user->designation->name == 'manager';

        Log::info('Fetching performance evaluations', [

            'user_id' => $user->id,
    
            'roles' => $user->getRoleNames() // This will return a collection of role names
    
        ]);

        if ($role == 'employee') {
        if ($user->role == 'employee') {
            Log::info('Role is employee');
            $evaluations = PerformanceEvaluation::where('user_id', $user->id)->with('user')->get();
        } elseif ($isHr) {
            Log::info('Role is hr');
            $evaluations = PerformanceEvaluation::with('user')->get();
        } elseif ($isManager) {
            Log::info('Role is manager');
            $evaluations = PerformanceEvaluation::where('department_id', $user->department_id)->with('user')->get();
        } elseif ($isHod) {
            Log::info('Role is hod');
            $evaluations = PerformanceEvaluation::whereIn('department_id', $user->departments->pluck('id'))->with('user')->get();
        } else {
            return response()->json(['message' => 'Unauthorized', 'role' => $role], 403);
            return response()->json(['message' => 'Unauthorized'], 403);
        }

      

        Log::info('Performance evaluations fetched successfully', ['count' => $evaluations->count()]);
        return response()->json(['evaluations' => $evaluations]);
    }


    
   
}
}