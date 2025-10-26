// js/register.js
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("registerForm");
  const formMessage = document.getElementById("form-message");
  const registerBtn = document.getElementById("registerBtn");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    formMessage.textContent = "";
    registerBtn.disabled = true;
    registerBtn.textContent = "Registering...";

    const formData = new FormData(form);

    try {
      const response = await fetch("../actions/register_customer_action.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        formMessage.style.color = "green";
        formMessage.textContent = result.message || "Registration successful!";
        setTimeout(() => {
          window.location.href = "login.php";
        }, 1200);
      } else {
        formMessage.style.color = "red";
        formMessage.textContent = result.message || "Registration failed.";
      }
    } catch (error) {
      console.error(error);
      formMessage.style.color = "red";
      formMessage.textContent = "An error occurred while registering.";
    }

    registerBtn.disabled = false;
    registerBtn.textContent = "Register";
  });
});
