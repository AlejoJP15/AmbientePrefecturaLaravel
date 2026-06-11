<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="{{ asset('styles/menu.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&family=Open+Sans&display=swap"
        rel="stylesheet">
    <title>SCCA — Coordinador</title>
</head>

<body>
    <div class="main-container">

        <!-- MENÚ LATERAL (Coordinador) -->
        <div class="sidebar">
            <div class="logo">
                <img src="{{ asset('img/logo_login.jpg') }}" alt="Logo">
            </div>
            <br><br>
            <center>
                <h1>MENÚ</h1>
            </center>
            <hr style="border: 2px solid #ffffffff; width: 100%; margin: 10px auto;">

            <ul class="menu-vertical">

                <!-- 1) OBLIGACIONES -> Listado -->
                <li class="submenu">
                    <a href="#">
                        <i class="fa-solid fa-file-circle-check"></i>
                        <span>Obligaciones</span>
                        <span class="flecha"><i class="fas fa-angle-down"></i></span>
                    </a>
                    <ul class="submenu-items">
                        <li>
                            <a href="{{ route('coordinador.obligaciones.listadoGeneral') }}">
                                <i class="fa-solid fa-list-ul"></i> Listado general
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coordinador.obligaciones.listadoAsignado') }}">
                                <i class="fa-solid fa-list-ul"></i> Listado asignado
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('coordinador.obligaciones.listadoPendiente') }}">
                                <i class="fa-solid fa-list-ul"></i> Listado pendiente
                            </a>
                        </li>
                    </ul>
                </li>


                <!-- 1) PROYECTOS APROBADOS-> Listado -->
                <li class="submenu">
                <a href="#">
                    <i class="fa-solid fa-file-circle-check"></i>
                    <span>Proyectos aprobados por el técnico</span>
                    <span class="flecha"><i class="fas fa-angle-down"></i></span>
                </a>
                <ul class="submenu-items">
                    <li>
                    <a href="{{ route('coordinador.obligaciones.aprobados') }}">
                        <i class="fa-solid fa-list-ul"></i> Listado
                    </a>
                    </li>
                </ul>
                </li>



            </ul>
        </div>

        <!-- CONTENIDO Y PERFIL -->
        <div class="contenido">
            <div class="perfil">
                <button type="button" class="perfil-btn">Coordinador ▼</button>
                <div class="menu-perfil" id="menuPerfil">
                    <a href="{{ route('persona.perfil') }}">Perfil</a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-link">Cerrar sesión</button>
                    </form>
                </div>
            </div>

            {{-- Portada opcional --}}
            <center>
                <div class="bienvenida">
                    <h1>BIENVENIDOS</h1>
                    <h1>SOACH <br>Sistema de Obligaciones Ambientales</br></h1>
                    <h1>HGADPCH — Coordinador</h1>
                </div>
            </center>

            @yield('content')
        </div>
    </div>

    <script>
        (function removeSwal() {
            try {
                Swal.close();
            } catch (_) {}
            try {
                document.querySelectorAll('.swal2-container').forEach(el => el.remove());
            } catch (_) {}
        })();

        document.querySelectorAll('.submenu > a').forEach(function(trigger) {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.parentElement;
                parent.classList.toggle('activo');
                const sub = parent.querySelector('.submenu-items');
                if (sub) sub.style.display = (sub.style.display === 'block') ? 'none' : 'block';
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const perfilBtn = document.querySelector('.perfil-btn');
            const menuPerfil = document.getElementById('menuPerfil');
            perfilBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                menuPerfil.style.display = (menuPerfil.style.display === 'block') ? 'none' : 'block';
            });
            document.addEventListener('click', function() {
                menuPerfil.style.display = 'none';
            });
            menuPerfil.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    </script>
</body>

</html>
