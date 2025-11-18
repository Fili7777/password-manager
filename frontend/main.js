// =====================
// UTILITY JWT
// =====================
function getToken() {
  return localStorage.getItem("jwtToken");
}

function setToken(token) {
  localStorage.setItem("jwtToken", token);
}

function removeToken() {
  localStorage.removeItem("jwtToken");
}

// =====================
// LOGIN FORM
// =====================
if (document.getElementById("loginForm")) {
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const togglePassword = document.getElementById("togglePassword");

  // toggle password
  togglePassword.addEventListener("click", () => {
    const type = passwordInput.type === "password" ? "text" : "password";
    passwordInput.type = type;
    togglePassword.style.color =
      type === "password" ? "var(--text-muted)" : "var(--accent)";
  });

  document.getElementById("loginForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = emailInput.value.trim();
    const password = passwordInput.value.trim();

    if (!email || !password) {
      alert("Inserisci email e password!");
      return;
    }

    try {
      const res = await fetch(
        "http://localhost/password-manager-backend/login.php",
        {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ email, password }),
        }
      );

      const data = await res.json();

      if (res.ok && data.token) {
        setToken(data.token);
        window.location.href = "dashboard.html";
      } else {
        alert(data.message);
      }
    } catch (err) {
      alert("Errore server: " + err);
    }
  });
}

// =====================
// REGISTER FORM
// =====================
if (document.getElementById("registerForm")) {
  const nameInput = document.getElementById("nome");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("passwordRegister");
  const togglePasswordRegister = document.getElementById(
    "togglePasswordRegister"
  );

  togglePasswordRegister.addEventListener("click", () => {
    const type = passwordInput.type === "password" ? "text" : "password";
    passwordInput.type = type;
    togglePasswordRegister.style.color =
      type === "password" ? "var(--text-muted)" : "var(--accent)";
  });

  document
    .getElementById("registerForm")
    .addEventListener("submit", async (e) => {
      e.preventDefault();

      const name = nameInput.value.trim();
      const email = emailInput.value.trim();
      const password = passwordInput.value.trim();

      if (!name || !email || !password) {
        alert("Compila tutti i campi!");
        return;
      }

      try {
        const res = await fetch(
          "http://localhost/password-manager-backend/register.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ name, email, password }),
          }
        );

        const data = await res.json();
        alert(data.message);

        if (res.ok) {
          window.location.href = "login.html";
        }
      } catch (err) {
        alert("Errore server: " + err);
      }
    });
}

// =====================
// DASHBOARD
// =====================
if (document.getElementById("dashboard")) {
  const token = getToken();

  if (!token) {
    alert("Devi effettuare il login per accedere alla dashboard!");
    window.location.href = "login.html";
  }

  const passwordListDiv = document.getElementById("passwordList");
  const addBtn = document.getElementById("addPasswordBtn");
  const addForm = document.getElementById("addPasswordForm");
  const saveBtn = document.getElementById("savePasswordBtn");
  const cancelBtn = document.getElementById("cancelPasswordBtn");

  const newSiteInput = document.getElementById("newSite");
  const newUserInput = document.getElementById("newUser");
  const newPassInput = document.getElementById("newPass");
  const toggleNewPass = document.getElementById("toggleNewPass");

  // toggle password nuova
  if (toggleNewPass) {
    toggleNewPass.addEventListener("click", () => {
      const type = newPassInput.type === "password" ? "text" : "password";
      newPassInput.type = type;
      toggleNewPass.style.color =
        type === "password" ? "var(--text-muted)" : "var(--accent)";
    });
  }

  // render password da backend
  async function renderPasswords() {
    try {
      const res = await fetch(
        "http://localhost/password-manager-backend/passwords.php",
        {
          headers: { Authorization: "Bearer " + token },
        }
      );

      const data = await res.json();
      passwordListDiv.innerHTML = "";

      data.forEach((pwd) => {
        const div = document.createElement("div");
        div.classList.add("password-box");
        div.innerHTML = `<strong>${pwd.site}</strong><br>Utente: ${pwd.username}<br>Password: ${pwd.password}<br><br>`;
        passwordListDiv.appendChild(div);
      });
    } catch (err) {
      passwordListDiv.innerHTML =
        "<p>Errore nel caricamento delle password</p>";
    }
  }

  renderPasswords();

  // LOGOUT
  document.getElementById("logoutBtn").addEventListener("click", () => {
    removeToken();
    window.location.href = "login.html";
  });

  // Mostra/nasconde form aggiungi
  addBtn.addEventListener("click", () => (addForm.style.display = "block"));
  cancelBtn.addEventListener("click", () => {
    addForm.style.display = "none";
    newSiteInput.value = "";
    newUserInput.value = "";
    newPassInput.value = "";
  });

  // Salva nuova password
  saveBtn.addEventListener("click", async () => {
    const site = newSiteInput.value.trim();
    const user = newUserInput.value.trim();
    const pass = newPassInput.value.trim();

    if (!site || !user || !pass) {
      alert("Compila tutti i campi!");
      return;
    }

    try {
      const res = await fetch(
        "http://localhost/password-manager-backend/passwords.php",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            Authorization: "Bearer " + token,
          },
          body: JSON.stringify({ site, username: user, password: pass }),
        }
      );

      const data = await res.json();
      if (res.ok) {
        alert(data.message);
        newSiteInput.value = "";
        newUserInput.value = "";
        newPassInput.value = "";
        addForm.style.display = "none";
        renderPasswords();
      } else {
        alert(data.message);
      }
    } catch (err) {
      alert("Errore server: " + err);
    }
  });
}
