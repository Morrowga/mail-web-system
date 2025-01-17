@php
    // Ensure that we are rendering newlines in plain text format
    $originalEmailContent = nl2br($originalContent);
    $replyContent = nl2br($replyContent);
@endphp

{!! $replyContent !!}

{!! $originalEmailContent !!}

