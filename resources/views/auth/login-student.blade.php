<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Login - CvSU Portal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Fonts and Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <style>
    body {
      margin: 0;
      height: 100vh;
      display: grid;
      place-items: center;
      background: radial-gradient(circle at top left, #0e2f0c 0%, #001a00 100%);
      color: #f2f2f2;
      font-family: "Poppins", sans-serif;
    }

    .card {
      display: flex;
      flex-direction: row;
      background: rgba(20, 40, 20, 0.92);
      border-radius: 20px;
      width: clamp(300px, 90vw, 740px);
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
      overflow: hidden;
      backdrop-filter: blur(10px);
    }

    .hero {
      flex: 1;
      background: url("{{ asset('images/hero2.jpg') }}") center/cover no-repeat;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .hero::after {
      content: "";
      position: absolute;
      inset: 0;
      background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, rgba(0,20,0,0.8) 100%);
    }

    .hero-inner {
      position: relative;
      color: #fff;
      padding: 40px;
      text-align: left;
      z-index: 2;
    }

    .hero-inner h2 {
      font-size: 26px;
      font-weight: 600;
      color: #FFD700;
    }

    .hero-inner h3 {
      font-size: 17px;
      margin-top: 10px;
      color: #d6d6d6;
    }

    .form-area {
      flex: 1;
      padding: 45px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      background: rgba(255, 255, 255, 0.03);
    }

    h2 {
      color: #FFD700;
      font-size: 22px;
      margin-bottom: 5px;
    }

    h3 {
      font-size: 15px;
      color: #aaa;
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .form-control {
      height: 44px;
      border-radius: 6px;
      border: 1px solid #3b3b3b;
      font-size: 16px;
      padding: 0 15px;
      background: rgba(255, 255, 255, 0.08);
      color: #f9f9f9;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #FFD700;
      box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.3);
      outline: none;
      background: rgba(255, 255, 255, 0.12);
    }

    .btn-primary {
      background: linear-gradient(90deg, #FFD700, #c89b00);
      border: none;
      color: #102d0e;
      font-size: 17px;
      font-weight: 600;
      height: 44px;
      border-radius: 6px;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
    }

    .enroll-link {
      text-align: center;
      margin-top: 10px;
      font-weight: 500;
    }

    .enroll-link a {
      color: #FFD700;
      text-decoration: none;
      transition: color 0.2s ease;
    }

    .enroll-link a:hover {
      color: #fff;
      text-decoration: underline;
    }

    .invalid-feedback {
      color: #ff4c4c;
      font-size: 13px;
    }

    /* Floating particles for subtle motion */
    .particles {
      position: fixed;
      inset: 0;
      overflow: hidden;
      z-index: -1;
    }

    .particle {
      position: absolute;
      width: 3px;
      height: 3px;
      background: rgba(255, 255, 255, 0.6);
      border-radius: 50%;
      animation: float 6s infinite ease-in-out;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); opacity: 0.4; }
      50% { transform: translateY(-20px); opacity: 1; }
    }

    @media (max-width: 768px) {
      .card {
        flex-direction: column;
      }
      .hero {
        height: 200px;
      }
    }
  </style>
</head>
<body>

<div class="particles">
  <div class="particle" style="top:10%; left:20%; animation-delay:0s;"></div>
  <div class="particle" style="top:40%; left:70%; animation-delay:1.5s;"></div>
  <div class="particle" style="top:70%; left:30%; animation-delay:3s;"></div>
  <div class="particle" style="top:85%; left:80%; animation-delay:2s;"></div>
</div>

<div class="card">
  <!-- Left: Hero Section -->
  <div class="hero">
    <div class="hero-inner">
      <h2>Welcome to CvSU</h2>
      <h3>Your academic journey starts here.</h3>
    </div>
  </div>

  <!-- Right: Login Form -->
  <div class="form-area">
    <form method="POST" action="{{ route('login.student.submit') }}" novalidate>
      @csrf
      <h2>Student Login</h2>
      <h3>Sign in with your LRN and password</h3>

      <input type="text" name="lrn" id="lrn"
        class="form-control @error('lrn') is-invalid @enderror"
        placeholder="12-digit LRN"
        value="{{ old('lrn') }}"
        required minlength="12" maxlength="12" pattern="\d{12}" autofocus />
      @error('lrn')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror

      <input type="password" name="password" id="password"
        class="form-control @error('password') is-invalid @enderror"
        placeholder="Password" required />
      @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror

      <button type="submit" class="btn-primary">Login</button>

      <div class="enroll-link">
        <a href="{{ route('enroll.form') }}">Not Enrolled Yet? Enroll Now!</a>
      </div>
    </form>
  </div>
</div>

</body>
</html>
