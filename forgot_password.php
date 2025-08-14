<!DOCTYPE html>
<html>
<head>
  <title>Forgot Password</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #fff3e0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .box {
      background: white;
      padding: 30px;
      border-radius: 8px;
      width: 300px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    h2 {
      text-align: center;
      color: #e65100;
    }

    input {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    button {
      background: #ff7043;
      color: white;
      border: none;
      padding: 10px;
      width: 100%;
      margin-top: 20px;
      border-radius: 4px;
      cursor: pointer;
    }

    button:hover {
      background: #f4511e;
    }

    .dark-toggle {
      text-align: center;
      margin-top: 15px;
      color: #888;
      cursor: pointer;
      font-size: 14px;
    }

    .info {
      margin-top: 10px;
      text-align: center;
      font-size: 14px;
      color: #666;
    }

    body.dark-mode {
      background-color: #121212;
      color: #f5f5f5;
    }

    body.dark-mode .box {
      background: #1e1e1e;
    }

    body.dark-mode input,
    body.dark-mode button {
      background-color: #333;
      color: #fff;
      border: 1px solid #555;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>Reset Password</h2>
    <form method="POST">
      <input type="email" name="email" placeholder="Enter your email" required>
      <button type="submit">Send Reset Link</button>
    </form>
    <p class="info">This is a placeholder. Email service not yet connected.</p>
    <div class="dark-toggle" onclick="toggleDarkMode()">🌓 Toggle Dark Mode</div>
  </div>

  <script>
    function toggleDarkMode() {
      document.body.classList.toggle('dark-mode');
      localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
    }

    window.onload = function () {
      if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark-mode');
      }
    };
  </script>
</body>
</html>
