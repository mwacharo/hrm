<!DOCTYPE html>
<html>
<head>
    <title>New Ticket Assigned</title>
</head>
<body>
    <p>Hi {{ $user->firstname }},</p>

    <p>You have been assigned a new ticket titled "<strong>{{ $ticket->title }}</strong>". Please review it at your earliest convenience.</p>

    <p>Ticket Details:</p>
    <ul>
        <li>Title: {{ $ticket->title }}</li>
        <li>Category: {{ $ticket->category }}</li>
        <li>Priority: {{ $ticket->priority }}</li>
        <li>Status: {{ $ticket->status }}</li>
    </ul>

    <p>Best regards,</p>
    <p>{{env('APP_NAME')}}</p>
</body>
</html>
