<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - Sure Shop</title>
<style>
:root {
  --brand: #660a38;
  --bg: #ffffff;
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: Arial, sans-serif;
  background-color: var(--bg);
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}
.container {
  width: 380px;
  background-color: #fff;
  border-radius: 12px;
  box-shadow: 0 0 15px rgba(0,0,0,0.1);
  padding: 2rem;
}
h1 {
  text-align: center;
  color: var(--brand);
  margin-bottom: 1.5rem;
}
.form-row {
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
}
label {
  font-size: .9rem;
  margin-bottom: .3rem;
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
}
button:hover { opacity: 0.9; }
.feedback {
  font-size: .85rem;
  color: #777;
  margin-top: .3rem;
}
p {
  text-align: center;
  margin-top: 1rem;
  font-size: .9rem;
}
a { color: var(--brand); text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
</head>
<body>
  <div class="container">
    <h1>Register</h1>
    <form id="registerForm" novalidate>
      <div class="form-row">
        <label>Full Name</label>
        <input type="text" id="name" name="customer_name" required>
      </div>
      <div class="form-row">
        <label>Email</label>
        <input type="email" id="email" name="customer_email" required>
        <small id="email-feedback" class="feedback"></small>
      </div>
      <div class="form-row">
        <label>Password</label>
        <input type="password" id="password" name="customer_pass" required>
      </div>
      <div class="form-row">
        <label>Country</label>
                        <input type="text" id="country" name="customer_country" placeholder="e.g. Nigeria" required>
      </div>
      <div class="form-row">
        <label>City</label>
        <input type="text" id="city" name="customer_city" required>
      </div>
      <div class="form-row">
        <label>Contact Number</label>
        <input type="text" id="contact" name="customer_contact" required>
      </div>
      <div class="form-row">
        <label>Profile Image (optional)</label>
        <input type="file" id="image" name="customer_image" accept="image/*">
      </div>
      <input type="hidden" name="user_role" value="2">
      <button type="submit" id="registerBtn">Register</button>
      <div id="form-message" class="feedback"></div>
    </form>
    <p>Already registered? <a href="login.php">Login here</a>.</p>
  </div>

<script>
const countryInput = document.getElementById('country');
const countries = [
                  'Nigeria','Ghana','Kenya','Egypt','South Africa','Morocco',
  'United Kingdom','Canada','France','Germany',
  'Brazil','India','Australia','Japan'
];
setInterval(() => {
  const random = Math.floor(Math.random() * countries.length);
  countryInput.placeholder = countries[random];
}, 3000);
</script>

<script src="../js/register.js"></script>

</body>
</html>
