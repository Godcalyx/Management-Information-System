<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Professor Login Credentials</title>
</head>
<body>
    <h2>Hello {{ $user->name }},</h2>
    <p>You have been registered as a professor.</p>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Temporary Password:</strong> {{ $temporaryPassword }}</p>
    <p>Please log in and change your password immediately.</p>
</body>
</html>
