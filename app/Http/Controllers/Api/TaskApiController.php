<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with('leader', 'department')->get();

        return response()->json(['tasks' => $tasks]);
    }


    public function store(Request $request)
    {

        // Validate incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'user_id' => 'required|exists:users,id', // Assuming users table exists
            'description' => 'nullable|string',

        ]);

        // Create a new task using the Task model
        $task = new Task();
        $task->name = $validatedData['name'];
        $task->department_id = $validatedData['department_id'];
        $task->start_date = $validatedData['start_date'];
        $task->end_date = $validatedData['end_date'];
        $task->leader = $validatedData['user_id'];
        $task->description = $validatedData['description'];

        // Save the task to the database
        $task->save();

        // You can return a response if needed, e.g., the created task
        return response()->json(['message' => 'Task created successfully', 'task' => $task], 201);
    }



    
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
