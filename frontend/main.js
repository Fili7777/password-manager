// =====================
// LOGIN FORM
// =====================
if (document.getElementById("loginForm")) {
  const passwordInput = document.getElementById("password");
  const togglePassword = document.getElementById("togglePassword");

  // mostra/nasconde password con icona integrata
  togglePassword.addEventListener("click", () => {
    const type = passwordInput.type === "password" ? "text" : "password";
    passwordInput.type = type;

    // cambia colore icona quando aperto/nascosto
    togglePassword.style.color = type === "password" ? "var(--text-muted)" : "var(--accent)";
  });
}

// =====================
// REGISTER FORM
// =====================
if (document.getElementById("registerForm")) {
  const passwordInput = document.getElementById("passwordRegister");
  const togglePasswordRegister = document.getElementById("togglePasswordRegister");

  // mostra/nasconde password con icona integrata
  togglePasswordRegister.addEventListener("click", () => {
    const type = passwordInput.type === "password" ? "text" : "password";
    passwordInput.type = type;

    // cambia colore icona quando aperto/nascosto
    togglePasswordRegister.style.color = type === "password" ? "var(--text-muted)" : "var(--accent)";
  });
}

// =====================
// DASHBOARD
// =====================
if (document.getElementById("dashboard")) {
  // codice per dashboard
}