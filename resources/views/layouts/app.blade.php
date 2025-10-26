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

  </style>
</head>
<body>

  <!-- Desktop Sidebar -->
  <div class="sidebar sidebar-fixed d-none d-md-flex flex-column">
    <h4 class="text-center mb-4">Student Portal</h4>

    <a href="{{ route('student.dashboard') }}" class="{{ request()->is('student/dashboard') ? 'active' : '' }}">🏠 Dashboard</a>
    <hr class="border-light" />
    <small class="text-white-50 px-3">Academics</small>
    <a href="{{ route('student.grades') }}" class="{{ request()->is('student/grades') ? 'active' : '' }}">📑 Grades</a>
    <hr class="border-light" />
    <small class="text-white-50 px-3">Communication</small>
    <a href="{{ route('announcements.index') }}" class="{{ request()->is('student/announcements') ? 'active' : '' }}">📢 Announcements</a>
    <hr class="border-light" />
    <small class="text-white-50 px-3">User Management</small>
    <a href="{{ route('student.settings') }}" class="{{ request()->is('student/settings') ? 'active' : '' }}">⚙️ Settings</a>

    <div class="logout-btn-wrapper mt-auto">
      <button class="btn btn-warning w-100 text-dark fw-bold mt-4" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <i class="bi bi-box-arrow-right me-1"></i> Logout
      </button>
    </div>
  </div>

  <!-- Mobile Offcanvas Sidebar -->
  <div class="offcanvas offcanvas-start d-md-none" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel" style="width: 260px;">
    <div class="offcanvas-body sidebar d-flex flex-column">
      <a href="{{ route('student.dashboard') }}" class="{{ request()->is('student/dashboard') ? 'active' : '' }}">🏠 Dashboard</a>
      <hr class="border-light" />
      <small class="text-white-50 px-3">Academics</small>
      <a href="{{ route('student.grades') }}" class="{{ request()->is('student/grades') ? 'active' : '' }}">📑 My Grades</a>
      <hr class="border-light" />
      <small class="text-white-50 px-3">Communication</small>
      <a href="{{ route('announcements.index') }}" class="{{ request()->is('student/announcements') ? 'active' : '' }}">📢 Announcements</a>
      <hr class="border-light" />
      <small class="text-white-50 px-3">Settings</small>
      <a href="{{ route('student.settings') }}" class="{{ request()->is('student/settings') ? 'active' : '' }}">⚙️ User Management</a>

      <div class="logout-btn-wrapper mt-auto">
        <button class="btn btn-warning w-100 text-dark fw-bold mt-4" data-bs-toggle="modal" data-bs-target="#logoutModal">
          <i class="bi bi-box-arrow-right me-1"></i> Logout
        </button>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <div class="main-content">

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
              <span class="nav-link active text-white">
                👋 Hello, {{ Auth::user()->name ?? 'Student' }}
              </span>
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

  <!-- Auto-hide Toasts -->
  <script>
    setTimeout(() => {
      const toastElList = document.querySelectorAll('.toast');
      toastElList.forEach(toastEl => {
        const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
        toast.hide();
      });
    }, 5000);
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
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-warning text-dark fw-bold">Yes, Logout</button>
          </form>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  @yield('scripts')
</body>
</html>
