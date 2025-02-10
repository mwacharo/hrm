<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Complaint;
use Illuminate\Http\Request;
use App\Mail\ComplaintNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class ComplaintApiController extends Controller
{

    public function index(Request $request)
    {
        $userId = $request->query('user_id');
        $status = $request->query('status');
        $category = $request->query('category');
        $priority = $request->query('priority');

        $query = Complaint::with(['user', 'assignedTo']);

        // Filtering based on the request parameters
        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        // Fetch and map the complaints
        $complaints = $query->get()->map(function ($complaint) {
            // Safely handle null date fields and format them
            $createdAt = $complaint->created_at ? Carbon::parse($complaint->created_at)->format('D d M Y H:i') : null;
            $updatedAt = $complaint->updated_at ? Carbon::parse($complaint->updated_at)->format('D d M Y H:i') : null;

            // Get the full names of the user and assignedTo person
            $userFullName = $complaint->user ? $complaint->user->firstname . ' ' . $complaint->user->lastname : null;
            $assignedToFullName = $complaint->assignedTo ? $complaint->assignedTo->firstname . ' ' . $complaint->assignedTo->lastname : null;

            return [
                'id' => $complaint->id,
                'subject' => $complaint->subject,
                'description' => $complaint->description,
                'category' => $complaint->category,
                'priority' => $complaint->priority,
                'user_id' => $complaint->user_id,
                'status' => $complaint->status,
                'entry_channel' => $complaint->entry_channel,
                'due_date' => $complaint->due_date,
                'date_opened' => $complaint->date_opened,
                'closed_date' => $complaint->closed_date,
                'attachments' => $complaint->attachments,
                'comments' => $complaint->comments,
                'resolution' => $complaint->resolution,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
                'deleted_at' => $complaint->deleted_at,
                'created_by' => $userFullName,
                'addressed_to' => $assignedToFullName,
            ];
        });

        return response()->json(['complaints' => $complaints], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        // Validate the complaint data
        $data = $this->validateComplaint($request);
        $data['status'] = 'Open';
        $data['priority'] = 'Medium';


        //dd($request->assigned_to);
        // Create the complaint
        $complaint = Complaint::create($data);

        // Send email to the assignedd_to user
        if ($request->assigned_to) {
            $addressedToUser = User::find($request->assigned_to);
            if ($addressedToUser) {
                Mail::to($addressedToUser->email)->send(new ComplaintNotification($complaint, $addressedToUser));
            }
        }

        return response()->json(['complaint' => $complaint], Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $complaint = $this->findComplaint($id);

        if (!$complaint) {
            return response()->json(['message' => 'Complaint not found'], Response::HTTP_NOT_FOUND);
        }

        $data = $this->validateComplaint($request, $id);

        $complaint->update($data);

        return response()->json(['complaint' => $complaint], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $complaint = $this->findComplaint($id);

        if (!$complaint) {
            return response()->json(['message' => 'Complaint not found'], Response::HTTP_NOT_FOUND);
        }

        $complaint->delete();

        return response()->json(['message' => 'Complaint deleted'], Response::HTTP_OK);
    }

    private function validateComplaint(Request $request, $id = null)
    {
        $rules = [
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_anonymous' => 'required|boolean',
            'user_id' => 'nullable|exists:users,id',
            'category' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255', // Allow status to be nullable
            'priority' => 'nullable|string|max:255',
            'date_opened' => 'nullable|date',
            'closed_date' => 'nullable|date',
            'attachment' => 'nullable|string|max:255',
            'comments' => 'nullable|string',
            'addressed_to' => 'nullable|string|max:255',
            'resolution' => 'nullable|string',
        ];

        if ($id) {
            $rules['subject'] = 'sometimes|required|string|max:255';
            $rules['is_anonymous'] = 'sometimes|required|boolean';
            $rules['user_id'] = 'sometimes|required|exists:users,id';
        }

        return $request->validate($rules);
    }

    private function findComplaint($id)
    {
        return Complaint::find($id);
    }
}
