<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Panel</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* ✅ Register local Aptos font */
    @font-face {
      font-family: 'Aptos';
      src: url('{{ asset('fonts/Aptos.ttf') }}') format('truetype');
      font-weight: normal;
      font-style: normal;
    }
    @font-face {
      font-family: 'Aptos';
      src: url('{{ asset('fonts/Aptos-Bold.ttf') }}') format('truetype');
      font-weight: bold;
      font-style: normal;
    }

    /* ✅ Global style */
    * {
      font-family: 'Aptos', sans-serif !important;
      text-transform: uppercase !important;
      letter-spacing: 0.5px;
    }

    body {
      margin: 0;
      padding: 0;
      background-color: #f8f9fa;
      transition: background-color 0.3s, color 0.3s;
    }

    /* ✅ Sidebar */
    .sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 260px;
  height: 100vh;
  background-color: #14532d;
  color: white;
  padding: 20px 15px;
  overflow-y: hidden; /* keep hidden */
  display: flex;
  flex-direction: column;
  z-index: 1000;
}

    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 10px 15px;
      border-radius: 6px;
      font-size: 14px;
      transition: background-color 0.3s, padding-left 0.3s;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: #facc15;
      color: #14532d;
      font-weight: bold;
      padding-left: 20px;
    }

    .main-content {
      margin-left: 260px; /* matches sidebar */
      padding: 20px;
      transition: background-color 0.3s, color 0.3s;
    }

    .logout-btn-wrapper {
  position: absolute;
  bottom: 20px; /* distance from bottom */
  left: 15px;
  right: 15px;
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

    /* ✅ Dark mode */
    body.dark-mode {
      background-color: #1e1e1e;
      color: #f1f1f1;
    }

    body.dark-mode .sidebar {
      background-color: #0f172a;
      color: white;
    }

    body.dark-mode .sidebar a {
      color: white;
    }

    body.dark-mode .sidebar a:hover,
    body.dark-mode .sidebar a.active {
      background-color: #facc15;
      color: #0f172a;
    }

    body.dark-mode .main-content {
      background-color: #1e1e1e;
      color: #f1f1f1;
    }

    body.dark-mode .navbar {
      background-color: #0f172a !important;
      color: white;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">

  <div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center">
      <img src="{{ Auth::user()->profile_photo_url ?? asset('images/def.jpg') }}" alt="Profile Photo"
           class="rounded-circle me-2" width="40" height="40">
      <div>
        <strong class="text-white">{{ Auth::user()->name }}</strong>
        <div class="text-white-50" style="font-size: 13px;">ADMIN</div>
      </div>
    </div>
    <button class="btn btn-outline-light btn-sm" id="themeToggleBtn">
      <i id="themeIcon" class="bi bi-moon-fill"></i>
    </button>
  </div>

  <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}">📊 DASHBOARD</a>

  <hr class="border-light">
  <small class="text-white-50 px-3">ADMIN</small>
  <a href="{{ route('admin.enrollments.index') }}" class="{{ request()->is('admin/pending-enrollments*') ? 'active' : '' }}">⏳ MANAGE REQUESTS</a>
  <a href="{{ route('admin.reportcard.index') }}" class="{{ request()->is('admin/reportcard/index*') ? 'active' : '' }}">📄 FORM REQUESTS</a>
  <a href="{{ route('admin.professors.index') }}" class="{{ request()->is('admin/professors*') ? 'active' : '' }}">👨‍🏫 MANAGE PROFESSORS</a>
  <a href="{{ route('admin.students.index') }}" class="{{ request()->is('admin/students*') ? 'active' : '' }}">👩‍🎓 MANAGE STUDENTS</a>
  <a href="{{ route('admin.announcements.index') }}" class="{{ request()->is('admin/announcements*') ? 'active' : '' }}">📢 ANNOUNCEMENTS</a>
  <a href="{{ route('attendance.index') }}" class="{{ request()->is('admin/attendance*') ? 'active' : '' }}">✅ STUDENT ATTENDANCE</a>
  <hr class="border-light">
  <small class="text-white-50 px-3">GRADES</small>
  <a href="{{ route('grades.summary') }}" class="{{ request()->routeIs('grades.summary') ? 'active' : '' }}">📋 GRADE SUMMARY</a>
  <a href="{{ route('classrecord.index') }}" class="{{ request()->is('admin/ecr*') ? 'active' : '' }}">📄 REPORT CARDS</a>
  {{-- <hr class="border-light"> --}}
  {{-- <small class="text-white-50 px-3">ATTENDANCE</small> --}}

  <hr class="border-light">
  <small class="text-white-50 px-3">SETTINGS</small>
  <a href="{{ route('admin.change-password.form') }}">🔑 CHANGE PASSWORD</a>

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
    <span class="navbar-brand mb-0 h1">ADMIN PANEL</span>
  </div>
</nav>

<!-- Main Content -->
<div class="main-content">
  <h2>@yield('header')</h2>
  @yield('content')
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Dark Mode & Sidebar Toggle -->
<script>
  function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
  }

  const themeToggleBtn = document.getElementById('themeToggleBtn');
  const themeIcon = document.getElementById('themeIcon');
  const currentTheme = localStorage.getItem('theme');

  function applyTheme(theme) {
    if (theme === 'dark') {
      document.body.classList.add('dark-mode');
      themeIcon.classList.replace('bi-sun-fill', 'bi-moon-fill');
    } else {
      document.body.classList.remove('dark-mode');
      themeIcon.classList.replace('bi-moon-fill', 'bi-sun-fill');
    }
    localStorage.setItem('theme', theme);
  }

  applyTheme(currentTheme === 'dark' ? 'dark' : 'light');

  themeToggleBtn.addEventListener('click', () => {
    const isDark = document.body.classList.contains('dark-mode');
    applyTheme(isDark ? 'light' : 'dark');
  });
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
        ARE YOU SURE YOU WANT TO LOG OUT OF YOUR ADMIN SESSION?
      </div>
      <div class="modal-footer">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn btn-warning text-dark fw-bold">YES, LOGOUT</button>
        </form>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
      </div>
    </div>
  </div>
</div>

</body>
</html>
