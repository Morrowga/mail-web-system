<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forwarded Email</title>
</head>
<body>
    <p>{{ $emailData['message_content'] }}</p>

    <hr>

    <!-- Original Message Section -->
    <p>--- Forwarded Message ---</p>
    <p><strong>From:</strong> {{ $originalEmail['sender'] }}</p>
    <p><strong>Sent:</strong> {{ $originalEmail['datetime'] }}</p>
    <p><strong>Subject:</strong> {{ $originalEmail['subject'] }}</p>
    <p>{!! nl2br(e($originalEmail['body'])) !!}</p>
</body>
</html>
