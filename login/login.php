<?php
session_start();
if (!empty($_SESSION['customer_id'])) {
    header('Location: ../dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Sure Shop</title>
<style>
:root {
  --brand: #660a38;
  --bg: #ffffff;
}
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}
body {
  font-family: Arial, sans-serif;
  background-color: var(--bg);
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}
.container {
  width: 340px;
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 0 15px rgba(0,0,0,0.1);
  padding: 2rem;
}
h1 {
  text-align: center;
  color: var(--brand);
  margin-bottom: 1.2rem;
}
.form-row {
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
}
label {
  font-size: .9rem;
  margin-bottom: .3rem;
  color: #222;
}
input {
  padding: .6rem;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: .95rem;
}
button {
  width: 100%;
  padding: .7rem;
  border: none;
  border-radius: 6px;
  background-color: var(--brand);
  color: #fff;
  font-weight: 700;
  cursor: pointer;
  margin-top: .5rem;
  transition: opacity 0.2s ease-in-out;
}
button:hover { opacity: 0.9; }
.feedback {
  font-size: .9rem;
  margin-top: .8rem;
  text-align: center;
}
.feedback.success { color: green; }
.feedback.error { color: red; }
p {
  text-align: center;
  margin-top: 1rem;
  font-size: .9rem;
}
a {
  color: var(--brand);
  text-decoration: none;
}
a:hover {
  text-decoration: underline;
}
</style>
</head>
<body>
  <div class="container">
    <h1>Login</h1>
    <form id="loginForm" method="post" novalidate>
      <div class="form-row">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="form-row">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" id="loginBtn">Login</button>
    </form>
    <div id="login-feedback" class="feedback"></div>
    <p>Don't have an account? <a href="register.php">Register</a>.</p>
  </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('loginForm');
  const feedback = document.getElementById('login-feedback');
  const btn = document.getElementById('loginBtn');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    feedback.textContent = '';
    feedback.className = 'feedback';
    btn.disabled = true;
    btn.textContent = 'Signing in...';

    const fd = new FormData(form);

    try {
      const res = await fetch('../actions/login_action.php', {
        method: 'POST',
        body: fd
      });
      const json = await res.json();

      if (json.success) {
        feedback.textContent = json.message || 'Login successful. Redirecting...';
        feedback.classList.add('success');
        setTimeout(() => {
          window.location.href = '../dashboard.php';
        }, 1000);
      } else {
        feedback.textContent = json.message || 'Login failed.';
        feedback.classList.add('error');
      }
    } catch (err) {
      console.error(err);
      feedback.textContent = 'An error occurred. Try again.';
      feedback.classList.add('error');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Login';
    }
  });
});
</script>
</body>
</html>
