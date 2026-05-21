<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            color: #0f172a;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .navbar {
            background-color: #00801e; /* CVSU Blue */
            height: 60px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .navbar .navbar-brand,
        .navbar .nav-link,
        .navbar .dropdown-toggle {
            color: white !important;
        }
        .navbar .nav-link:hover,
        .navbar .dropdown-item:hover {
            color: #ffc107 !important; /* Highlight on hover */
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        body.dark-mode {
            background-color: #0f172a;
            color: #e5e7eb;
        }
        body.dark-mode .navbar {
            background-color: #111827 !important;
            border-bottom: 1px solid #334155;
        }
        body.dark-mode .card,
        body.dark-mode .modal-content,
        body.dark-mode .dropdown-menu,
        body.dark-mode .list-group-item {
            background-color: #111827;
            color: #e5e7eb;
            border-color: #334155;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25);
        }
        body.dark-mode .card-header,
        body.dark-mode .card-footer,
        body.dark-mode .modal-header,
        body.dark-mode .modal-footer {
            border-color: #334155;
        }
        body.dark-mode .bg-light,
        body.dark-mode .table-light,
        body.dark-mode .alert-light {
            background-color: #1f2937 !important;
            color: #e5e7eb !important;
            border-color: #334155 !important;
        }
        body.dark-mode .bg-white {
            background-color: #111827 !important;
            color: #e5e7eb !important;
        }
        body.dark-mode .text-dark,
        body.dark-mode .text-muted,
        body.dark-mode .text-secondary,
        body.dark-mode .text-body {
            color: #cbd5e1 !important;
        }
        body.dark-mode .table {
            --bs-table-color: #e5e7eb;
            --bs-table-bg: #111827;
            --bs-table-border-color: #334155;
            --bs-table-striped-bg: #172033;
            --bs-table-striped-color: #e5e7eb;
            --bs-table-active-bg: #1f2937;
            --bs-table-active-color: #f8fafc;
            --bs-table-hover-bg: #1b2538;
            --bs-table-hover-color: #f8fafc;
            color: var(--bs-table-color);
        }
        body.dark-mode .table-dark,
        body.dark-mode .table-success,
        body.dark-mode .table-primary,
        body.dark-mode .table-info {
            --bs-table-bg: #1e293b;
            --bs-table-color: #f8fafc;
            --bs-table-border-color: #334155;
        }
        body.dark-mode .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        body.dark-mode .badge.bg-light {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }
        body.dark-mode .btn-outline-secondary,
        body.dark-mode .btn-outline-dark {
            color: #e5e7eb;
            border-color: #64748b;
        }
        body.dark-mode .btn-outline-secondary:hover,
        body.dark-mode .btn-outline-dark:hover {
            background-color: #1f2937;
            color: #f8fafc;
            border-color: #94a3b8;
        }
    </style>
</head>
<body data-layout-shell="superadmin">

    <!-- Top Navbar -->
<nav class="navbar navbar-dark" data-shell-fragment="navbar">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <!-- Left: System Name -->
        <span class="navbar-brand fw-bold">
            LSHS-OMIS | Super Admin Panel
        </span>

        <!-- Right: User + Logout -->
        <div class="d-flex align-items-center gap-3 text-white">
            <span class="fw-semibold">
                <i class="bi bi-person-circle me-1"></i>
                {{ auth()->user()->name }} 
<small class="opacity-75">({{ ucfirst(auth()->user()->role) }})</small>

            </span>
            <button type="button" class="btn btn-sm btn-outline-light" id="themeToggleBtn" aria-label="Toggle theme">
                <i id="themeIcon" class="bi bi-moon-fill"></i>
            </button>

            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </div>

    </div>
</nav>


    <!-- Main content -->
    <div class="container-fluid mt-4" data-shell-fragment="content">
        @yield('content')
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/persistent-layout.js') }}"></script>
<script>
    function applySuperadminTheme(theme) {
        const themeIcon = document.getElementById('themeIcon');

        if (theme === 'dark') {
            document.body.classList.add('dark-mode');
            themeIcon?.classList.replace('bi-moon-fill', 'bi-sun-fill');
        } else {
            document.body.classList.remove('dark-mode');
            themeIcon?.classList.replace('bi-sun-fill', 'bi-moon-fill');
        }

        localStorage.setItem('theme', theme);
    }

    function initializeSuperadminLayoutShell() {
        const themeToggleBtn = document.getElementById('themeToggleBtn');

        applySuperadminTheme(localStorage.getItem('theme') === 'dark' ? 'dark' : 'light');

        if (themeToggleBtn && themeToggleBtn.dataset.bound !== 'true') {
            themeToggleBtn.dataset.bound = 'true';
            themeToggleBtn.addEventListener('click', () => {
                applySuperadminTheme(document.body.classList.contains('dark-mode') ? 'light' : 'dark');
            });
        }
    }

    initializeSuperadminLayoutShell();
    window.addEventListener('persistent-nav:render', initializeSuperadminLayoutShell);
</script>
<div hidden data-page-scripts></div>
</body>
</html>
