<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
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

    .rotate-icon {
      transition: transform 0.3s ease;
    }

    .rotate-icon.rotate {
      transform: rotate(180deg);
    }
  </style>
</head>

<body>

  <!-- Loader -->
  <div id="preloader">
    <img src="{{ asset('images/logo.jpg') }}" alt="Loading..." class="loader-logo">
  </div>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div class="d-flex align-items-center">
        <img src="{{ Auth::user()->profile_photo_url ?? asset('images/def.jpg') }}" alt="Profile Photo"
             class="rounded-circle me-2" width="40" height="40">
        <div>
          <strong class="text-white">{{ Auth::user()->name }}</strong>
          <div class="text-white-50" style="font-size: 12px;">PROFESSOR</div>
        </div>
      </div>
      <button class="btn btn-outline-light btn-sm" id="themeToggleBtn">
        <i id="themeIcon" class="bi bi-moon-fill"></i>
      </button>
    </div>

    <a href="{{ route('professor.dashboard') }}" class="{{ request()->routeIs('professor.dashboard') ? 'active' : '' }}">📊 DASHBOARD</a>

    <hr class="border-light">
    <small class="text-uppercase text-white-50 px-3">TEACHING</small>

    <a class="d-flex justify-content-between align-items-center text-white" data-bs-toggle="collapse" href="#gradesMenu" role="button" aria-expanded="false" aria-controls="gradesMenu">
      🎓 GRADES
      <i class="bi bi-chevron-down rotate-icon"></i>
    </a>
    <div class="collapse {{ request()->routeIs('grades.*') ? 'show' : '' }}" id="gradesMenu">
      <div class="ps-3">
        <a href="{{ route('grades.consolidation') }}" class="{{ request()->routeIs('grades.consolidation') ? 'active' : '' }}">📝 ENCODE GRADE</a>
        <a href="{{ route('grades.consolidated') }}" class="{{ request()->routeIs('grades.consolidated') ? 'active' : '' }}">📊 CONSOLIDATED GRADES</a>
      </div>
    </div>

    <hr class="border-light">
    <small class="text-uppercase text-white-50 px-3">COMMUNICATION</small>
    <a href="{{ route('professor.announcements') }}" class="{{ request()->routeIs('professor.announcements') ? 'active' : '' }}">📢 ANNOUNCEMENTS</a>

    <hr class="border-light">
    <small class="text-uppercase text-white-50 px-3">SETTINGS</small>
    <a href="{{ route('professor.change-password.form') }}">🔑 CHANGE PASSWORD</a>

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
  <div class="main-content">
    <h2>@yield('header')</h2>
    @yield('content')
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('active');
    }

    // Dark mode
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

    // Collapse icon rotation
    const gradesToggle = document.querySelector('[href="#gradesMenu"]');
    const chevronIcon = gradesToggle.querySelector('.rotate-icon');
    const gradesMenu = document.getElementById('gradesMenu');

    gradesMenu.addEventListener('show.bs.collapse', function () {
      chevronIcon.classList.add('rotate');
    });
    gradesMenu.addEventListener('hide.bs.collapse', function () {
      chevronIcon.classList.remove('rotate');
    });

    if (gradesMenu.classList.contains('show')) {
      chevronIcon.classList.add('rotate');
    }

    // Hide Preloader
    window.addEventListener('load', function () {
      const preloader = document.getElementById('preloader');
      preloader.style.opacity = '0';
      preloader.style.visibility = 'hidden';
      preloader.style.transition = 'opacity 0.5s ease, visibility 0.5s ease';
      setTimeout(() => preloader.remove(), 600);
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
          ARE YOU SURE YOU WANT TO LOG OUT OF YOUR PROFESSOR SESSION?
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

  @yield('scripts')
</body>
</html>
