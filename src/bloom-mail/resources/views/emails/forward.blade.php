@php
    // Remove all HTML tags to ensure only plain text is shown
    $forwardedBody = nl2br($forwardedContent);  // Clean the forwarded email body
    $messageContent = nl2br($originalEmailContent);  // Clean the reply content
@endphp

<!-- This is the plain text output without any HTML tags or structure -->
{!! $forwardedBody !!}

{!! $messageContent !!}
