<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Complaint;
use Illuminate\Http\Request;
use App\Mail\ComplaintNotification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
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

        // Load related users: creator, addressedTo, and followers
        $query = Complaint::with(['user', 'addressedTo', 'followers']);

        // Apply filters
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
                'created_at' => $complaint->created_at ? Carbon::parse($complaint->created_at)->format('D d M Y H:i') : null,
                'updated_at' => $complaint->updated_at ? Carbon::parse($complaint->updated_at)->format('D d M Y H:i') : null,
                'deleted_at' => $complaint->deleted_at,

                // Get the full name of the creator
                'created_by' => $complaint->user ? $complaint->user->firstname . ' ' . $complaint->user->lastname : null,

                // Get all assigned users as an array of full names
                'addressed_to' => $complaint->addressedTo->map(fn($user) => $user->firstname . ' ' . $user->lastname)->toArray(),

                // Get all followers as an array of full names
                'followers' => $complaint->followers->map(fn($user) => $user->firstname . ' ' . $user->lastname)->toArray(),
            ];
        });

        return response()->json(['complaints' => $complaints], Response::HTTP_OK);
    }

    // public function store(Request $request)
    // {
    //     $res= $request->all();

    //     Log::info('Complaint request received.', ['request' => $request->all()]);
    //      dd($res);
    //     // Validate the request including attachments
    //     $data = $this->validateComplaint($request);
    //     $data['status'] = 'Open';
    //     $data['priority'] = 'Medium'; 

    //     $attachments = [];
    //     if ($request->hasFile('attachments')) {
    //         $attachments = [];
    //         foreach ($request->file('attachments') as $file) {
    //             if ($file->isValid()) {
    //                 $path = $file->store('complaints', 'public');
    //                 $attachments[] = $path;
    //             }
    //         }

    //         if (!empty($attachments)) {
    //             $data['attachments'] = json_encode($attachments);
    //         }
    //     }

    //     // Log the attachments
    //     Log::info('Attachments Stored:', ['attachments' => $attachments]);


    //     // Create the complaint
    //     $complaint = Complaint::create($data);

    //     // Attach Assigned Users (Role: addressed_to)
    //     if ($request->has('assigned_to')) {
    //         $complaint->users()->syncWithoutDetaching(
    //             collect($request->assigned_to)->mapWithKeys(function ($userId) {
    //                 return [$userId => ['role' => 'addressed_to']];
    //             })->toArray()
    //         );
    //         Log::info('Assigned users added.', ['complaint_id' => $complaint->id, 'users' => $request->assigned_to]);
    //     }

    //     // Attach Followers (Role: follower)
    //     if ($request->has('followers')) {
    //         $complaint->users()->syncWithoutDetaching(
    //             collect($request->followers)->mapWithKeys(function ($userId) {
    //                 return [$userId => ['role' => 'follower']];
    //             })->toArray()
    //         );
    //         Log::info('Follower users added.', ['complaint_id' => $complaint->id, 'users' => $request->followers]);
    //     }


    //     // Send email to assigned user
    //     if ($request->assigned_to) {
    //         $assignedToUsers = User::whereIn('id', $request->assigned_to)->get();
    //         foreach ($assignedToUsers as $addressedToUser) {
    //             Mail::to($addressedToUser->email)->send(new ComplaintNotification($complaint, $addressedToUser));
    //         }
    //     }

    //     if ($request->followers) {
    //         $followerUsers = User::whereIn('id', $request->followers)->get();
    //         foreach ($followerUsers as $followerUser) {
    //             Mail::to($followerUser->email)->send(new ComplaintNotification($complaint, $followerUser));
    //         }
    //     }

    //     return response()->json(['complaint' => $complaint], Response::HTTP_CREATED);
    // }





    public function store(Request $request)
    {
        Log::info('Complaint request received.', ['request' => $request->all()]);

        try {
            // Validate the request including attachments
            $data = $this->validateComplaint($request);
            $data['status'] = 'Open';
            $data['priority'] = 'Medium';

            // Handle attachments
            // $attachments = [];
            // if ($request->hasFile('attachments')) {
            //     foreach ((array) $request->file('attachments') as $file) {
            //         if ($file->isValid()) {
            //             $path = $file->store('complaints', 'public');
            //             $attachments[] = $path;
            //         }
            //     }
            // Handle a single attachment
            $attachments = [];
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                if ($file->isValid()) {
                    $path = $file->store('complaints', 'public');
                    $attachments[] = $path;
                }
            }



            // Store attachments in JSON format if available
            if (!empty($attachments)) {
                $data['attachments'] = json_encode($attachments);
            }

            // Log stored attachments
            Log::info('Attachments Stored:', ['attachments' => $attachments]);

            // Create the complaint
            $complaint = Complaint::create($data);
            Log::info('Complaint created successfully.', ['complaint_id' => $complaint->id]);

            // Attach Assigned Users (Role: addressed_to)
            if ($request->filled('assigned_to')) {
                $complaint->users()->syncWithoutDetaching(
                    collect($request->assigned_to)->mapWithKeys(fn($userId) => [$userId => ['role' => 'addressed_to']])
                );
                Log::info('Assigned users added.', ['complaint_id' => $complaint->id, 'users' => $request->assigned_to]);
            }

            // Attach Followers (Role: follower)
            if ($request->filled('followers')) {
                $complaint->users()->syncWithoutDetaching(
                    collect($request->followers)->mapWithKeys(fn($userId) => [$userId => ['role' => 'follower']])
                );
                Log::info('Follower users added.', ['complaint_id' => $complaint->id, 'users' => $request->followers]);
            }

            // Send email notifications
            $this->sendComplaintNotifications($request, $complaint);

            return response()->json(['complaint' => $complaint], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error storing complaint:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred while submitting the complaint.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    private function sendComplaintNotifications(Request $request, Complaint $complaint)
    {
        if ($request->filled('assigned_to')) {
            $assignedToUsers = User::whereIn('id', $request->assigned_to)->get();
            foreach ($assignedToUsers as $user) {
                Mail::to($user->email)->send(new ComplaintNotification($complaint, $user));
            }
            Log::info('Emails sent to assigned users.', ['users' => $request->assigned_to]);
        }

        if ($request->filled('followers')) {
            $followerUsers = User::whereIn('id', $request->followers)->get();
            foreach ($followerUsers as $user) {
                Mail::to($user->email)->send(new ComplaintNotification($complaint, $user));
            }
            Log::info('Emails sent to followers.', ['users' => $request->followers]);
        }
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



    public function addComment(Request $request, $id)
    {
        $complaint = $this->findComplaint($id);

        if (!$complaint) {
            return response()->json(['message' => 'Complaint not found'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'comment' => 'required|string',
        ]);

        $complaint->comments = $complaint->comments ? $complaint->comments . "\n" . $request->comment : $request->comment;
        $complaint->save();

        Log::info('Comment added to complaint.', ['complaint_id' => $complaint->id, 'comment' => $request->comment]);
        $this->storeAction($complaint->id, 'Comment Added', $request->comment);

        return response()->json(['complaint' => $complaint], Response::HTTP_OK);
    }

    public function updateStatus(Request $request, $id)
    {
        $complaint = $this->findComplaint($id);

        if (!$complaint) {
            return response()->json(['message' => 'Complaint not found'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'status' => 'required|string|max:255',
        ]);

        $complaint->status = $request->status;
        $complaint->save();

        Log::info('Complaint status updated.', ['complaint_id' => $complaint->id, 'status' => $request->status]);
        $this->storeAction($complaint->id, 'Status Updated', $request->status);

        return response()->json(['complaint' => $complaint], Response::HTTP_OK);
    }

    public function addResolution(Request $request, $id)
    {
        $complaint = $this->findComplaint($id);

        if (!$complaint) {
            return response()->json(['message' => 'Complaint not found'], Response::HTTP_NOT_FOUND);
        }

        $request->validate([
            'resolution' => 'required|string',
        ]);

        $complaint->resolution = $request->resolution;
        $complaint->status = 'Resolved';
        $complaint->closed_date = Carbon::now();
        $complaint->save();

        Log::info('Complaint resolved.', ['complaint_id' => $complaint->id, 'resolution' => $request->resolution]);
        $this->storeAction($complaint->id, 'Resolved', $request->resolution);

        return response()->json(['complaint' => $complaint], Response::HTTP_OK);
    }

    private function storeAction($complaintId, $action, $details)
    {
        $complaint = $this->findComplaint($complaintId);
        $previousData = $complaint ? $complaint->toArray() : [];

        // Assuming you have a ComplaintAction model to store the actions
        ComplaintAction::create([
            'complaint_id' => $complaintId,
            'action' => $action,
            'details' => $details,
            'performed_at' => Carbon::now(),
        ]);

        Log::info('Action stored.', [
            'complaint_id' => $complaintId,
            'action' => $action,
            'details' => $details,
            'previous_data' => $previousData
        ]);
    }
}





