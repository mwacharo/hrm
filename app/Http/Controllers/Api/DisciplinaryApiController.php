<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Disciplinary;
use Illuminate\Http\Request;

class DisciplinaryApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $disciplinaries = Disciplinary::with('user')->get();

        // Format full name in the API response
        $disciplinaries->each(function ($disciplinary) {
            $disciplinary->user->fullname = $disciplinary->user->firstname . ' ' . $disciplinary->user->lastname;
        });

        return response()->json(['disciplinaries' => $disciplinaries]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'violation' => 'required|string',
            'violation_date' => 'nullable|date',
            'type_of_disciplinary' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx',
            'comment' => 'nullable|string',
        ]);

        $documentName = null;
        if ($request->hasFile('document')) {
            $documentName = time() . '.' . $request->file('document')->extension();
            $request->file('document')->move(public_path('storage/disciplinaries/documents'), $documentName);
        }

        $disciplinary = Disciplinary::create($validatedData);

        return response()->json(['disciplinary' => $disciplinary], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Disciplinary $disciplinary)
    {
        // Example validation, replace with your actual validation rules
        $validatedData = $request->validate([
            'user_id' => 'exists:users,id',
            'violation' => 'string',
            'violation_date' => 'nullable|date',
            'type_of_disciplinary' => 'string',
            'document' => 'nullable|file|mimes:pdf,doc,docx',
            'comment' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store('disciplinary_documents', 'public');
            $validatedData['document'] = $path;
        }

        $disciplinary->update($validatedData);

        return response()->json(['disciplinary' => $disciplinary]);
    }

    public function destroy(Disciplinary $disciplinary)
    {
        $disciplinary->delete();

        return response()->json(['message' => 'Disciplinary deleted successfully']);
    }
}
