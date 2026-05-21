<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Panel</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"> --}}



  <style>
    /* --- Fonts --- */
    @font-face {
      font-family: 'Aptos';
      src: url('{{ asset('fonts/Aptos.ttf') }}') format('truetype');
      font-weight: normal;
    }
    @font-face {
      font-family: 'Aptos';
      src: url('{{ asset('fonts/Aptos-Bold.ttf') }}') format('truetype');
      font-weight: bold;
    }

    /* --- Global Styles --- */
    * {
      font-family: 'Aptos', sans-serif !important;
      text-transform: uppercase !important;
      letter-spacing: 0.5px;
    }
    body {
      margin: 0;
      padding: 0;
      background-color: #f8f9fa;
      color: #0f172a;
      transition: background-color 0.3s, color 0.3s;
    }

    /* --- Sidebar --- */
    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 260px;
      height: 100vh;
      background-color: #14532d;
      color: white;
      padding: 20px 15px;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
      z-index: 1000;
      transition: height 0.3s;
    }
    .sidebar.no-scroll { overflow-y: hidden; }
    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 10px 15px;
      border-radius: 6px;
      font-size: 14px;
      transition: background-color 0.3s, padding-left 0.3s;
      position: relative;
    }
    .sidebar a:hover, .sidebar a.active {
      background-color: #facc15;
      color: #14532d;
      font-weight: bold;
      padding-left: 20px;
    }

    /* --- Main Content --- */
    .main-content {
      margin-left: 260px;
      padding: 20px;
    }

    /* --- Logout Button --- */
    .logout-btn-wrapper { margin-top: auto; }

    /* --- Responsive --- */
    @media (max-width: 768px) {
      .sidebar { transform: translateX(-100%); transition: transform 0.3s ease; }
      .sidebar.active { transform: translateX(0); }
      .main-content { margin-left: 0; }
    }

    /* --- Dark Mode --- */
    body.dark-mode {
      background-color: #0f172a;
      color: #e5e7eb;
    }
    body.dark-mode .sidebar { background-color: #111827; }
    body.dark-mode .sidebar a { color: #e5e7eb; }
    body.dark-mode .sidebar a:hover, body.dark-mode .sidebar a.active { color: #0f172a; background-color: #facc15; }
    body.dark-mode .main-content,
    body.dark-mode .container,
    body.dark-mode .container-fluid {
      background-color: transparent;
      color: inherit;
    }
    body.dark-mode .navbar {
      background-color: #111827 !important;
      color: #e5e7eb;
      border-bottom: 1px solid #334155;
    }
    body.dark-mode .card,
    body.dark-mode .modal-content,
    body.dark-mode .offcanvas,
    body.dark-mode .dropdown-menu,
    body.dark-mode .list-group-item,
    body.dark-mode .kebab-menu {
      background-color: #111827;
      color: #e5e7eb;
      border-color: #334155;
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25);
    }
    body.dark-mode .card-header,
    body.dark-mode .card-footer,
    body.dark-mode .modal-header,
    body.dark-mode .modal-footer,
    body.dark-mode .offcanvas-header {
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
    body.dark-mode .dropdown-item:focus {
      background-color: #1f2937;
      color: #f8fafc;
    }
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
<body data-layout-shell="admin">

<!-- Sidebar -->
<div class="sidebar" id="sidebar" data-shell-fragment="sidebar">

  <!-- Profile & Theme -->
  <div class="d-flex align-items-center justify-content-between mb-4">
    <div>
      @if(Auth::check())
        <strong class="text-white">{{ Auth::user()->name }}</strong>
        <div class="text-white-50" style="font-size: 12px;">Principal, CvSU Naic LSHS</div>
      @else
        <script>window.location = "{{ route('login.admin') }}";</script>
      @endif
    </div>
    <button class="btn btn-outline-light btn-sm" id="themeToggleBtn">
      <i id="themeIcon" class="bi bi-moon-fill"></i>
    </button>
  </div>

  <!-- --- Menu Sections --- -->

  <!-- Dashboard -->
  <small class="text-white-50 px-3">HOME</small>
  <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2 me-2"></i> Dashboard
  </a>
  <hr class="border-light">

  <!-- Requests Section -->
  <small class="text-white-50 px-3">REQUESTS & FORMS</small>
  <a class="nav-link dropdown-toggle text-white" data-bs-toggle="collapse" href="#requestsMenu" role="button" aria-expanded="false">
    <i class="bi bi-folder2-open me-2"></i> Requests
  </a>
  <div class="collapse" id="requestsMenu">
    <a href="{{ route('admin.enrollments.index') }}" class="nav-link ms-3 {{ request()->is('admin/pending-enrollments*') ? 'active' : '' }}">
      <i class="bi bi-person-lines-fill me-2"></i> Enrollments
    </a>
    <a href="{{ route('admin.reportcard.index') }}" class="nav-link ms-3 {{ request()->routeIs('admin.reportcard.index', 'admin.reportcard.archive') ? 'active' : '' }}">
      <i class="bi bi-file-earmark-text me-2"></i> Forms
    </a>
  </div>

  <!-- Management Section -->
  <small class="text-white-50 px-3">MANAGEMENT</small>
  <a class="nav-link dropdown-toggle text-white" data-bs-toggle="collapse" href="#manageMenu" role="button" aria-expanded="false">
    <i class="bi bi-gear me-2"></i> Manage
  </a>
  <div class="collapse" id="manageMenu">
    <a href="{{ route('admin.professors.index') }}" class="nav-link ms-3 {{ request()->is('admin/professors*') ? 'active' : '' }}">
      <i class="bi bi-person-badge me-2"></i> Professors
    </a>
    <a href="{{ route('admin.students.index') }}" class="nav-link ms-3 {{ request()->is('admin/students*') ? 'active' : '' }}">
      <i class="bi bi-people me-2"></i> Students
  </a>
  </div>
    
  <!-- Grades Section -->
  <small class="text-white-50 px-3">GRADES</small>
  <a class="nav-link dropdown-toggle text-white" data-bs-toggle="collapse" href="#gradesMenu" role="button" aria-expanded="false">
    <i class="bi bi-journal-text me-2"></i> Grades
  </a>
  <div class="collapse" id="gradesMenu">
    <a href="{{ route('grades.summary') }}" class="nav-link ms-3 {{ request()->routeIs('grades.summary') ? 'active' : '' }}">
      <i class="bi bi-journal-check me-2"></i> Summary
    </a>
    <a href="{{ route('classrecord.index') }}" class="nav-link ms-3 {{ request()->is('admin/ecr*') ? 'active' : '' }}">
      <i class="bi bi-file-earmark-text me-2"></i> Report Cards
    </a>
    <a href="{{ route('admin.grade-approvals.index') }}" class="nav-link ms-3 {{ request()->is('admin/grade-approvals*') ? 'active' : '' }}">
      <i class="bi bi-check2-square me-2"></i> Grade Approvals
    </a>
  </div>

  <hr class="border-light">

  <!-- Misc / Standalone Links -->
  <small class="text-white-50 px-3">OTHERS</small>
  <a href="{{ route('admin.announcements.index') }}" class="{{ request()->is('admin/announcements*') ? 'active' : '' }}">
    <i class="bi bi-megaphone me-2"></i> Announcements
  </a>
  <a href="{{ route('admin.audit-trails.index') }}" class="{{ request()->is('admin/audit-trails*') ? 'active' : '' }}">
    <i class="bi bi-journal-text me-2"></i> Audit Trails
  </a>

  <hr class="border-light">

  <!-- User Section -->
  <small class="text-white-50 px-3">ALL ACCESS</small>
  {{-- <a href="{{ route('admin.change-password.form') }}">
    <i class="bi bi-key me-2"></i> Change Password
  </a> --}}
  <a href="{{ route('admin.settings.index') }}" class="{{ request()->is('admin/settings*') ? 'active' : '' }}">
    <i class="bi bi-sliders me-2"></i> Settings 
  </a>

  <!-- Logout -->
  <div class="logout-btn-wrapper mt-auto">
    <button class="btn btn-warning w-100 text-dark fw-bold mt-4" data-bs-toggle="modal" data-bs-target="#logoutModal">
      <i class="bi bi-box-arrow-right me-1"></i> Logout
    </button>
  </div>
</div>

<!-- Mobile Navbar -->
<nav class="navbar bg-light d-md-none">
  <div class="container-fluid">
    <button class="btn btn-outline-success" onclick="toggleSidebar()">
      <i class="bi bi-list"></i>
    </button>
  </div>
</nav>

<!-- Main Content -->
<div class="main-content" data-shell-fragment="content">
  <h2>@yield('header')</h2>
  @yield('content')
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/persistent-layout.js') }}"></script>

<!-- Sidebar Toggle & Dark Mode -->
<script>
  function applyAdminTheme(theme) {
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

  function initializeAdminLayoutShell() {
    const sidebar = document.getElementById('sidebar');
    const themeToggleBtn = document.getElementById('themeToggleBtn');

    applyAdminTheme(localStorage.getItem('theme') === 'dark' ? 'dark' : 'light');

    if (themeToggleBtn && themeToggleBtn.dataset.bound !== 'true') {
      themeToggleBtn.dataset.bound = 'true';
      themeToggleBtn.addEventListener('click', () => {
        applyAdminTheme(document.body.classList.contains('dark-mode') ? 'light' : 'dark');
      });
    }

    document.querySelectorAll('.sidebar .collapse').forEach((collapseEl) => {
      if (collapseEl.dataset.bound === 'true') {
        return;
      }

      collapseEl.dataset.bound = 'true';
      collapseEl.addEventListener('show.bs.collapse', () => {
        sidebar?.classList.add('no-scroll');
      });
      collapseEl.addEventListener('hide.bs.collapse', () => {
        sidebar?.classList.remove('no-scroll');
      });
    });
  }

  function toggleSidebar() {
    document.getElementById('sidebar')?.classList.toggle('active');
  }

  initializeAdminLayoutShell();
  window.addEventListener('persistent-nav:render', initializeAdminLayoutShell);
</script>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to log out?
      </div>
      <div class="modal-footer">
        <form method="POST" action="{{ route('admin.logout') }}">
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
  @stack('scripts')
</div>
</body>
</html>

