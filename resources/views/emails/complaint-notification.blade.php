<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Complaint Notification</title>
</head>
<body>
    <h1>New Complaint Received</h1>
    <p>A new complaint has been registered:</p>
    <p><strong>Subject:</strong> {{ $complaint->subject }}</p>
    <p><strong>Description:</strong> {{ $complaint->description }}</p>
    <p><strong>Priority:</strong> {{ $complaint->priority }}</p>
    <p><strong>Status:</strong> {{ $complaint->status }}</p>
</body>
</html>
