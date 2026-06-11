<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Restablecer contraseña</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  {{-- mismo CSS del login --}}
  <link rel="stylesheet" href="{{ asset('styles/login.css') }}">
</head>
<body>
  <div class="login-box">
    <h2>Restablecer contraseña</h2>

    @if ($errors->any())
      <div class="errors">
        <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
      </div>
    @endif

    <form method="POST" action="{{ route('password.update', $token) }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="email" value="{{ $email }}">

      <label for="password">Nueva contraseña</label>
      <input
        type="password"
        id="password"
        name="password"
        required
        minlength="8"
        placeholder="Mín. 8, 1 mayúscula, 1 número, 1 especial"
        pattern="^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$"
        title="Mínimo 8 caracteres, con al menos 1 mayúscula, 1 número y 1 símbolo"
      >

      <label for="password_confirmation">Confirmar contraseña</label>
      <input type="password" id="password_confirmation" name="password_confirmation" required>

      <button type="submit" class="btn-login">Guardar</button>
    </form>

    <div class="footer">© {{ date('Y') }}</div>
  </div>
</body>
</html>
