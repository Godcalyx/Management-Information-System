<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'Student Portal')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

  <!-- 🔤 Load Aptos Font -->
  <style>
    @font-face {
      font-family: 'Aptos';
      src: url('{{ asset('fonts/Aptos.ttf') }}') format('truetype'),
           url('{{ asset('fonts/Aptos-Bold.ttf') }}') format('truetype');
      font-weight: normal;
      font-style: normal;
    }

    body {
      font-family: 'Aptos', sans-serif;
      text-transform: uppercase;
      margin: 0;
      padding: 0;
      background-color: #f8f9fa;
      color: #0f172a;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Sidebar */
    .sidebar {
      background-color: #14532d;
      color: white;
      padding: 20px 15px;
      min-height: 100vh;
      width: 260px;
    }

    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 12px 15px;
      border-radius: 8px;
      font-size: 15px;
      letter-spacing: 0.5px;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: #facc15;
      color: #14532d;
      font-weight: bold;
    }

    .sidebar h4 {
      font-weight: bold;
      letter-spacing: 1px;
    }

    .logout-btn-wrapper {
      margin-top: auto;
    }

    /* Fixed Sidebar for Desktop */
    @media (min-width: 768px) {
      .sidebar-fixed {
        position: fixed;
        top: 0;
        left: 0;
        width: 260px;
        height: 100%;
        overflow-y: auto;
      }
      .main-content {
        margin-left: 260px;
      }
    }

    /* Offcanvas Sidebar (Mobile) */
    .offcanvas-body.sidebar {
      flex-grow: 1;
      min-height: 80vh;
      display: flex;
      flex-direction: column;
    }

    /* Navbar */
    nav.navbar {
      font-family: 'Aptos', sans-serif;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .navbar .nav-link {
      font-weight: 600;
    }

    /* Logout Button */
    .btn-warning {
      background-color: #facc15 !important;
      border: none;
      font-weight: bold;
    }

    .btn-warning:hover {
      background-color: #f3c613 !important;
    }

    /* Headings */
    h2, h4, small {
      letter-spacing: 1px;
    }

    body.dark-mode {
      background-color: #0f172a;
      color: #e5e7eb;
    }

    body.dark-mode .sidebar,
    body.dark-mode .offcanvas-body.sidebar,
    body.dark-mode .navbar {
      background-color: #111827 !important;
      color: #e5e7eb;
      border-color: #334155;
    }

    body.dark-mode .sidebar a,
    body.dark-mode .navbar .nav-link {
      color: #e5e7eb;
    }

    body.dark-mode .sidebar a:hover,
    body.dark-mode .sidebar a.active {
      background-color: #facc15;
      color: #0f172a;
    }

    body.dark-mode .main-content,
    body.dark-mode .page-content,
    body.dark-mode .container,
    body.dark-mode .container-fluid {
      background-color: transparent;
      color: inherit;
    }

    body.dark-mode .card,
    body.dark-mode .modal-content,
    body.dark-mode .offcanvas,
    body.dark-mode .dropdown-menu,
    body.dark-mode .list-group-item,
    body.dark-mode .accordion-item {
      background-color: #111827;
      color: #e5e7eb;
      border-color: #334155;
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25);
    }

    body.dark-mode .card-header,
    body.dark-mode .card-footer,
    body.dark-mode .modal-header,
    body.dark-mode .modal-footer,
    body.dark-mode .accordion-button {
      border-color: #334155;
    }

    body.dark-mode .accordion-button:not(.collapsed) {
      background-color: #1f2937;
      color: #f8fafc;
      box-shadow: inset 0 calc(-1 * var(--bs-accordion-border-width)) 0 #334155;
    }

    body.dark-mode .accordion-button,
    body.dark-mode .accordion-body {
      background-color: #111827;
      color: #e5e7eb;
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

  </style>
</head>
<body data-layout-shell="student">
  @php
    $studentUser = Auth::user();
    $latestEnrollment = null;
    $gradeLevel = 'N/A';
    $unreadCount = 0;

    if ($studentUser) {
        $latestEnrollment = \App\Models\Enrollment::with('gradeLevel')
            ->where('user_id', $studentUser->id)
            ->latest('id')
            ->first();

        $gradeLevel = optional($latestEnrollment?->gradeLevel)->name ?? 'N/A';
        $gradeNumber = $studentUser->grade_level;
        $gradeText = "Grade {$gradeNumber}";

        $unreadCount = \App\Models\Announcement::where(function ($query) use ($gradeNumber, $gradeText) {
                $query->whereJsonContains('target_grades', $gradeText)
                    ->orWhereJsonContains('target_grades', $gradeNumber)
                    ->orWhereJsonContains('target_grades', 'All')
                    ->orWhereNull('target_grades');
            })
            ->whereDoesntHave('users', function ($query) use ($studentUser) {
                $query->where('user_id', $studentUser->id)
                    ->where('announcement_user.is_read', true);
            })
            ->count();
    }
  @endphp

  <!-- Desktop Sidebar -->
<div class="sidebar sidebar-fixed d-none d-md-flex flex-column" data-shell-fragment="desktop-sidebar">
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="text-center mb-0">Student Portal</h4>
    <button class="btn btn-outline-light btn-sm" id="themeToggleBtn" type="button" aria-label="Toggle theme">
      <i id="themeIcon" class="bi bi-moon-fill"></i>
    </button>
  </div>

  <a href="{{ route('student.dashboard') }}" class="{{ request()->is('student/dashboard') ? 'active' : '' }}">
    <i class="bi bi-house-door me-2"></i> Dashboard
  </a>

  <hr class="border-light" />
  <small class="text-white-50 px-3">Academics</small>
  <a href="{{ route('student.grades') }}" class="{{ request()->is('student/grades') ? 'active' : '' }}">
    <i class="bi bi-journal-bookmark me-2"></i> Grades
  </a>

  <hr class="border-light" />
  <small class="text-white-50 px-3">Communication</small>
    <a href="{{ route('announcements.index') }}">
        <i class="bi bi-megaphone me-2"></i> ANNOUNCEMENTS
        @if($unreadCount > 0)
            <span class="badge bg-danger">{{ $unreadCount }}</span>
        @endif
    </a>
<hr class="border-light" />
    <small class="text-white-50 px-3">Reports</small>
    <a href="{{ route('student.reportforms') }}"class="{{ request()->routeIs('student.reportforms') ? 'active' : '' }}">
                    <i class="bi bi-download me-1"></i> Request Forms
                </a>

  <hr class="border-light" />
  <small class="text-white-50 px-3">User Management</small>
  <a href="{{ route('student.settings') }}" class="{{ request()->is('student/settings') ? 'active' : '' }}">
    <i class="bi bi-gear me-2"></i> Settings
  </a>

  <div class="logout-btn-wrapper mt-auto">
    <button class="btn btn-warning w-100 text-dark fw-bold mt-4" data-bs-toggle="modal" data-bs-target="#logoutModal">
      <i class="bi bi-box-arrow-right me-1"></i> Logout
    </button>
  </div>
</div>

<!-- Mobile Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel" style="width: 260px;" data-shell-fragment="mobile-sidebar">
  <div class="offcanvas-body sidebar d-flex flex-column">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <strong class="text-white">Student Portal</strong>
      <button class="btn btn-outline-light btn-sm" id="mobileThemeToggleBtn" type="button" aria-label="Toggle theme">
        <i class="bi bi-moon-fill"></i>
      </button>
    </div>
    <a href="{{ route('student.dashboard') }}" class="{{ request()->is('student/dashboard') ? 'active' : '' }}">
      <i class="bi bi-house-door me-2"></i> Dashboard
    </a>
    <hr class="border-light" />
    <small class="text-white-50 px-3">Academics</small>
    <a href="{{ route('student.grades') }}" class="{{ request()->is('student/grades') ? 'active' : '' }}">
      <i class="bi bi-journal-bookmark me-2"></i>Grades
    </a>
    <hr class="border-light" />
    <small class="text-white-50 px-3">Communication</small>
    <a href="{{ route('announcements.index') }}">
        <i class="bi bi-megaphone me-2"></i> ANNOUNCEMENTS
        @if($unreadCount > 0)
            <span class="badge bg-danger">{{ $unreadCount }}</span>
        @endif
    </a>

<hr class="border-light" />
    <small class="text-white-50 px-3">Reports</small>
    <a href="{{ route('student.reportforms') }}" class="{{ request()->routeIs('student.reportforms') ? 'active' : '' }}">
                    <i class="bi bi-download me-1"></i> Request Forms
                </a>
    <hr class="border-light" />
    <small class="text-white-50 px-3">Settings</small>
    <a href="{{ route('student.settings') }}" class="{{ request()->is('student/settings') ? 'active' : '' }}">
      <i class="bi bi-gear me-2"></i> Settings
    </a>

    <div class="logout-btn-wrapper mt-auto">
      <button class="btn btn-warning w-100 text-dark fw-bold mt-4" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <i class="bi bi-box-arrow-right me-1"></i> Logout
      </button>
    </div>
  </div>
</div>

  <!-- Main Content -->
  <div class="main-content" data-shell-fragment="content">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark px-4 sticky-top" style="background-color: #14532d;">
      <div class="container-fluid px-0 d-flex align-items-center position-relative w-100">

        <!-- Mobile Sidebar Toggle -->
        <button class="btn text-white d-md-none p-0 me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-label="Open sidebar" style="background: none; border: none;">
          <i class="bi bi-list fs-3"></i>
        </button>

        <!-- Mobile Centered Title -->
        <div class="mx-auto d-md-none text-center">
          <img src="{{ asset('images/logo.jpg') }}" alt="Logo" style="height: 65px; width: 65px; object-fit: contain; vertical-align: middle; margin-right: 0px;">
          <span class="text-white fw-bold align-middle">STUDENT PORTAL</span>
        </div>

        <!-- Mobile Collapse Toggle -->
    <button class="btn text-white d-md-none p-0 ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbarContent" aria-controls="topNavbarContent" aria-expanded="false" aria-label="Toggle user menu" style="background: none; border: none;">
      <i class="bi bi-list fs-3"></i>
    </button>

        <!-- User Info -->
<div class="collapse navbar-collapse justify-content-end d-md-flex" id="topNavbarContent">
    <ul class="navbar-nav mb-2 mb-lg-0 ms-auto">
        <li class="nav-item">
            <div>
                @if (Auth::check())
                    <strong class="text-white">Hello! {{ Auth::user()->name }}</strong>
                    <div class="text-white-50" style="font-size: 12px;">
                        Grade Level {{ $gradeLevel }} Student
                    </div>
                @else
                    <script>window.location = "{{ route('login.student') }}";</script>
                @endif
            </div>
        </li>
    </ul>
</div>


      </div>
    </nav>

    <!-- Page Body -->
    <div class="page-content p-4">
      <h2>@yield('header')</h2>
      @yield('content')
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/persistent-layout.js') }}"></script>

  <!-- Auto-hide Toasts -->
  <script>
    function applyStudentTheme(theme) {
      const themeIcon = document.getElementById('themeIcon');
      const mobileThemeIcon = document.querySelector('#mobileThemeToggleBtn i');

      if (theme === 'dark') {
        document.body.classList.add('dark-mode');
        themeIcon?.classList.replace('bi-moon-fill', 'bi-sun-fill');
        mobileThemeIcon?.classList.replace('bi-moon-fill', 'bi-sun-fill');
      } else {
        document.body.classList.remove('dark-mode');
        themeIcon?.classList.replace('bi-sun-fill', 'bi-moon-fill');
        mobileThemeIcon?.classList.replace('bi-sun-fill', 'bi-moon-fill');
      }

      localStorage.setItem('theme', theme);
    }

    function initializeStudentLayoutShell() {
      const themeToggleBtn = document.getElementById('themeToggleBtn');
      const mobileThemeToggleBtn = document.getElementById('mobileThemeToggleBtn');

      applyStudentTheme(localStorage.getItem('theme') === 'dark' ? 'dark' : 'light');

      if (themeToggleBtn && themeToggleBtn.dataset.bound !== 'true') {
        themeToggleBtn.dataset.bound = 'true';
        themeToggleBtn.addEventListener('click', () => {
          applyStudentTheme(document.body.classList.contains('dark-mode') ? 'light' : 'dark');
        });
      }

      if (mobileThemeToggleBtn && mobileThemeToggleBtn.dataset.bound !== 'true') {
        mobileThemeToggleBtn.dataset.bound = 'true';
        mobileThemeToggleBtn.addEventListener('click', () => {
          applyStudentTheme(document.body.classList.contains('dark-mode') ? 'light' : 'dark');
        });
      }
    }

    setTimeout(() => {
      const toastElList = document.querySelectorAll('.toast');
      toastElList.forEach(toastEl => {
        const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
        toast.hide();
      });
    }, 5000);

    initializeStudentLayoutShell();
    window.addEventListener('persistent-nav:render', initializeStudentLayoutShell);
  </script>

  <!-- Logout Modal -->
  <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to log out of your student account?
        </div>
        <div class="modal-footer">
          <form method="POST" action="{{ route('student.logout') }}">
            @csrf
            <button type="submit" class="btn btn-warning text-dark fw-bold">Yes, Logout</button>
          </form>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <div hidden data-page-scripts>
    @yield('scripts')
  </div>
</body>
</html>
