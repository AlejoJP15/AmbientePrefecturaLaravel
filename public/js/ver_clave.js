document.addEventListener("DOMContentLoaded", function () {
  const passwordInput = document.getElementById("contraseña");
  const toggleBtn = document.getElementById("togglePassword");

  if (passwordInput && toggleBtn) {
    toggleBtn.addEventListener("click", function () {
      const isPassword = passwordInput.type === "password";
      passwordInput.type = isPassword ? "text" : "password";

      // Alternar íconos
      this.classList.toggle("fa-eye");
      this.classList.toggle("fa-eye-slash");
    });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const passwordInput = document.getElementById("contraseña_juridico");
  const toggleBtn = document.getElementById("togglePasswordJuri");

  if (passwordInput && toggleBtn) {
    toggleBtn.addEventListener("click", function () {
      const isPassword = passwordInput.type === "password";
      passwordInput.type = isPassword ? "text" : "password";

      // Alternar íconos
      this.classList.toggle("fa-eye");
      this.classList.toggle("fa-eye-slash");
    });
  }
});

document.addEventListener('DOMContentLoaded', function () {
  function soloNumeros(input, maxLength) {
    input.addEventListener('input', function () {
      this.value = this.value.replace(/\D/g, '').slice(0, maxLength);
    });

    input.addEventListener('keypress', function (e) {
      if (!/[0-9]/.test(e.key)) {
        e.preventDefault();
      }
    });
  }

  soloNumeros(document.getElementById('cedula'), 10);
  soloNumeros(document.getElementById('telefono'), 9);
  soloNumeros(document.getElementById('celular'), 10);
});