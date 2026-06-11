<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="{{ asset('styles/menu.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&family=Open+Sans&display=swap" rel="stylesheet">

  <title>SCCA</title>
</head>
<body>

  <div class="main-container">

    <!-- MENÚ LATERAL -->
    <div class="sidebar">
      <div class="logo">
        <img src="{{ asset('img/logo_login.jpg') }}" alt="Logo">
      </div>
      <br><br>
      <center><h1>MENÚ</h1></center>
      <hr style="border: 2px solid #ffffffff; width: 100%; margin: 10px auto;">
      <ul class="menu-vertical">
               <!-- Submenú Proyectos-->
         <li class='submenu'>
          <a href="#"><i class="fas fa-project-diagram"></i> <span>Proyectos</span><span class="flecha"><i class="fas fa-angle-down"></i></span></a>
          <ul class="submenu-items">
            <li><a href="{{ route('usuario.proyectos.create') }}"><i class="fas fa-plus"></i> Crear Proyecto</a></li>
            <li><a href="{{ route('admin.proyectos.index.todos') }}"><i class="fas fa-folder-plus"></i> Listado</a></li>
            <li><a href="#"><i class="fas fa-diagram-project"></i> Listado General </a></li>
          </ul>
        </li>
      <!-- Enlaces Obligaciones Director-->
         <li class='submenu'>
          <a href="#"><i class="fas fa-tasks"></i> <span>Obligaciones - Director</span><span class="flecha"><i class="fas fa-angle-down"></i></span></a>
          <ul class="submenu-items">
            <li><a href="{{ route('obligacionesD.listado') }}"><i class="fas fa-folder-plus"></i></i> Listado Pendiente </a></li>
            <li><a href="{{ route('obligacionesD.listadoG') }}"><i class="fas fa-diagram-project"></i> Listado General </a></li>
          </ul>
        </li>

      <!-- Enlaces Obligaciones Coordinador-->
         <li class='submenu'>
          <a href="#"><i class="fas fa-tasks"></i> <span>Obligaciones - Coordinador</span><span class="flecha"><i class="fas fa-angle-down"></i></span></a>
          <ul class="submenu-items">
            <li><a href="{{ route('obligacionC.listado') }}"><i class="fas fa-folder-plus"></i></i> Listado </a></li>
            <li><a href="{{ route('obligacionC.listadoPendiente') }}"><i class="fas fa-folder-plus"></i></i> Listado Pendiente </a></li>
            <li><a href="{{ route('obligacionC.listadoAsignados') }}"><i class="fas fa-diagram-project"></i> Listado Asigandos </a></li>
          </ul>
        </li>
        <!-- Enlaces Obligaciones Tecnico -->
         <li class='submenu'>
          <a href="#"><i class="fas fa-tasks"></i> <span>Obligaciones</span><span class="flecha"><i class="fas fa-angle-down"></i></span></a>
          <ul class="submenu-items">
            
            <li><a href="{{ route('tecnico.obligaciones.listado') }}"><i class="fas fa-diagram-project"></i> Listado </a></li>
          </ul>
        </li>

        <!-- Submenú Reportes -->
        <li class='submenu'>
          <a href="#"><i class="fas fa-cogs"></i> <span>Reportes</span><span class="flecha"><i class="fas fa-angle-down"></i></span></a>
          <ul class="submenu-items">
            <li><a href="{{ route('reportes.documentos') }}"><i class="fas fa-file"></i> Documentos</a></li>
            <li><a href="{{ route('reportes.coordenadas') }}"><i class="fas fa-chart-bar"></i> Coordenadas</a></li>
          </ul>
        </li>
        <!-- Submenú Administracion -->
        <li class="submenu">
          <a href="#"><i class="fas fa-cogs"></i> <span>Administración</span><span class="flecha"><i class="fas fa-angle-down"></i></span></a>
          <ul class="submenu-items">
            <li><a href="{{ route('personas.index') }}"><i class="fas fa-users"></i> Usuarios</a></li>
            <li><a href="{{ route('obligacion.index') }}"><i class="fas fa-tasks"></i> Tipo de Obligación</a></li>
            <li><a href="{{route('formatos.index')}}"><i class="fas fa-user-shield"></i> Formatos</a></li>
            <li><a href="{{route('admin.proyectos.index')}}"><i class="fas fa-user-shield"></i> Actualizar Proyecto</a></li>
          </ul>
        </li>

        <!-- Submenú Mapa -->
          <li class="submenu">
            <a href="#" class="submenu-toggle">
              <i class="fas fa-map"></i> <span>Mapa</span>
              <span class="flecha"><i class="fas fa-angle-down"></i></span>
            </a>
            <ul class="submenu-items">
              <li>
                <a href="{{ route('tecnico.proyectos.index') }}">
                  <i class="fas fa-project-diagram"></i> Proyectos
                </a>
              </li>
              <li>
                
              </li>
            </ul>
          </li>

           <!-- Submenú Catastro -->
        <li class='submenu'>
          <a href="#"><i class="fas fa-chart-bar"></i> <span>Catastro</span><span class="flecha"><i class="fas fa-angle-down"></i></span></a>
          <ul class="submenu-items">
            <li><a href="{{ route('catastro.index') }}"><i class="fas fa-file"></i> Estudios Impacto Ambiental</a></li>
          </ul>
        </li>
        <li class='submenu'>
          <a href="#"><i class="fas fa-archive"></i> <span>Archivo</span><span class="flecha"><i class="fas fa-angle-down"></i></span></a>
          <ul class="submenu-items">
            <li><a href="{{ route('archivo.index') }}"><i class="fas fa-file"></i> Listado de Trámites</a></li>
          </ul>
        </li>
      </ul>
    </div>

    <!-- CONTENIDO Y PERFIL -->
    <div class="contenido">

      <div class="perfil">
        <button type="button" class="perfil-btn">Administrador ▼</button>
        <div class="menu-perfil" id="menuPerfil">
          <a href="{{ route('persona.perfil') }}">Perfil</a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-link">Cerrar sesión</button>
          </form>
        </div>
      </div>

      <center>
        <div class="bienvenida">
          <h1>BIENVENIDOS</h1>
          <h1>SOACH <BR>Sistema de Obligaciones Ambientales</BR> </h1>
          <h1>HGADPCH</h1>
        </div>
      </center>

    </div>
  </div>

  <!-- JS -->
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // Submenús
      document.querySelectorAll(".submenu > a").forEach(function (el) {
        el.addEventListener("click", function (e) {
          e.preventDefault();
          this.parentElement.classList.toggle("active");
        });
      });

      // Menú perfil
      const perfilBtn = document.querySelector(".perfil-btn");
      const menuPerfil = document.getElementById("menuPerfil");

      perfilBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        const isVisible = menuPerfil.style.display === "block";
        menuPerfil.style.display = isVisible ? "none" : "block";
      });

      document.addEventListener("click", function () {
        menuPerfil.style.display = "none";
      });

      menuPerfil.addEventListener("click", function (e) {
        e.stopPropagation(); // no cerrar si clic en menú
      });
    });
  </script>

</body>
</html>
