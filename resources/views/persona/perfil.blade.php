@extends('layouts.app')

@section('content')
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;

    $role = optional(Auth::user()->perfil)->descripcion;

    $candidates = [
        'Administrador'    => 'admin.dashboard',
        'Técnico'          => 'tecnico.dashboard',
        'Tecnico'          => 'tecnico.dashboard',
        'Coordinador'      => 'coordinador.dashboard',
        'Director'         => 'director.dashboard',
        'Usuario externo'  => 'usuario.dashboard',
    ];

    $dashboardRoute = $candidates[$role] ?? null;

    if (!$dashboardRoute || !Route::has($dashboardRoute)) {
        $dashboardRoute = Route::has('home') ? 'home' : (Route::has('dashboard') ? 'dashboard' : 'login');
    }
@endphp
@php
  $persona   = $user ?? Auth::user();
  $tipoRaw   = (string) ($persona->tipo_persona ?? '');
  // Normalización mínima para "JURÍDICA" → "juridica"
  $tipoNorm  = mb_strtolower(str_replace(['Í','í'], 'i', $tipoRaw));

  // SOLO por tipo_persona
  $esJuridica = ($tipoNorm === 'juridica');
@endphp
    <link rel="stylesheet" href="{{ asset('styles/perfil2.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <div class="perfil-container">

      <!--{{-- Mensaje de éxito --}}
        @if (session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Errores de validación --}}
        @if ($errors->any())
            <div class="alert-danger">
                <strong>Se encontraron errores:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif -->


            <!-- Encabezado del perfil -->
            <div class="perfil-header">
                <label for="fotoPerfil">
                    <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Foto de perfil" id="previewFoto">
                </label>
                <input type="file" name="foto" id="fotoPerfil" accept="image/*" style="display: none;" onchange="previewImagen(event)">

                
                <p>
                    <input type="text" name="rol" value="{{ $user->perfil->descripcion ?? 'Usuario' }}" class="input-texto" readonly>
                </p>
            </div>

@if($esJuridica)
  {{-- ====== PERFIL JURÍDICO / USUARIO EXTERNO (tabla: usuario_externo) ====== --}}
 <form action="{{ route('persona.perfil.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="perfil-datos-grid">
        <div class="perfil-dato">
        <h5><i class="fas fa-building"></i> Nombre organización</h5>
        <input type="text" name="organizacion" class="input-campo"
                value="{{ old('organizacion', optional($externo)->organizacion) }}">
        @error('organizacion') <small style="color:#c00">{{ $message }}</small> @enderror
        </div>
        <div class="perfil-dato">
        <h5><i class="fas fa-store"></i> Nombre comercial</h5>
        <input type="text" name="comercial" class="input-campo"
                value="{{ old('comercial', optional($externo)->comercial) }}">
        @error('comercial') <small style="color:#c00">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="perfil-datos-grid">
        <div class="perfil-dato">
            <h5><i class="fas fa-user"></i> Usuario:</h5>
            <input type="text" name="cedula" value="{{ old('usuario', $user->usuario) }}" class="input-campo" readonly>
         </div>
        <div class="perfil-dato">
        <h5><i class="fas fa-id-card"></i> Cédula / RUC representante</h5>
        <input type="text" name="representante" class="input-campo" maxlength="13"
                value="{{ old('representante', optional($externo)->representante) }}">
        @error('representante') <small style="color:#c00">{{ $message }}</small> @enderror
        </div>
         
        <div class="perfil-dato">
        <h5><i class="fas fa-user-tie"></i> Nombres y apellidos del representante</h5>
        <input type="text" name="nombre_representante" class="input-campo"
                value="{{ old('nombre_representante', optional($externo)->nombre_representante) }}">
        @error('nombre_representante') <small style="color:#c00">{{ $message }}</small> @enderror
        </div>
        <div class="perfil-dato">
        <h5><i class="fas fa-briefcase"></i> Cargo representante</h5>
        <input type="text" name="cargo_representante" class="input-campo"
                value="{{ old('cargo_representante', optional($externo)->cargo_representante) }}">
        @error('cargo_representante') <small style="color:#c00">{{ $message }}</small> @enderror
        </div>
    </div>



    <div class="perfil-datos-grid">
       
        <div class="perfil-dato">
        <h5><i class="fas fa-map"></i> Sector</h5>
        <input type="text" name="sector" class="input-campo"
                value="{{ old('sector', optional($externo)->sector) }}">
        @error('sector') <small style="color:#c00">{{ $message }}</small> @enderror
        </div>
    </div>


    {{-- Contacto (PERSONA) --}}
    <h4>Información de Contacto</h4>
    <div class="perfil-datos-grid">
        <div class="perfil-dato">
        <h5><i class="fas fa-phone"></i> Teléfono</h5>
        <input type="text" name="telefono" class="input-campo"
                value="{{ old('telefono', $persona->telefono) }}">
        @error('telefono') <small style="color:#c00">{{ $message }}</small> @enderror
        </div>
        <div class="perfil-dato">
        <h5><i class="fas fa-mobile-alt"></i> Celular</h5>
        <input type="text" name="celular" class="input-campo"
                value="{{ old('celular', $persona->celular) }}">
        @error('celular') <small style="color:#c00">{{ $message }}</small> @enderror
        </div>
    </div>

    <div class="perfil-datos-grid">
        <div class="perfil-dato">
        <h5><i class="fas fa-envelope"></i> Email</h5>
        <input type="email" name="email" class="input-campo"
                value="{{ old('email', $persona->email) }}">
        @error('email') <small style="color:#c00">{{ $message }}</small> @enderror
        </div>
        <div class="perfil-dato">
        <h5><i class="fas fa-map-marker-alt"></i> Dirección</h5>
        <input type="text" name="direccion" class="input-campo"
                value="{{ old('direccion', $persona->direccion) }}">
        @error('direccion') <small style="color:#c00">{{ $message }}</small> @enderror
        </div>
    </div>
    <div class="perfil-buttons">
        <button type="submit" class="btn-editar">Guardar Cambios</button>
    </div>
 </form>
@else
        <form action="{{ route('persona.perfil.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
                <h2>
                    <input type="text" name="nombres" value="{{ old('nombres', $user->nombres) }}" class="input-texto" required>
                </h2>
                

            <!-- Campos en dos columnas -->
            <div class="perfil-datos-grid">
                <div class="perfil-dato">
                    <h5><i class="fas fa-venus-mars"></i> Género:</h5>
                    <select name="genero" class="input-campo" required>
                        <option value="">Seleccione género</option>
                        <option value="MASCULINO" {{ strtoupper(old('genero', $user->genero)) == 'MASCULINO' ? 'selected' : '' }}>Masculino</option>
                        <option value="FEMENINO"  {{ strtoupper(old('genero', $user->genero)) == 'FEMENINO'  ? 'selected' : '' }}>Femenino</option>
                        <option value="OTRO"      {{ strtoupper(old('genero', $user->genero)) == 'OTRO'      ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>

                <div class="perfil-dato">
                    <h5><i class="fas fa-user"></i> Usuario:</h5>
                    <input type="text" name="cedula" value="{{ old('usuario', $user->usuario) }}" class="input-campo" readonly>
                </div>

                <div class="perfil-dato">
                    <h5><i class="fas fa-envelope"></i> Email:</h5>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-campo" required>
                </div>

                <div class="perfil-dato">
                    <h5><i class="fas fa-at"></i> Correo alternativo:</h5>
                    <input type="email" name="correo_alternativo" value="{{ old('correo_alternativo', $user->correo_alternativo) }}" class="input-campo">
                </div>

                <div class="perfil-dato">
                    <h5><i class="fas fa-phone"></i> Teléfono:</h5>
                    <input type="text" name="telefono" maxlength="10"
                        value="{{ old('telefono', $user->telefono) }}"
                        class="input-campo"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                        title="Ingrese solo números (máximo 10)">
                </div>

                <div class="perfil-dato">
                    <h5><i class="fas fa-mobile-alt"></i> Celular:</h5>
                    <input type="text" name="celular" maxlength="10"
                        value="{{ old('celular', $user->celular) }}"
                        class="input-campo"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                        title="Ingrese solo números (máximo 10)">
                </div>

                <div class="perfil-dato">
                    <h5><i class="fas fa-map-marker-alt"></i> Dirección:</h5>
                    <input type="text" name="direccion" value="{{ old('direccion', $user->direccion) }}" class="input-campo">
                </div>

                <div class="perfil-dato">
                    <h5><i class="fas fa-graduation-cap"></i> Nivel de instrucción:</h5>
                    <select name="nivel_instruccion" class="input-campo" required>
                        <option value="">Seleccione nivel de instrucción</option>
                        <option value="PRIMARIA"      {{ strtoupper(old('nivel_instruccion', $user->nivel_instruccion)) == 'PRIMARIA'      ? 'selected' : '' }}>Primaria</option>
                        <option value="SECUNDARIA"    {{ strtoupper(old('nivel_instruccion', $user->nivel_instruccion)) == 'SECUNDARIA'    ? 'selected' : '' }}>Secundaria</option>
                        <option value="UNIVERSITARIA" {{ strtoupper(old('nivel_instruccion', $user->nivel_instruccion)) == 'UNIVERSITARIA' ? 'selected' : '' }}>Universitaria</option>
                        <option value="POSGRADO"      {{ strtoupper(old('nivel_instruccion', $user->nivel_instruccion)) == 'POSGRADO'      ? 'selected' : '' }}>Posgrado</option>
                    </select>
                </div>
            </div>

            <!-- Botones -->
            <div class="perfil-buttons">
                <button type="submit" class="btn-editar">Guardar Cambios</button>
            </div>
        </form>
@endif





        <form action="{{ route('persona.password.update') }}" method="POST" novalidate autocomplete="off" style="margin-top:2rem">
            @csrf

            {{-- Mensajes --}}
            @if (session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert-error">
                <ul style="margin:0;padding-left:1rem">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                </div>
            @endif

            <h3 style="margin-bottom:1rem"><i class="fas fa-user-shield"></i> Cambiar contraseña</h3>

            <div class="perfil-datos-grid">
                <div class="perfil-dato">
                <h5><i class="fas fa-lock"></i> Contraseña actual:</h5>
                <div class="input-wrapper" style="position:relative">
                    <input id="current_password" type="password" name="current_password"
                        class="input-campo" required autocomplete="current-password">
                    <button type="button" onclick="togglePassword('current_password','ico1')" style="position:absolute;right:10px;top:9px;background:none;border:none;cursor:pointer">
                    <i id="ico1" class="fa-solid fa-eye"></i>
                    </button>
                </div>
                @error('current_password') <small style="color:#c00">{{ $message }}</small> @enderror
                </div>

                <div class="perfil-dato">
                <h5><i class="fas fa-key"></i> Nueva contraseña:</h5>
                <div class="input-wrapper" style="position:relative">
                    <input id="password" type="password" name="password" class="input-campo" required
                        minlength="8"
                        pattern="^(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z0-9]).{8,}$"
                        title="Mínimo 8 caracteres, al menos 1 mayúscula, 1 número y 1 carácter especial."
                        autocomplete="new-password">
                    <button type="button" onclick="togglePassword('password','ico2')" style="position:absolute;right:10px;top:9px;background:none;border:none;cursor:pointer">
                    <i id="ico2" class="fa-solid fa-eye"></i>
                    </button>
                </div>
                @error('password') <small style="color:#c00">{{ $message }}</small> @enderror
                </div>

                <div class="perfil-dato">
                <h5><i class="fas fa-check-double"></i> Confirmar nueva contraseña:</h5>
                <div class="input-wrapper" style="position:relative">
                    <input id="password_confirmation" type="password" name="password_confirmation"
                        class="input-campo" required autocomplete="new-password">
                    <button type="button" onclick="togglePassword('password_confirmation','ico3')" style="position:absolute;right:10px;top:9px;background:none;border:none;cursor:pointer">
                    <i id="ico3" class="fa-solid fa-eye"></i>
                    </button>
                </div>
                </div>
            </div>

            <ul id="password-hints" style="font-size:.9rem;line-height:1.4;margin:.5rem 0 1rem">
                <li id="hint-length">Mínimo 8 caracteres</li>
                <li id="hint-upper">Al menos 1 mayúscula (A–Z)</li>
                <li id="hint-number">Al menos 1 número (0–9)</li>
                <li id="hint-special">Al menos 1 carácter especial (!@#$…)</li>
            </ul>

            {{-- Botones alineados --}}
            <div class="perfil-buttons">
                <button type="submit" class="btn-editar">Actualizar contraseña</button>
                <a href="{{ route($dashboardRoute) }}" class="btn-volver">Cancelar</a>
            </div>
        </form>
    </div>

    <!-- Vista previa de la foto -->
    <script>
        function previewImagen(event) {
            const reader = new FileReader();
            reader.onload = function () {
                document.getElementById('previewFoto').src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
    <script>
        function togglePassword(inputId, iconId){
        const inp = document.getElementById(inputId);
        const ico = document.getElementById(iconId);
        inp.type = inp.type === 'password' ? 'text' : 'password';
        ico.classList.toggle('fa-eye');
        ico.classList.toggle('fa-eye-slash');
        }

        const pwd = document.getElementById('password');
        if (pwd) {
        const hints = {
            length: document.getElementById('hint-length'),
            upper:  document.getElementById('hint-upper'),
            number: document.getElementById('hint-number'),
            special: document.getElementById('hint-special'),
        };
        pwd.addEventListener('input', () => {
            const v = pwd.value;
            toggleHint(hints.length, v.length >= 8);
            toggleHint(hints.upper, /[A-Z]/.test(v));
            toggleHint(hints.number, /\d/.test(v));
            toggleHint(hints.special, /[^A-Za-z0-9]/.test(v));
        });
       function toggleHint(li, ok){
            if (ok) {
                li.classList.add('ok');
            } else {
                li.classList.remove('ok');
            }
            }
        }
    </script>

@endsection
