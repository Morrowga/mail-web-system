<!-- resources/views/emails/template.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] }}</title>
</head>
<body>
    <span style="white-space: pre-wrap; word-break: break-word; overflow-wrap: break-word;">{!! $data->body !!}</span>
</body>
</html>
