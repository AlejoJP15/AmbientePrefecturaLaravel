@extends('layouts.app')
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login | Prefectura de Chimborazo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- 🔹 Agrega esto en el <head> de tu plantilla si aún no tienes Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('styles/login.css') }}">
</head>
<body>

  <div class="login-box">
    <img src="{{ asset('img/logo_login.jpg') }}" alt="Prefectura de Chimborazo" class="logo">
    
    <h2>Ingreso al Sistema</h2>

    @if ($errors->any())
      <div class="errors">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    @if (session('success'))
      <div class="alert-success" id="flash-success" style="margin:12px 0; padding:10px; border:1px solid #cce5ff; background:#e6f2ff; border-radius:6px;">
        {{ session('success') }}
      </div>
    @endif

    <form action="{{ route('login.submit') }}" method="POST">
      @csrf

      <label for="usuario">Usuario (Documento CI)</label>
      <input type="text" id="usuario" name="usuario" required>


<!-- 🔹 Campo de contraseña con el ojo elegante -->
      <label for="password">Contraseña</label>
      <div style="position: relative; width: 100%;">
        <input 
          type="password" 
          id="password" 
          name="password" 
          required 
          style="width: 100%; padding-right: 40px;"
        >
        <i 
          id="togglePassword" 
          class="fa-solid fa-eye" 
          style="
            position: absolute;
            right: 10px;
            top: 40%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #3587dfe6; /* Azul elegante */
            font-size: 18px;
            transition: color 0.3s ease;
          "
        ></i>
      </div>

      <script>
      const passwordInput = document.getElementById("password");
      const togglePassword = document.getElementById("togglePassword");

      togglePassword.addEventListener("click", () => {
        const isPassword = passwordInput.type === "password";
        passwordInput.type = isPassword ? "text" : "password";

        // Alterna entre el ojo normal y el ojo tachado
        togglePassword.classList.toggle("fa-eye");
        togglePassword.classList.toggle("fa-eye-slash");
      });
      </script>


      <!-- Enlace para recuperar contraseña -->
      <div style="text-align: right; margin: 1px 0 12px 0;">
        <a href="{{ route('password.request') }}" style="font-size: 0.9em; color: #007BFF; text-decoration: none;">
          ¿Olvidaste tu contraseña?
        </a>
      </div>

      <button type="submit" class="btn-login">Iniciar Sesión</button>
      <br><br>
      <center>
        ¿No tienes una cuenta? 
        <a href="{{ route('registro') }}">Regístrate</a>
      </center>
    </form>

    <div class="footer">
      © {{ date('Y') }} Prefectura de Chimborazo
    </div>
  </div>

</body>
</html>


