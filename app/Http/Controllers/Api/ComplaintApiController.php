<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Complaint;
use Illuminate\Http\Request;
use App\Mail\ComplaintNotification;
use App\Http\Controllers\Controller;
use App\Models\ComplaintLog;
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
        // $links = $request->query('links');

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
                'links' => $complaint->links,
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



    public function store(Request $request)
    {
        Log::info('Complaint request received.', ['request' => $request->all()]);

        // dd($request->file('attachments'));

        try {
            // Validate the request including attachments
            $data = $this->validateComplaint($request);
            $data['status'] = 'Open';
            $data['priority'] = 'Medium';

            // Handle attachments - support both single and multiple files
            $attachments = [];

            if ($request->hasFile('attachments')) {
                $files = is_array($request->file('attachments')) ? $request->file('attachments') : [$request->file('attachments')];
                // dd($files);


                // ray:1 [ // app/Http/Controllers/Api/ComplaintApiController.php:98
                //   0 => Illuminate\Http\UploadedFile {#1329
                //     -test: false
                //     -originalName: "Screenshot from 2025-03-04 14-03-38.png"
                //     -mimeType: "image/png"
                //     -error: 0
                //     #hashName: null
                //     path: "/tmp"
                //     filename: "phpQiJWn1"
                //     basename: "phpQiJWn1"
                //     pathname: "/tmp/phpQiJWn1"
                //     extension: ""
                //     realPath: "/tmp/phpQiJWn1"
                //     aTime: 2025-03-07 13:21:18
                //     mTime: 2025-03-07 13:21:18
                //     cTime: 2025-03-07 13:21:18
                //     inode: 27267214
                //     size: 173381
                //     perms: 0100600
                //     owner: 1000
                //     group: 1000
                //     type: "file"
                //     writable: true
                //     readable: true
                //     executable: false
                //     file: true
                //     dir: false
                //     link: false
                //   }
                // ]


                foreach ($files as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('complaints', $filename, 'public');

                    Log::info('Stored file path:', ['path' => $path]);


                    $storedAttachment = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType()
                    ];

                    $attachments[] = $storedAttachment;

                    Log::info('Attachment details:', [
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize()
                    ]);
                }
            }

            // Ensure attachments are stored as JSON
            $data['attachments'] = json_encode($attachments);

            // Handle links
            if ($request->has('links')) {
                $data['links'] = json_encode($request->input('links'));
            }

            // Create the complaint
            $complaint = Complaint::create($data);
            Log::info('Complaint created successfully.', ['complaint_id' => $complaint->id]);

            // Attach Assigned Users (Role: addressed_to)
            if ($request->filled('addressed_to')) {
                $complaint->users()->syncWithoutDetaching(
                    collect($request->addressed_to)->mapWithKeys(fn($userId) => [$userId => ['role' => 'addressed_to']])
                );
                Log::info('Assigned users added.', ['complaint_id' => $complaint->id, 'users' => $request->addressed_to]);
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
            Log::error('Error storing complaint:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'An error occurred while submitting the complaint: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // proper validation
    protected function validateComplaint(Request $request)
    {
        return $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'addressed_to' => 'required|array',
            'addressed_to.*' => 'exists:users,id',
            'followers' => 'nullable|array',
            'followers.*' => 'exists:users,id',
            'is_anonymous' => 'boolean',
            // 'attachments' => 'nullable|array',
            // 'attachments.*' => 'file|mimes:pdf,doc,docx,png|max:10240',
            'attachments' => 'nullable|file|mimes:pdf,doc,docx.pdf,png|max:10240',

            'links' => 'nullable|array',
            'links.*' => 'string|url',
            'user_id' => 'required|exists:users,id',

        ]);
    }

    private function sendComplaintNotifications(Request $request, Complaint $complaint)
    {
        if ($request->filled('addressed_to')) {
            $assignedToUsers = User::whereIn('id', $request->addressed_to)->get();
            foreach ($assignedToUsers as $user) {
                Mail::to($user->email)->send(new ComplaintNotification($complaint, $user));
            }
            Log::info('Emails sent to assigned users.', ['users' => $request->addressed_to]);
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
        Log::info('Complaint update request received.', ['request' => $request->all()]);

        $complaint = $this->findComplaint($id);

        if (!$complaint) {
            return response()->json(['message' => 'Complaint not found'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->validate([
            'subject' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'addressed_to' => 'nullable|array',
            'addressed_to.*' => 'exists:users,id',
            'followers' => 'nullable|array',
            'followers.*' => 'exists:users,id',
            'is_anonymous' => 'nullable|boolean', // Make is_anonymous nullable
            'attachments' => 'nullable|array', // Change to array to allow multiple files
            'attachments.*' => 'file|mimes:pdf,doc,docx,png|max:10240', // Validate each file
            'links' => 'nullable|array',
            'links.*' => 'string|url',
            'comments' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
        ]);

        // Handle attachments - support both single and multiple files
        $attachments = [];
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');

            // Handle both array and single file upload
            if (is_array($files)) {
                foreach ($files as $file) {
                    if ($file->isValid()) {
                        $path = $file->store('complaints', 'public');
                        $attachments[] = [
                            'original_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'mime_type' => $file->getMimeType(),
                            'size' => $file->getSize()
                        ];
                        Log::info('Document uploaded', ['document' => $file->getClientOriginalName(), 'path' => $path]);
                    } else {
                        Log::warning('Invalid file upload attempt', ['file' => $file->getClientOriginalName()]);
                    }
                }
            } else {
                // Single file upload
                if ($files->isValid()) {
                    $path = $files->store('complaints', 'public');
                    $attachments[] = [
                        'original_name' => $files->getClientOriginalName(),
                        'file_path' => $path,
                        'mime_type' => $files->getMimeType(),
                        'size' => $files->getSize()
                    ];
                    Log::info('Document uploaded', ['document' => $files->getClientOriginalName(), 'path' => $path]);
                } else {
                    Log::warning('Invalid file upload attempt', ['file' => $files->getClientOriginalName()]);
                }
            }
        }

        // Store attachments in JSON format if available
        if (!empty($attachments)) {
            $data['attachments'] = json_encode($attachments);
        }

        // Handle links
        if ($request->has('links')) {
            $data['links'] = json_encode($request->input('links'));
        }

        $complaint->update($data);

        // Attach Assigned Users (Role: addressed_to)
        if ($request->filled('addressed_to')) {
            $complaint->users()->syncWithoutDetaching(
                collect($request->addressed_to)->mapWithKeys(fn($userId) => [$userId => ['role' => 'addressed_to']])
            );
            Log::info('Assigned users updated.', ['complaint_id' => $complaint->id, 'users' => $request->addressed_to]);
        }

        // Attach Followers (Role: follower)
        if ($request->filled('followers')) {
            $complaint->users()->syncWithoutDetaching(
                collect($request->followers)->mapWithKeys(fn($userId) => [$userId => ['role' => 'follower']])
            );
            Log::info('Follower users updated.', ['complaint_id' => $complaint->id, 'users' => $request->followers]);
        }

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
        $this->LogAction($complaint->id, 'Resolved', $request->resolution);

        return response()->json(['complaint' => $complaint], Response::HTTP_OK);
    }

    private function LogAction($complaintId, $action, $details)
    {
        $complaint = $this->findComplaint($complaintId);
        $previousData = $complaint ? $complaint->toArray() : [];

        // Assuming you have a ComplaintAction model to store the actions
        ComplaintLog::create([
            'complaint_id' => $complaintId,
            'user_id' => auth()->id(),
            'action' => $action,
            'old_data' => json_encode($previousData),
            'new_data' => json_encode($complaint->toArray()),
            'performed_at' => Carbon::now(),
        ]);

        Log::info('Action stored.', [
            'complaint_id' => $complaintId,
            'action' => $action,
            'old_data' => $previousData,
            'new_data' => $complaint->toArray()
        ]);
    }
}
