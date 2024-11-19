<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <span style="white-space: pre-wrap; word-break: break-word; overflow-wrap: break-word;">{!! $emailData['message_content'] !!}</span>

    <hr>

    <div style="margin-top: 5px;">
        <span>
            {{ $replyMailData->template?->subject ?? ''}}
        </span>
    </div>

    <div style="margin-top: 3px;">
        <span style="white-space: pre-wrap; word-break: break-word; overflow-wrap: break-word;">{!! $replyMailData->template?->message_content ?? '' !!}</span>
    </div>

    <hr>

    <!-- Original Message Section -->
    <p>--- Original Message ---</p>
    <p><strong>From:</strong> {{ $originalEmail['sender'] }}</p>
    <p><strong>Sent:</strong> {{ $originalEmail['datetime'] }}</p>
    <p><strong>Subject:</strong> {{ $originalEmail['subject'] }}</p>
    <p>{!! $originalEmail['body'] !!}</p>
</body>
</html>
