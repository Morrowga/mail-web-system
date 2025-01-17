@php
    // Ensure that we are rendering newlines in plain text format
    $originalEmailContent = nl2br($originalEmailContent);
    $replyContent = nl2br($replyContent);
@endphp

{!! $originalEmailContent !!}

{!! $replyContent !!}
