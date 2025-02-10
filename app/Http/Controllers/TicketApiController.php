<?php

namespace App\Http\Controllers;

use App\Mail\TicketAssigned;
use App\Models\ActivityLog;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketFollower;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TicketApiController extends Controller
{

    public function index(Request $request)
    {
        $userId = $request->query('user_id');
        $status = $request->query('status');
        $category = $request->query('category');
        $priority = $request->query('priority');

        $query = Ticket::with(['user', 'addressedTo', 'followers.user']);

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

        $query->orderBy('created_at', 'desc');

        $tickets = $query->get()->map(function ($ticket) {
            $formattedCreatedAt = Carbon::parse($ticket->created_at)->format('D d M Y H:i');
            $formattedUpdatedAt = Carbon::parse($ticket->updated_at)->format('D d M Y H:i');

            $userFullName = optional($ticket->user)->firstname . ' ' . optional($ticket->user)->lastname;
            $addressedToFullName = optional($ticket->addressedTo)->firstname . ' ' . optional($ticket->addressedTo)->lastname;

            $followersFullNames = $ticket->followers->map(function ($follower) {
                return optional($follower->user)->firstname . ' ' . optional($follower->user)->lastname;
            })->implode(', ');

            return [
                'id' => $ticket->id,
                'title' => $ticket->title,
                'category' => $ticket->category,
                'priority' => $ticket->priority,
                'user_id' => $ticket->user_id,
                'status' => $ticket->status,
                'entry_channel' => $ticket->entry_channel,
                'due_date' => $ticket->due_date,
                'date_opened' => $ticket->date_opened,
                'closed_date' => $ticket->closed_date,
                'attachments' => $ticket->attachments,
                'comments' => $ticket->comments,
                'resolution' => $ticket->resolution,
                'created_at' => $formattedCreatedAt,
                'updated_at' => $formattedUpdatedAt,
                'deleted_at' => $ticket->deleted_at,
                'created_by' => $userFullName,
                'addressed_to' => $addressedToFullName,
                'followers' => $followersFullNames,
            ];
        });

        return response()->json(['tickets' => $tickets], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string',
            'priority' => 'nullable|string',
            'entry_channel' => 'nullable|string',
            'due_date' => 'nullable|date',
            'date_opened' => 'nullable|date',
            'closed_date' => 'nullable|date',
            'attachments' => 'nullable|array',
            'comments' => 'nullable|string',
            'addressed_to' => 'nullable|integer|exists:users,id',
            'resolution' => 'nullable|string',
            'followers' => 'nullable|array',
        ]);

        $validatedData['user_id'] = auth()->id();
        $validatedData['status'] = 'Open';
        $validatedData['priority'] = 'Medium';

        $ticket = Ticket::create($validatedData);

        if (!empty($validatedData['followers'])) {
            foreach ($validatedData['followers'] as $followerId) {
                TicketFollower::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $followerId,
                ]);
            }
        }
        $user = auth()->user();
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'Ticket Created',
            'description' => "Ticket '{$ticket->title}' was created by {$user->firstname}  {$user->lastname}",
        ]);

        // Send email to the addressed_to user
        if ($ticket->addressed_to) {
            $addressedToUser = User::find($ticket->addressed_to);
            if ($addressedToUser) {
                Mail::to($addressedToUser->email)->send(new TicketAssigned($ticket, $addressedToUser));
            }
        }

        return response()->json(['ticket' => $ticket], 201);
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'category' => 'nullable|string',
            'priority' => 'nullable|string',
            'status' => 'sometimes|required|string',
            'entry_channel' => 'nullable|string',
            'due_date' => 'nullable|date',
            'date_opened' => 'nullable|date',
            'closed_date' => 'nullable|date',
            'attachments' => 'nullable|array',
            'comments' => 'nullable|string',
            'resolution' => 'nullable|string',
            'addressed_to' => 'nullable|integer|exists:users,id',
            'followers' => 'nullable|array',
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->update($validatedData);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Ticket Updated',
            'description' => "Ticket '{$ticket->title}' was updated by " . auth()->user()->firstname . " " . auth()->user()->lastname,
        ]);

        return response()->json(['ticket' => $ticket], 200);
    }

    public function assignUser(Request $request, Ticket $ticket)
    {
        $validatedData = $request->validate([
            'addressed_to' => 'required|exists:users,id',
        ]);

        $ticket->addressed_to = $validatedData['addressed_to'];
        $ticket->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'User Assigned',
            'description' => "User ID {$validatedData['addressed_to']} was assigned to ticket '{$ticket->title}'",
        ]);

        return response()->json(['ticket' => $ticket], 200);
    }

    public function markAsClosed(Ticket $ticket)
    {
        $ticket->status = 'Closed';
        $ticket->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Ticket Closed',
            'description' => "Ticket '{$ticket->title}' was marked as closed by user ID " . auth()->id(),
        ]);

        return response()->json(['ticket' => $ticket], 200);
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        $validatedData = $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment = new TicketComment();
        $comment->ticket_id = $ticket->id;
        $comment->user_id = auth()->id();
        $comment->comment = $validatedData['comment'];
        $comment->save();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Comment Added',
            'description' => "Comment added to ticket '{$ticket->title}' by " . auth()->user()->firstname . " " . auth()->user()->lastname,
        ]);

        return response()->json([
            'message' => 'Comment added successfully!',
            'comment' => $comment,
        ]);
    }

    public function ticketComments(Ticket $ticket)
    {
        $ticketComments = TicketComment::with('user')
            ->where('ticket_id', $ticket->id)
            ->get()
            ->map(function ($comment) {
                return [
                    'user' => $comment->user->firstname . ' ' . $comment->user->lastname,
                    'time' => $comment->created_at->diffForHumans(),
                    'comment' => $comment->comment,
                ];
            });

        return response()->json(['ticketComments' => $ticketComments]);
    }

    public function destroy(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticketTitle = $ticket->title;
        $ticket->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'Ticket Deleted',
            'description' => "Ticket '{$ticketTitle}' was deleted by " . auth()->user()->firstname . " " . auth()->user()->lastname,
        ]);

        return response()->json(['message' => 'Ticket deleted successfully'], 200);
    }
}
