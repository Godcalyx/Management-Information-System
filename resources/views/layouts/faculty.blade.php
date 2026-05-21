  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}

    <title>Professor Panel</title>

    <!-- Aptos Local Font -->
    <style>
      @font-face {
        font-family: 'Aptos';
        src: url('{{ asset('fonts/Aptos.ttf') }}') format('truetype'),
            url('{{ asset('fonts/Aptos-Bold.ttf') }}') format('truetype');
        font-weight: normal;
        font-style: normal;
      }
    </style>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
      /* Preloader */
      #preloader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
      }

      .loader-logo {
        width: 200px;
        animation: pulse 5.5s infinite;
      }

      @keyframes pulse {
        0% { transform: scale(1); opacity: 0.9; }
        50% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(1); opacity: 0.9; }
      }

      body {
        font-family: 'Aptos', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f8f9fa;
        color: #0f172a;
        text-transform: uppercase;
        transition: background-color 0.3s, color 0.3s;
      }

      .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 280px;
        height: 100vh;
        background-color: #14532d;
        color: white;
        padding: 20px 18px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        z-index: 1000;
        letter-spacing: 0.5px;
      }

      .sidebar a {
        color: white;
        text-decoration: none;
        display: block;
        padding: 10px 15px;
        border-radius: 5px;
        font-size: 14px;
        transition: 0.3s;
      }

      .sidebar a:hover,
      .sidebar a.active {
        background-color: #facc15;
        color: #14532d;
        font-weight: bold;
      }

      .main-content {
        margin-left: 280px;
        padding: 25px;
        font-size: 15px;
      }

      .logout-btn-wrapper {
        margin-top: auto;
      }

      @media (max-width: 768px) {
        .sidebar {
          transform: translateX(-100%);
          transition: transform 0.3s ease;
        }
        .sidebar.active {
          transform: translateX(0);
        }
        .main-content {
          margin-left: 0;
        }
      }

      /* Dark Mode */
      body.dark-mode {
        background-color: #0f172a;
        color: #e5e7eb;
      }

      body.dark-mode .sidebar {
        background-color: #111827;
        color: #e5e7eb;
      }

      body.dark-mode .sidebar a {
        color: #e5e7eb;
      }

      body.dark-mode .sidebar a:hover,
      body.dark-mode .sidebar a.active {
        background-color: #facc15;
        color: #0f172a;
      }

      body.dark-mode .main-content {
        background-color: transparent;
        color: inherit;
      }

      body.dark-mode .navbar {
        background-color: #111827 !important;
        color: #e5e7eb;
        border-bottom: 1px solid #334155;
      }

      body.dark-mode .container,
      body.dark-mode .container-fluid,
      body.dark-mode .row,
      body.dark-mode .col,
      body.dark-mode .col-12 {
        color: inherit;
      }

      body.dark-mode .card,
      body.dark-mode .modal-content,
      body.dark-mode .offcanvas,
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

      body.dark-mode .border,
      body.dark-mode .border-top,
      body.dark-mode .border-bottom,
      body.dark-mode .border-start,
      body.dark-mode .border-end,
      body.dark-mode .border-light {
        border-color: #334155 !important;
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

      body.dark-mode .form-control,
      body.dark-mode .form-select,
      body.dark-mode .form-control:disabled,
      body.dark-mode .form-select:disabled {
        background-color: #0f172a;
        color: #e5e7eb;
        border-color: #475569;
      }

      body.dark-mode .form-control::placeholder {
        color: #94a3b8;
      }

      body.dark-mode .form-control:focus,
      body.dark-mode .form-select:focus {
        background-color: #0f172a;
        color: #f8fafc;
        border-color: #22c55e;
        box-shadow: 0 0 0 0.25rem rgba(34, 197, 94, 0.2);
      }

      body.dark-mode .form-check-input {
        background-color: #0f172a;
        border-color: #64748b;
      }

      body.dark-mode .input-group-text {
        background-color: #1f2937;
        color: #e5e7eb;
        border-color: #475569;
      }

      body.dark-mode .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
      }

      body.dark-mode .dropdown-item {
        color: #e5e7eb;
      }

      body.dark-mode .dropdown-item:hover,
      body.dark-mode .dropdown-item:focus,
      body.dark-mode .list-group-item-action:hover,
      body.dark-mode .list-group-item-action:focus {
        background-color: #1f2937;
        color: #f8fafc;
      }

      body.dark-mode .alert-secondary {
        background-color: #1f2937;
        border-color: #334155;
        color: #e5e7eb;
      }

      body.dark-mode .badge.bg-light {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
      }

      body.dark-mode .btn-outline-primary,
      body.dark-mode .btn-outline-secondary,
      body.dark-mode .btn-outline-success,
      body.dark-mode .btn-outline-info,
      body.dark-mode .btn-outline-dark {
        color: #e5e7eb;
        border-color: #64748b;
      }

      body.dark-mode .btn-outline-primary:hover,
      body.dark-mode .btn-outline-secondary:hover,
      body.dark-mode .btn-outline-success:hover,
      body.dark-mode .btn-outline-info:hover,
      body.dark-mode .btn-outline-dark:hover {
        background-color: #1f2937;
        color: #f8fafc;
        border-color: #94a3b8;
      }

      .rotate-icon {
        transition: transform 0.3s ease;
      }

      .rotate-icon.rotate {
        transform: rotate(180deg);
      }
    </style>
  </head>

  <body data-layout-shell="faculty">

    <!-- Loader -->
    <div id="preloader">
      <img src="{{ asset('images/logo.jpg') }}" alt="Loading..." class="loader-logo">
    </div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar" data-shell-fragment="sidebar">
      <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
          
          <div>
            @if (Auth::check())
  <strong class="text-white">{{ Auth::user()->name }}</strong>
  <div class="text-white-50" style="font-size: 12px;">Subject Teacher</div>
@else
  {{-- Redirect if not logged in --}}
  <script>window.location = "{{ route('login.professor') }}";</script>
@endif

          </div>
        </div>
        <button class="btn btn-outline-light btn-sm" id="themeToggleBtn">
          <i id="themeIcon" class="bi bi-moon-fill"></i>
        </button>
      </div>

      <a href="{{ route('professor.dashboard') }}" class="{{ request()->routeIs('professor.dashboard') ? 'active' : '' }}">
  <i class="bi bi-speedometer2 me-2"></i> DASHBOARD
</a>

<hr class="border-light">
<small class="text-uppercase text-white-50 px-3">TEACHING</small>

<a class="d-flex align-items-center text-white" data-bs-toggle="collapse" href="#gradesMenu" role="button" aria-expanded="false" aria-controls="gradesMenu">
  <i class="bi bi-mortarboard me-2"></i>
  <span>GRADES</span>
  <i class="bi bi-chevron-down ms-auto rotate-icon"></i>
</a>

<div class="collapse {{ request()->routeIs('grades.*') ? 'show' : '' }}" id="gradesMenu">
  <div class="ps-3">
    <a href="{{ route('grades.consolidation') }}" class="{{ request()->routeIs('grades.consolidation') ? 'active' : '' }}">
      <i class="bi bi-pencil-square me-2"></i> ENCODE GRADE
    </a>
    {{-- <a href="{{ route('grades.consolidated') }}" class="{{ request()->routeIs('grades.consolidated') ? 'active' : '' }}">
      <i class="bi bi-journal-check me-2"></i> CONSOLIDATED GRADES
    </a> --}}
    @auth
@if(auth()->user()->advisory)
    <a href="{{ route('grades.consolidated') }}" class="{{ request()->routeIs('grades.consolidated') ? 'active' : '' }}">
        <i class="bi bi-arrow-up-square me-1"></i> CONSOLIDATED GRADES
    </a>
@endif
@endauth
  </div>
</div>

<hr class="border-light">
<small class="text-uppercase text-white-50 px-3">COMMUNICATION</small>
<a href="{{ route('professor.announcements') }}" class="{{ request()->routeIs('professor.announcements') ? 'active' : '' }}">
  <i class="bi bi-megaphone me-2"></i> ANNOUNCEMENTS
</a>
@auth
  @if(auth()->user()->advisory)
    <a href="{{ route('professor.attendance.index') }}" class="{{ request()->routeIs('professor.attendance*') ? 'active' : '' }}">
      <i class="bi bi-calendar-check me-2"></i> ATTENDANCE
    </a>
  @endif
@endauth

<hr class="border-light">
<small class="text-uppercase text-white-50 px-3">SETTINGS</small>
     <a href="{{ route('professor.change-password.form') }}" class="{{ request()->routeIs('professor.change-password.form') ? 'active' : '' }}">
       <i class="bi bi-key me-2"></i> CHANGE PASSWORD</a>


      <div class="logout-btn-wrapper">
        <button class="btn btn-warning w-100 text-dark fw-bold mt-4" data-bs-toggle="modal" data-bs-target="#logoutModal">
          <i class="bi bi-box-arrow-right me-1"></i> LOGOUT
        </button>
      </div>
    </div>

    <!-- Mobile Navbar -->
    <nav class="navbar bg-light d-md-none">
      <div class="container-fluid">
        <button class="btn btn-outline-success" onclick="toggleSidebar()">
          <i class="bi bi-list"></i>
        </button>
        <span class="navbar-brand mb-0 h1">PROFESSOR PANEL</span>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" data-shell-fragment="content">
      <h2>@yield('header')</h2>
      @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/persistent-layout.js') }}"></script>
    <script>
      function applyFacultyTheme(theme) {
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

      function initializeFacultyLayoutShell() {
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const gradesToggle = document.querySelector('[href="#gradesMenu"]');
        const gradesMenu = document.getElementById('gradesMenu');
        const chevronIcon = gradesToggle?.querySelector('.rotate-icon');

        applyFacultyTheme(localStorage.getItem('theme') === 'dark' ? 'dark' : 'light');

        if (themeToggleBtn && themeToggleBtn.dataset.bound !== 'true') {
          themeToggleBtn.dataset.bound = 'true';
          themeToggleBtn.addEventListener('click', () => {
            applyFacultyTheme(document.body.classList.contains('dark-mode') ? 'light' : 'dark');
          });
        }

        if (gradesMenu && chevronIcon && gradesMenu.dataset.bound !== 'true') {
          gradesMenu.dataset.bound = 'true';
          gradesMenu.addEventListener('show.bs.collapse', function () {
            chevronIcon.classList.add('rotate');
          });
          gradesMenu.addEventListener('hide.bs.collapse', function () {
            chevronIcon.classList.remove('rotate');
          });
        }

        if (gradesMenu?.classList.contains('show')) {
          chevronIcon?.classList.add('rotate');
        } else {
          chevronIcon?.classList.remove('rotate');
        }
      }

      function toggleSidebar() {
        document.getElementById('sidebar')?.classList.toggle('active');
      }

      // Hide Preloader
      window.addEventListener('load', function () {
        const preloader = document.getElementById('preloader');
        preloader.style.opacity = '0';
        preloader.style.visibility = 'hidden';
        preloader.style.transition = 'opacity 0.5s ease, visibility 0.5s ease';
        setTimeout(() => preloader.remove(), 600);
      });

      initializeFacultyLayoutShell();
      window.addEventListener('persistent-nav:render', initializeFacultyLayoutShell);
    </script>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="logoutModalLabel">CONFIRM LOGOUT</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            ARE YOU SURE YOU WANT TO LOG OUT OF YOUR PROFESSOR SESSION?
          </div>
          <div class="modal-footer">
            <form method="POST" action="{{ route('professor.logout') }}">
              @csrf
              <button type="submit" class="btn btn-warning text-dark fw-bold">YES, LOGOUT</button>
            </form>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
          </div>
        </div>
      </div>
    </div>

    <div hidden data-page-scripts>
      @yield('scripts')
      @stack('scripts')
    </div>

  </body>
  </html>
