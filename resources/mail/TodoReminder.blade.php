<!DOCTYPE html>
<html>
<head>
    <title>Todo Reminder</title>
</head>
<body>
    <h1>Todo Reminder</h1>
    <p>Hi {{ $user->name }},</p>
    <p>This is a reminder for your pending tasks:</p>
    <ul>
        @foreach ($todos as $todo)
            <li>{{ $todo->task }} - Due: {{ $todo->due_date->format('M d, Y') }}</li>
        @endforeach
    </ul>
    <p>Please make sure to complete them on time.</p>
    <p>Thank you!</p>
</body>
</html>