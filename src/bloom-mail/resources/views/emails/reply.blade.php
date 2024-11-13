<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <p>{{ $emailData['message_content'] }}</p>

    <hr>

    <!-- Original Message Section -->
    <p>--- Original Message ---</p>
    <p><strong>From:</strong> {{ $originalEmail['sender'] }}</p>
    <p><strong>Sent:</strong> {{ $originalEmail['datetime'] }}</p>
    <p><strong>Subject:</strong> {{ $originalEmail['subject'] }}</p>
    <p>{!! $originalEmail['body'] !!}</p>
</body>
</html>
