@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('styles/registro_user.css') }}">
<!-- Agrega esto en el <head> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="{{asset('js/ver_clave.js') }}"></script>
<script src="{{asset('js/natural_juri.js') }}"></script>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<img src="{{ asset('img/logo_login.jpg') }}" alt="Logo" class="logo">

<form action="{{ route('personas.store') }}" method="POST">
    @csrf

    <center><h2 style="margin-top: 30px; color: #1565c0;">Registro de Usuario</h2></center> 

    <h6 style="margin-top: 30px; color: #1565c0;">1. Tipo de Persona</h6>
    <!-- Tipo de Persona -->
    <div class="form-group">
        <select name="tipo_persona" id="tipo_persona" class="form-control" required>
            <option value="natural" selected>Natural</option>
            <option value="juridica">Jurídica</option>
        </select>
    </div>

    <!-- FORMULARIO NATURAL -->

    <div id="campos_natural">
        <h6 style="margin-top: 30px; color: #1565c0;">2. Tipo de Documento</h6>
        <div class="form-group">
            <select name="tipo_documento" id="tipo_documento" class="form-control" required>
                <option value="" disable selected hidden>Seleccione</option>
                <option value="cedula">Cédula</option>
                <option value="ruc">RUC</option>
            </select>
        </div>

    <h6 style="margin-top: 30px; color: #1565c0;">3. Información Personal</h6>

        <div class="form-group">
            <!--
            <label for="usuario" id="label_usuario">Número de documento</label>
            <input type="text" name="usuario" id="usuario" class="form-control" maxlength="13" value="{{ old('usuario') }}" required>
            -->
                <div class="campo input-icon">
                    <label for="usuario" id="label_usuario">Número de documento</label>
                    <input type="text" name="usuario" id="usuario" class="form-control" maxlength="13" value="{{ old('usuario') }}" required>
                    <i class="fas fa-id-card"></i>
                </div>
        </div>

        <!--
        <label for="nombres">Nombres</label>
        <input type="text" id="nombres" name="nombres" required oninput="formatInput(this)">

        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" name="apellidos" required oninput="formatInput(this)">

        <label for="contraseña">Contraseña</label>
        <input type="password" id="contraseña" name="contraseña" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}" title="Debe contener al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo especial." required>

        -->
        <div class="fila-doble">
        <div class="campo input-icon">
            <label for="nombres">Nombres</label>
            <input type="text" id="nombres" name="nombres" required oninput="formatInput(this)">
            <i class="fas fa-user"></i>
        </div>

        <div class="campo input-icon">
            <label for="apellidos">Apellidos</label>
            <input type="text" id="apellidos" name="apellidos" required oninput="formatInput(this)">
            <i class="fas fa-user-tag"></i>
        </div>
        </div>

        <div class="campo input-icon">
        <label for="contraseña">Contraseña</label>
        <input type="password" id="contraseña" name="contraseña" required>
        <i class="fas fa-eye" id="togglePassword" style="cursor: pointer;"></i>
        </div>

        
        <!-- Ventana flotante contraseña-->
        <div id="password-popup" class="password-popup">
            <h4>La contraseña debe contener:</h4>
            <p id="length" class="invalid">Mínimo 8 caracteres</p>
            <p id="uppercase" class="invalid">Al menos 1 mayúscula (A–Z)</p>
            <p id="number" class="invalid">Al menos 1 número (0–9)</p>
            <p id="special" class="invalid">Al menos 1 carácter especial (!@#$…)</p>
        </div>

        <style>
        /* Estilos del popup */
        .password-popup {
            display: none; /* Oculto por defecto */
            position: absolute;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 12px;
            padding: 12px 16px;
            width: 250px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            font-size: 12px;
            z-index: 100;
        }

        .password-popup h4 {
            margin: 0 0 8px;
            font-size: 15px;
            color: #333;
        }

        .password-popup p {
            margin: 4px 0;
        }

        .valid {
            color: green;
            font-weight: bold;
        }
        .invalid {
            color: blue;
        }
        </style>

        <script>
        // Referencias
        const passwordInput = document.getElementById("contraseña");
        const popup = document.getElementById("password-popup");

        // Mostrar popup al enfocar
        passwordInput.addEventListener("focus", function () {
            popup.style.display = "block";
            // Ubicar debajo del input
            const rect = passwordInput.getBoundingClientRect();
            popup.style.top = rect.bottom + window.scrollY + "px";
            popup.style.left = rect.left + window.scrollX + "px";
        });

        // Ocultar popup al salir
        passwordInput.addEventListener("blur", function () {
            setTimeout(()=> popup.style.display = "none", 200); // delay para evitar parpadeo
        });

        // Validar reglas
        passwordInput.addEventListener("input", function () {
            let value = this.value;
        
            document.getElementById("length").className = value.length >= 8 ? "valid" : "invalid";
            document.getElementById("uppercase").className = /[A-Z]/.test(value) ? "valid" : "invalid";
            document.getElementById("number").className = /[0-9]/.test(value) ? "valid" : "invalid";
            document.getElementById("special").className = /[!@#$%^&*(),.?":{}|<>]/.test(value) ? "valid" : "invalid";
        });
        </script>

        <div class="fila-doble">
            <div class="campo">
                <label for="genero">Género</label>
                <select id="genero" name="genero" class="select-estilizado">
                <option value="" disable selected hidden>Seleccione</option>
                <option value="MASCULINO">MASCULINO</option>
                <option value="FEMENINO">FEMENINO</option>
                <option value="OTRO">OTRO</option>
                </select>
            </div>

            <div class="campo">
                <label for="nivel_instruccion">Instrucción</label>
                <select id="nivel_instruccion" name="nivel_instruccion" class="select-estilizado">
                <option value="" disable selected hidden>Seleccione</option>
                <option value="Primaria">PRIMARIA</option>
                <option value="Secundaria">SECUNDARIA</option>
                <option value="Universitaria">UNIVERSITARIA</option>
                <option value="Postgrado">POSGRADO</option>
                <option value="Postgrado">OTRO</option>
                </select>
            </div>
        </div>

    <!-- 2. INFORMACIÓN DE CONTACTO -->
    <h6 style="margin-top: 30px; color: #1565c0;">4. Información de Contacto</h6>

    <div class="campo input-icon">
    <label for="email">Correo electrónico</label>
    <input type="email" id="email" name="email" required>
    <i class="fas fa-envelope"></i>
    </div>

    <div class="campo input-icon">
    <label for="correo_alternativo">Correo Alternativo</label>
    <input type="email" id="correo_alternativo" name="correo_alternativo">
    <i class="fas fa-envelope"></i>
    </div>

    <div class="fila-doble">
    <div class="campo input-icon">
        <label for="telefono">Teléfono Convencional</label>
        <input type="tel" id="telefono" name="telefono" maxlength="9" placeholder="022345678">
        <i class="fas fa-phone"></i>
    </div>

    <div class="campo input-icon">
        <label for="celular">Celular</label>
        <input type="tel" id="celular" name="celular" maxlength="10" placeholder="0999999999" required>
        <i class="fas fa-phone"></i>
    </div>
    </div>

    <div class="campo input-icon">
    <label for="direccion">Dirección</label>
    <input type="text" id="direccion" name="direccion" required oninput="formatInput(this)">
    <i class="fas fa-map-pin"></i>
    </div>

    <!-- 3. UBICACIÓN GEOGRÁFICA -->
    <!--
        <label for="id_provincia">Provincia</label>
        <select id="id_provincia" name="id_provincia" required>
            <option value="">Seleccione una provincia</option>
            @foreach($provincia as $item)
                <option value="{{ $item->id_provincia }}" {{ old('id_provincia') == $item->id_provincia ? 'selected' : '' }}>
                    {{ $item->nombre_provincia }}
                </option>
            @endforeach
        </select>

        <label for="id_canton">Cantón</label>
        <select id="id_canton" name="id_canton" required disabled>
            <option value="">Primero seleccione una provincia</option>
        </select>

        <label for="id_parroquia">Parroquia</label>
        <select id="id_parroquia" name="id_parroquia" required disabled>
            <option value="">Primero seleccione un cantón</option>
        </select>

    </div>
    -->

    <!-- 3. UBICACIÓN -->
    <h6 style="margin-top: 30px; color: #1565c0;">5. Lugar de Residencia</h6>

    <div class="fila-doble">
    <div class="campo input-icon">
        <label for="id_provincia">Provincia</label>
        <select id="id_provincia" name="id_provincia" class="select-estilizado" required>
        <option value="" disabled selected hidden>Provincia</option>
            @foreach($provincia as $item)
                <option value="{{ $item->id_provincia }}" {{ old('id_provincia') == $item->id_provincia ? 'selected' : '' }}>
                    {{ $item->nombre_provincia }}
                </option>
            @endforeach
        <!-- opciones PHP aquí -->
        </select>
        <i class="fas fa-map-marker-alt"></i>
    </div>

    <div class="campo input-icon">
        <label for="id_canton">Cantón</label>
        <select id="id_canton" name="id_canton" class="select-estilizado" required disabled>
        <option value="" disabled selected hidden>Cantón</option>
        </select>
        <i class="fas fa-map"></i>
    </div>

    <div class="campo input-icon">
        <label for="id_parroquia">Parroquia</label>
        <select id="id_parroquia" name="id_parroquia" class="select-estilizado" required disabled>
        <option value="" disabled selected hidden>Parroquia</option>
        </select>
        <i class="fas fa-street-view"></i>
    </div>
    </div>
</div>

    <!--
         FORMULARIO JURIDICA 
    <div id="campos_juridica" style="display: none;">
        <label for="usuario">RUC</label>
        <input type="text" name="usuario" class="form-control" maxlength="13" value="{{ old('usuario') }}">
        <label for="contraseña">Contraseña</label>
        <input type="password" id="contraseña" name="contraseña" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}" title="Debe contener al menos 8 caracteres, una mayúscula, una minúscula, un número y un símbolo especial." required>
        <label for="organizacion">Nombre organización</label>
        <input type="text" name="organizacion" class="form-control" value="{{ old('organizacion') }}" required oninput="formatInput(this)">

        <label for="comercial">Nombre Comercial</label>
        <input type="text" name="comercial" class="form-control" value="{{ old('comercial') }}" required oninput="formatInput(this)">

        <label for="representante">Cédula / RUC Representante</label>
        <input type="text" name="representante" class="form-control" value="{{ old('representante') }}" maxlength="13">

        <label for="nombre_representante">Nombres y Apellidos del representante</label>
        <input type="text" name="nombre_representante" class="form-control" value="{{ old('nombre_representante') }}" required oninput="formatInput(this)">

        <label for="cargo_representante">Cargo Representante</label>
        <input type="text" name="cargo_representante" class="form-control" value="{{ old('cargo_representante') }}" required oninput="formatInput(this)">

        <label for="telefono">Teléfono convencional</label>
        <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" maxlength="9" pattern="0[2-7][0-9]{7}" placeholder="022345678">

        <label for="celular">Celular</label>
        <input type="text" name="celular" class="form-control" value="{{ old('celular') }}" maxlength="10" pattern="09[0-9]{8}" placeholder="0999999999" required>

        <label for="email">Correo electrónico</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}">

        <label for="direccion">Dirección</label>
        <input type="text" name="direccion" class="form-control" value="{{ old('direccion') }}" required oninput="formatInput(this)">

        <label for="sector">Sector</label>
        <input type="text" name="sector" class="form-control" value="{{ old('sector') }}" required oninput="formatInput(this)">

        <label for="id_provincia">Provincia</label>
        <select id="id_provincia_juridica" name="id_provincia" class="form-control">
            <option value="">Seleccione una provincia</option>
            @foreach($provincia as $item)
                <option value="{{ $item->id_provincia }}">{{ $item->nombre_provincia }}</option>
            @endforeach
        </select>

        <label for="id_canton">Cantón</label>
        <select id="id_canton_juridica" name="id_canton" class="form-control" disabled>
            <option value="">Seleccione un cantón</option>
        </select>

        <label for="id_parroquia">Parroquia</label>
        <select id="id_parroquia_juridica" name="id_parroquia" class="form-control" disabled>
            <option value="">Seleccione una parroquia</option>
        </select>

        <label for="correo_alternativo">Correo Alternativo</label>
        <input type="email" id="correo_alternativo" name="correo_alternativo">
    </div>

    -->
    <!-- FORMULARIO JURIDICA -->
    <div id="campos_juridica" style="display: none;">

        <h6 style="margin-top: 30px; color: #1565c0;">2. Datos de la Organización</h6>

        <div class="campo input-icon">
            <label for="usuario_juridico">RUC</label>
            <input type="text" id="usuario_juridico" name="usuario" class="form-control" maxlength="13" value="{{ old('usuario') }}">
            <i class="fas fa-id-card"></i>
        </div>

        <div class="campo input-icon">
            <label for="contraseña_juridico">Contraseña</label>
            <input type="password" id="contraseña_juridico" name="contraseña" required>
            <i class="fas fa-eye" id="togglePasswordJuri" style="cursor: pointer;"></i>
        </div>

        <div class="fila-doble">
            <div class="campo input-icon">
                <label for="organizacion">Nombre organización</label>
                <input type="text" id="organizacion" name="organizacion" class="form-control" value="{{ old('organizacion') }}" required oninput="formatInput(this)">
                <i class="fas fa-building"></i>
            </div>

            <div class="campo input-icon">
                <label for="comercial">Nombre Comercial</label>
                <input type="text" id="comercial" name="comercial" class="form-control" value="{{ old('comercial') }}" required oninput="formatInput(this)">
                <i class="fas fa-store"></i>
            </div>
        </div>

        <h6 style="margin-top: 30px; color: #1565c0;">3. Representante Legal</h6>

        <div class="campo input-icon">
            <label for="representante">Cédula / RUC del Representante</label>
            <input type="text" id="representante" name="representante" class="form-control" value="{{ old('representante') }}" maxlength="13">
            <i class="fas fa-id-badge"></i>
        </div>

        <div class="fila-doble">
            <div class="campo input-icon">
                <label for="nombre_representante">Nombres y Apellidos del representante</label>
                <input type="text" id="nombre_representante" name="nombre_representante" class="form-control" value="{{ old('nombre_representante') }}" required oninput="formatInput(this)">
                <i class="fas fa-user"></i>
            </div>

            <div class="campo input-icon">
                <label for="cargo_representante">Cargo del Representante</label>
                <input type="text" id="cargo_representante" name="cargo_representante" class="form-control" value="{{ old('cargo_representante') }}" required oninput="formatInput(this)">
                <i class="fas fa-briefcase"></i>
            </div>
        </div>

        <h6 style="margin-top: 30px; color: #1565c0;">4. Información de Contacto</h6>

        <div class="fila-doble">
            <div class="campo input-icon">
                <label for="telefono_juridico">Teléfono convencional</label>
                <input type="text" id="telefono_juridico" name="telefono" class="form-control" value="{{ old('telefono') }}" maxlength="9" pattern="0[2-7][0-9]{7}" placeholder="022345678">
                <i class="fas fa-phone"></i>
            </div>

            <div class="campo input-icon">
                <label for="celular_juridico">Celular</label>
                <input type="text" id="celular_juridico" name="celular" class="form-control" value="{{ old('celular') }}" maxlength="10" pattern="09[0-9]{8}" placeholder="0999999999" required>
                <i class="fas fa-mobile-alt"></i>
            </div>
        </div>

        <div class="campo input-icon">
            <label for="email_juridico">Correo electrónico</label>
            <input type="email" id="email_juridico" name="email" class="form-control" value="{{ old('email') }}">
            <i class="fas fa-envelope"></i>
        </div>

        <div class="campo input-icon">
            <label for="correo_alternativo_juridico">Correo Alternativo</label>
            <input type="email" id="correo_alternativo_juridico" name="correo_alternativo" value="{{ old('correo_alternativo') }}">
            <i class="fas fa-envelope-open-text"></i>
        </div>

        <div class="campo input-icon">
            <label for="direccion_juridico">Dirección</label>
            <input type="text" id="direccion_juridico" name="direccion" class="form-control" value="{{ old('direccion') }}" required oninput="formatInput(this)">
            <i class="fas fa-map-pin"></i>
        </div>

        <div class="campo input-icon">
            <label for="sector">Sector</label>
            <input type="text" id="sector" name="sector" class="form-control" value="{{ old('sector') }}" required oninput="formatInput(this)">
            <i class="fas fa-location-arrow"></i>
        </div>

        <h6 style="margin-top: 30px; color: #1565c0;">5. Lugar de Residencia</h6>

        <div class="fila-doble">
            <div class="campo input-icon">
                <label for="id_provincia_juridica">Provincia</label>
                <select id="id_provincia_juridica" name="id_provincia" class="select-estilizado" required>
                    <option value="">Seleccione una provincia</option>
                    @foreach($provincia as $item)
                        <option value="{{ $item->id_provincia }}">{{ $item->nombre_provincia }}</option>
                    @endforeach
                </select>
                <i class="fas fa-map-marker-alt"></i>
            </div>

            <div class="campo input-icon">
                <label for="id_canton_juridica">Cantón</label>
                <select id="id_canton_juridica" name="id_canton" class="select-estilizado" disabled>
                    <option value="">Seleccione un cantón</option>
                </select>
                <i class="fas fa-map"></i>
            </div>

            <div class="campo input-icon">
                <label for="id_parroquia_juridica">Parroquia</label>
                <select id="id_parroquia_juridica" name="id_parroquia" class="select-estilizado" disabled>
                    <option value="">Seleccione una parroquia</option>
                </select>
                <i class="fas fa-street-view"></i>
            </div>
        </div>
    </div>

    
<!-- 4. TÉRMINOS Y CONDICIONES -->
 
<h6 style="margin-top: 30px; color: #1565c0;">6. Términos y Condiciones</h6>
<!--
<div class="terminos-condiciones d-flex align-items-center gap-3 flex-wrap mt-3">
  <a href="#" data-bs-toggle="modal" data-bs-target="#terminosModal" style="color: #007BFF; font-weight: bold; text-decoration: underline;">
    Leer condiciones de uso
  </a>
  
  <div class="form-check m-0" style="display: flex; justify-content: center; align-items: center; gap: 8px;">
    <input class="form-check-input" type="checkbox" id="consentimiento" name="consentimiento" value="1" required>
    
      <label class="form-check-label" for="consentimiento">
        Acepto los términos
      </label>
  </div>
  -->
  <!-- Contenedor general -->
<div class="mt-4">
  <!-- Enlace alineado a la izquierda -->
  <div class="text-start mb-2">
    <a href="#" data-bs-toggle="modal" data-bs-target="#terminosModal"
      style="color: #007BFF; font-weight: bold; text-decoration: underline;">
      Leer condiciones de uso
    </a>
  </div>

  <!-- Checkbox centrado -->
  <div class="d-flex justify-content-center">
    <div class="form-check d-flex align-items-center">
      <input class="form-check-input me-2" type="checkbox" id="consentimiento" name="consentimiento" value="1" required>
      <label class="form-check-label m-0" for="consentimiento" style="white-space: nowrap;">
        Acepto los términos
      </label>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="terminosModal" tabindex="-1" aria-labelledby="terminosModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="terminosModalLabel">Términos y Condiciones</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
        <div class="text-center mb-3">
          <img src="{{ asset('img/logo_login.jpg') }}" alt="Logo" class="img-fluid" style="max-height: 120px;">
        </div>

        <h5 class="text-center">Condiciones de uso</h5>

        <p>Las presentes condiciones generales de uso de la página web, regulan los términos y condiciones de acceso y uso de www.chimborazo.gob.ec, propiedad de GAD de la Provincia de Chimborazo, con domicilio en Riobamba-Ecuador, en adelante, «la Empresa», que el usuario del Portal deberá de leer y aceptar para usar todos los servicios e información que se facilitan desde el portal. El mero acceso y/o utilización del portal, de todos o parte de sus contenidos y/o servicios significa la plena aceptación de las presentes condiciones generales de uso.</p>

        <p><strong>Condiciones de uso</strong><br>
        Las presentes condiciones generales de uso del portal regulan el acceso y la utilización del portal, incluyendo los contenidos y los servicios puestos a disposición de los usuarios en y/o a través del portal, bien por el portal, bien por sus usuarios, bien por terceros. No obstante, el acceso y la utilización de ciertos contenidos y/o servicios pueden encontrarse sometido a determinadas condiciones específicas.</p>

        <p><strong>Modificaciones</strong><br>
        La empresa se reserva la facultad de modificar en cualquier momento las condiciones generales de uso del portal. En todo caso, se recomienda que consulte periódicamente los presentes términos de uso del portal, ya que pueden ser modificados.</p>

        <p><strong>Obligaciones del Usuario</strong><br>
        El usuario deberá respetar en todo momento los términos y condiciones establecidos en las presentes condiciones generales de uso del portal. De forma expresa el usuario manifiesta que utilizará el portal de forma diligente y asumiendo cualquier responsabilidad que pudiera derivarse del incumplimiento de las normas.</p>

        <p>Asimismo, el usuario no podrá utilizar el portal para transmitir, almacenar, divulgar, promover o distribuir datos o contenidos que sean portadores de virus o cualquier otro código informático, archivos o programas diseñados para interrumpir, destruir o perjudicar el funcionamiento de cualquier programa o equipo informático o de telecomunicaciones.</p>

        <p><strong>Responsabilidad del portal</strong><br>
        El usuario conoce y acepta que el portal no otorga ninguna garantía de cualquier naturaleza, ya sea expresa o implícita, sobre los datos, contenidos, información y servicios que se incorporan y ofrecen desde el Portal.</p>

        <p>Exceptuando los casos que la Ley imponga expresamente lo contrario, y exclusivamente con la medida y extensión en que lo imponga, el Portal no garantiza ni asume responsabilidad alguna respecto a los posibles daños y perjuicios causados por el uso y utilización de la información, datos y servicios del Portal.</p>

        <p>En todo caso, el Portal excluye cualquier responsabilidad por los daños y perjuicios que puedan deberse a la información y/o servicios prestados o suministrados por terceros diferentes de la Empresa. Toda responsabilidad será del tercero ya sea proveedor o colaborador.</p>

        <p><strong>Propiedad intelectual e industrial</strong><br>
        Todos los contenidos, marcas, logos, dibujos, documentación, programas informáticos o cualquier otro elemento susceptible de protección por la legislación de propiedad intelectual o industrial, que sean accesibles en el portal corresponden exclusivamente a la empresa o a sus legítimos titulares y quedan expresamente reservados todos los derechos sobre los mismos.</p>

        <p>Queda expresamente prohibida la creación de enlaces de hipertexto (links) a cualquier elemento integrante de las páginas web del Portal sin la autorización de la empresa, siempre que no sean a una página web del Portal que no requiera identificación o autenticación para su acceso, o el mismo esté restringido.</p>

        <p>En cualquier caso, el portal se reserva todos los derechos sobre los contenidos, información datos y servicios que ostente sobre los mismos. El portal no concede ninguna licencia o autorización de uso al usuario sobre sus contenidos, datos o servicios, distinta de la que expresamente se detalle en las presentes condiciones generales de uso del portal.</p>

        <p><strong>Legislación aplicable, jurisdicción competente y notificaciones</strong><br>
        Las presentes condiciones se rigen y se interpretan de acuerdo con las Leyes de Ecuador. Para cualquier reclamación serán competentes los juzgados y tribunales de Riobamba.</p>

        <p>Todas las notificaciones, requerimientos, peticiones y otras comunicaciones que el Usuario desee efectuar a la Empresa titular del Portal deberán realizarse por escrito y se entenderá que han sido correctamente realizadas cuando hayan sido recibidas en la siguiente dirección: <a href="mailto:prefectura@chimborazo.gob.ec">prefectura@chimborazo.gob.ec</a></p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- Botón de enviar -->
<button type="submit" class="btn btn-primary">Registrar</button>

<p style="margin-top: 20px; text-align: center;">
  ¿Ya tiene una cuenta?
  <a href="{{ url('/') }}" style="color: #007BFF; text-decoration: none;">Inicie sesión</a>
</p>

</form>
<!-- Modal de Éxito -->
<div class="modal fade" id="registroExitosoModal" tabindex="-1" aria-labelledby="registroExitosoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="registroExitosoModalLabel">¡Registro Exitoso!</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        Tu cuenta ha sido creada correctamente. Serás redirigido al inicio de sesión en unos segundos...
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-registro');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const data = new FormData(form);

        try {
            const response = await fetch("{{ route('personas.store') }}", {
                method: 'POST',
                body: data,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            });

            if (!response.ok) {
                const errorData = await response.json();
                console.error(errorData);
                alert("Error al registrar. Verifica los campos.");
                return;
            }

            // Si todo está bien
            const result = await response.json();

            // Mostrar modal de éxito
            const modal = new bootstrap.Modal(document.getElementById('registroExitosoModal'));
            modal.show();

            // Redirigir al login tras 4 segundos
            setTimeout(() => {
                window.location.href = "{{ url('/') }}";
            }, 3000);

        } catch (error) {
            console.error("Error en el registro:", error);
            alert("Error inesperado.");
        }
    });
});
</script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form'); // Asegúrate que sea el correcto
            form.addEventListener("submit", function(event) {
                const tipoPersona = document.getElementById("tipo_persona").value;
                const tipoDocumento = document.getElementById("tipo_documento")?.value;
                const usuario = (document.getElementById(tipoPersona === "juridica" ? "usuario_juridico" : "usuario")?.value || "");
                const representante = document.querySelector('[name="representante"]')?.value;

                // Validación general de campos numéricos
                const isNumeric = str => /^\d+$/.test(str);

                // NATURAL
                if (tipoPersona === "natural") {
                    if (tipoDocumento === "cedula") {
                        if (!usuario || usuario.length !== 10 || !isNumeric(usuario)) {
                            alert("La cédula debe tener 10 dígitos y ser numérica.");
                            event.preventDefault();
                            return;
                        }
                    } else if (tipoDocumento === "ruc") {
                        if (!usuario || usuario.length !== 13 || !isNumeric(usuario)) {
                            alert("El RUC debe tener 13 dígitos y ser numérico.");
                            event.preventDefault();
                            return;
                        }
                    }
                }

                // JURÍDICA
                if (tipoPersona === "juridica") {
                    if (!usuario || usuario.length !== 13 || !isNumeric(usuario)) {
                        alert("El RUC de la empresa debe tener 13 dígitos y ser numérico.");
                        event.preventDefault();
                        return;
                    }

                    if (!representante || (representante.length !== 10 && representante.length !== 13) || !isNumeric(representante)) {
                        alert("El documento del representante debe tener 10 o 13 dígitos y ser numérico.");
                        event.preventDefault();
                        return;
                    }
                }
            });
        });
    </script>

</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // --- Referencias ---
  const provinciaSelect = document.getElementById('id_provincia');
  const cantonSelect    = document.getElementById('id_canton');
  const parroquiaSelect = document.getElementById('id_parroquia');

  const provinciaSelectJuridica = document.getElementById('id_provincia_juridica');
  const cantonSelectJuridica    = document.getElementById('id_canton_juridica');
  const parroquiaSelectJuridica = document.getElementById('id_parroquia_juridica');

  // --- Helpers ---
  function resetSelect(select, message) {
    select.disabled = true;
    select.innerHTML = `<option value="">${message}</option>`;
  }
  function populateSelect(select, data, valueField, textField) {
    const placeholder = select.name.replace('id_', '').replace('_', ' ');
    select.innerHTML = `<option value="">Seleccione un ${placeholder}</option>`;
    data.forEach(item => {
      const option = document.createElement('option');
      option.value = item[valueField];
      option.textContent = item[textField];
      select.appendChild(option);
    });
    select.disabled = data.length === 0;
  }
  async function getJSON(url) {
    const r = await fetch(url, { headers: { 'Accept': 'application/json' }});
    if (!r.ok) throw new Error(`HTTP ${r.status}`);
    return r.json();
  }

  // --- URLs (por nombre de ruta) ---
  const R = {
    provincias: "{{ route('api.personas.provincias') }}",
    cantones:   idProv   => "{{ route('api.personas.cantones',   ['provincia' => '___']) }}".replace('___', encodeURIComponent(idProv)),
    parroquias: idCanton => "{{ route('api.personas.parroquias', ['canton'    => '___']) }}".replace('___', encodeURIComponent(idCanton)),
  };

  // --- Natural ---
  provinciaSelect?.addEventListener('change', async function () {
    const provinciaId = this.value;
    resetSelect(cantonSelect, 'Cargando...');
    resetSelect(parroquiaSelect, 'Primero seleccione un cantón');
    if (!provinciaId) return;

    try {
      const data = await getJSON(R.cantones(provinciaId));
      populateSelect(cantonSelect, data, 'id_canton', 'nombre_canton');
      cantonSelect.disabled = false;
    } catch (e) {
      resetSelect(cantonSelect, 'No se pudo cargar');
    }
  });

  cantonSelect?.addEventListener('change', async function () {
    const cantonId = this.value;
    resetSelect(parroquiaSelect, 'Cargando...');
    if (!cantonId) return;

    try {
      const data = await getJSON(R.parroquias(cantonId));
      populateSelect(parroquiaSelect, data, 'id_parroquia', 'nombre_parroquia');
      parroquiaSelect.disabled = false;
    } catch (e) {
      resetSelect(parroquiaSelect, 'No se pudo cargar');
    }
  });

  // --- Jurídica ---
  provinciaSelectJuridica?.addEventListener('change', async function () {
    const provinciaId = this.value;
    resetSelect(cantonSelectJuridica, 'Cargando...');
    resetSelect(parroquiaSelectJuridica, 'Primero seleccione un cantón');
    if (!provinciaId) return;

    try {
      const data = await getJSON(R.cantones(provinciaId));
      populateSelect(cantonSelectJuridica, data, 'id_canton', 'nombre_canton');
      cantonSelectJuridica.disabled = false;
    } catch (e) {
      resetSelect(cantonSelectJuridica, 'No se pudo cargar');
    }
  });

  cantonSelectJuridica?.addEventListener('change', async function () {
    const cantonId = this.value;
    resetSelect(parroquiaSelectJuridica, 'Cargando...');
    if (!cantonId) return;

    try {
      const data = await getJSON(R.parroquias(cantonId));
      populateSelect(parroquiaSelectJuridica, data, 'id_parroquia', 'nombre_parroquia');
      parroquiaSelectJuridica.disabled = false;
    } catch (e) {
      resetSelect(parroquiaSelectJuridica, 'No se pudo cargar');
    }
  });
});
</script>


<script>
    function formatInput(input) {
        const accents = {
            'á': 'a', 'é': 'e', 'í': 'i', 'ó': 'o', 'ú': 'u',
            'Á': 'A', 'É': 'E', 'Í': 'I', 'Ó': 'O', 'Ú': 'U',
            'ä': 'a', 'ë': 'e', 'ï': 'i', 'ö': 'o', 'ü': 'u',
            'Ä': 'A', 'Ë': 'E', 'Ï': 'I', 'Ö': 'O', 'Ü': 'U'
        };

        let value = input.value;
        value = value.replace(/[^a-zA-ZñÑ\s]/g, '');
        value = value.split('').map(char => accents[char] || char).join('');
        input.value = value.toUpperCase();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const tipoPersona = document.getElementById('tipo_persona');
        const camposNatural = document.getElementById('campos_natural');
        const camposJuridica = document.getElementById('campos_juridica');
        const tipoDocumento = document.getElementById('tipo_documento');

        function toggleCampos() {
            if (tipoPersona.value === 'juridica') {
                camposNatural.style.display = 'none';
                camposJuridica.style.display = 'block';
                if (tipoDocumento) tipoDocumento.closest('.form-group').style.display = 'none';
            } else {
                camposNatural.style.display = 'block';
                camposJuridica.style.display = 'none';
                if (tipoDocumento) tipoDocumento.closest('.form-group').style.display = 'block';
            }
        }

        tipoPersona.addEventListener('change', toggleCampos);
        toggleCampos();
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tipoDocumentoSelect = document.getElementById('tipo_documento');
        const usuarioInput = document.getElementById('usuario');
        const labelUsuario = document.getElementById('label_usuario');

        tipoDocumentoSelect.addEventListener('change', function () {
            const tipo = this.value;
            if (tipo === 'cedula') {
                usuarioInput.maxLength = 10;
                labelUsuario.innerText = 'Cédula';
            } else if (tipo === 'ruc') {
                usuarioInput.maxLength = 13;
                labelUsuario.innerText = 'RUC';
            } else {
                usuarioInput.maxLength = 13;
                labelUsuario.innerText = 'Número de documento';
            }
        });
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tipoPersona = document.getElementById('tipo_persona');
    const camposNatural = document.querySelectorAll('#campos_natural [name]');
    const camposJuridica = document.querySelectorAll('#campos_juridica [name]');

    function toggleCampos() {
        const isNatural = tipoPersona.value === 'natural';

        document.getElementById('campos_natural').style.display = isNatural ? 'block' : 'none';
        document.getElementById('campos_juridica').style.display = isNatural ? 'none' : 'block';

        // Desactivar y quitar required de todos los campos
        camposNatural.forEach(field => {
            field.disabled = !isNatural;
            field.required = isNatural;
        });

        camposJuridica.forEach(field => {
            field.disabled = isNatural;
            field.required = !isNatural;
        });
    }

    tipoPersona.addEventListener('change', toggleCampos);
    toggleCampos(); // Llamada inicial
});
</script>

@endsection