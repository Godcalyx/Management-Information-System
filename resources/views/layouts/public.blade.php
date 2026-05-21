<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'LSHS')</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-light bg-light px-4 py-2">
        <a href="/" class="navbar-brand">LSHS</a>

        <div>
            <a href="/about" class="mx-2">About</a>
            <a href="/news" class="mx-2">News</a>
            <a href="/contact" class="mx-2">Contact</a>

            <a href="/login" class="btn btn-primary btn-sm ms-3">Login</a>
        </div>
    </nav>

    <!-- Page Contents -->
    <main class="container py-4">
        @yield('content')
    </main>

</body>
</html>
