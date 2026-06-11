<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Recuperar contraseña</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  {{-- CSS del login --}}
  <link rel="stylesheet" href="{{ asset('styles/login.css') }}">
</head>
<body>
  <div class="login-box">
    <img src="{{ asset('img/logo_login.jpg') }}" alt="Prefectura de Chimborazo" class="logo">
    <h2>Recuperar contraseña</h2>

    @if (session('success'))
      <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
      <div class="errors">
        <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <label for="email">Correo</label>
      <input type="email" id="email" name="email" required autofocus placeholder="tu@correo.com">
      <button type="submit" class="btn-login">Enviar enlace</button> <br><br>
      <button type="submit" class="btn-register" onclick="window.location.href='{{ route('login') }}'">Cancelar</button>
    </form>

    <div class="footer">© {{ date('Y') }}</div>
  </div>
</body>
</html>
