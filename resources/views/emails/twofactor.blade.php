<!DOCTYPE html>
<html>
<head>
    <title>Two-Factor Authentication Code</title>
</head>
<body>
    <p>Hello {{ $user->name }},</p>
    <p>Your 2FA code is: <strong>{{ $code }}</strong></p>
    <p>This code will expire in 10 minutes.</p>
</body>
</html>
