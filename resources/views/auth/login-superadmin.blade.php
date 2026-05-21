<!DOCTYPE html>
<html>
<head>
    <title>Super Admin Login</title>
</head>
<body>

<h2>Super Admin Login</h2>

@if ($errors->any())
    <div style="color:red;">
        {{ $errors->first() }}
    </div>
@endif

<form method="POST" action="{{ route('login.superadmin.submit') }}">
    @csrf

    <input type="email" name="email" placeholder="Super Admin Email" required>
    <br><br>

    <input type="password" name="password" placeholder="Password" required>
    <br><br>

    <button type="submit">Login</button>
</form>

</body>
</html>
