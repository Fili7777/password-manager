if (document.getElementById("loginForm")) {
  const passwordInput = document.getElementById("password");
  const togglePassword = document.getElementById("togglePassword");

  // permette di nascondere o mostrare password
  togglePassword.addEventListener("click", () => {
    if (passwordInput.type === "password") {
      passwordInput.type = "text"; // mostra la password
      togglePassword.textContent = "Nascondi";
    } else {
      passwordInput.type = "password"; // nasconde di nuovo
      togglePassword.textContent = "Mostra";
    }
  });
}

if (document.getElementById("registerForm")) {
  const nameInput = document.getElementById("nome");
  const passwordInput = document.getElementById("password");
  const togglePasswordRegister = document.getElementById(
    "togglePasswordRegister"
  );

  // permette di nascondere o mostrare password
  togglePasswordRegister.addEventListener("click", () => {
    if (passwordInput.type === "password") {
      passwordInput.type = "text";
      togglePasswordRegister.textContent = "Nascondi";
    } else {
      passwordInput.type = "password";
      togglePasswordRegister.textContent = "Mostra";
    }
  });
}

if (document.getElementById("dashboard")) {
  // codice per dashboard
}
