<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forwarded Email</title>
</head>
<body>
    <span style="white-space: pre-wrap; word-break: break-word; overflow-wrap: break-word;">{!! $emailData['message_content'] !!}</span>

    <hr>

    <!-- Original Message Section -->
    <p>--- Forwarded Message ---</p>
    <p><strong>From:</strong> {{ $originalEmail['sender'] }}</p>
    <p><strong>Sent:</strong> {{ $originalEmail['datetime'] }}</p>
    <p><strong>Subject:</strong> {{ $originalEmail['subject'] }}</p>
    <p>{!! $originalEmail['body'] !!}</p>
</body>
</html>
