<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Registration - CvSU Portal</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    /* RESET */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    /* BODY */
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #001a1a, #002b2b);
      color: #f5f5f5;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
      position: relative;
    }

    /* BACKGROUND GLOW */
    .glow-behind {
      position: absolute;
      top: 50%;
      left: 50%;
      width: 600px;
      height: 600px;
      background: radial-gradient(circle at center, rgba(255, 215, 0, 0.12), transparent 70%);
      transform: translate(-50%, -50%);
      filter: blur(50px);
      z-index: 0;
      pointer-events: none;
    }

    /* CONTAINER */
    .register-container {
      position: relative;
      z-index: 1;
      background: rgba(0, 25, 25, 0.95);
      border-radius: 12px;
      box-shadow: 0 0 25px rgba(0, 0, 0, 0.6);
      width: 100%;
      max-width: 420px;
      padding: 35px 40px;
      text-align: center;
    }

    .logo {
      width: 130px;
      margin-bottom: 10px;
    }

    h2 {
      color: #FFD700;
      font-size: 22px;
      margin-bottom: 15px;
    }

    .note-text {
      font-size: 13px;
      color: #ccc;
      font-style: italic;
      margin-bottom: 20px;
    }

    hr {
      border: 0;
      height: 1px;
      background: #333;
      margin: 20px 0;
    }

    /* FORM */
    form input {
      width: 100%;
      padding: 12px;
      margin: 8px 0;
      border: 1px solid #444;
      border-radius: 8px;
      font-size: 15px;
      background-color: #0b2c2c;
      color: #f5f5f5;
      transition: border-color 0.2s, background-color 0.3s;
    }

    form input:focus {
      outline: none;
      border-color: #FFD700;
      background-color: #103636;
    }

    form button {
      width: 100%;
      padding: 12px;
      background-color: #FFD700;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      color: #003300;
      font-weight: bold;
      cursor: pointer;
      margin-top: 10px;
      transition: background-color 0.3s, transform 0.1s ease-in-out;
    }

    form button:hover {
      background-color: #FFC300;
      transform: scale(1.02);
    }

    .back-link {
      margin-top: 15px;
      font-size: 14px;
      color: #ccc;
    }

    .back-link a {
      color: #FFD700;
      font-weight: bold;
      text-decoration: none;
      transition: color 0.3s;
    }

    .back-link a:hover {
      text-decoration: underline;
      color: #fff;
    }
  </style>
</head>
<body>

  <!-- Subtle Glow -->
  <div class="glow-behind"></div>

  <!-- Registration Form -->
  <div class="register-container">
    <img src="{{ asset('images/logo123-removebg-preview.png') }}" alt="CvSU Logo" class="logo">
    <h2>Admin Registration</h2>
    <p class="note-text">(Please fill out all fields to create your admin account)</p>
    <hr>

    <form method="POST" action="{{ route('register.admin.submit') }}">
      @csrf
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Admin Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
      <button type="submit">Register</button>
    </form>

    <div class="back-link">
      Already have an account? <a href="{{ route('login.admin') }}">Login here</a>
    </div>
  </div>

</body>
</html>
