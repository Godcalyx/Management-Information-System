<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CvSU Portal')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar {
            background-color: #014421;
            color: white;
        }
        .navbar .navbar-brand {
            color: white;
            font-weight: bold;
        }
        .navbar .nav-link {
            color: white;
        }
        .container-boxed {
            max-width: 960px;
            margin: auto;
            padding-top: 40px;
        }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg px-4">
        <div class="container-fluid justify-content-between">
<a class="navbar-brand d-flex align-items-center" href="{{ route('login.student') }}">
    <img src="{{ asset('images/logo123-removebg-preview.png') }}" alt="CvSU" style="width:54px; border-radius:6px; margin-right:8px;">
    <span>CvSU Portal</span>
</a>
            {{-- <a class="nav-link" href="{{ route('enroll.form') }}">Not enrolled yet? Enroll now!</a> --}}
        </div>
    </nav>

    <!-- Page Content -->
    <div class="container-boxed">
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
<!-- This is the guest layout for the CvSU Portal. It includes a top navigation bar and a container for the page content. -->