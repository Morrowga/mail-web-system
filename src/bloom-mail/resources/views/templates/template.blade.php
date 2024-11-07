<!-- resources/views/emails/template.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] }}</title>
</head>
<body>
    <h1>{{ $data['message_content'] }}</h1>
    <p>{{ $data['message_content'] }}</p>
</body>
</html>
