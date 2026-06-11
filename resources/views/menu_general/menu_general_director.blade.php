{{-- resources/views/menu_general/menu_general_director.blade.php --}}
<link rel="stylesheet" href="{{ asset('styles/listau.css') }}">
<link rel="stylesheet" href="{{ asset('styles/menu_general.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Montserrat:wght@600&display=swap"
    rel="stylesheet">

<!-- HEADER -->
<header class="header">
    <div class="logo-header">
        <img src="{{ asset('img/logo1.jpg') }}" alt="Logo" />
    </div>

    <div class="header-title">
        <h3>SOACH - Sistema de Obligaciones Ambientales - HGADPCH</h3>
    </div>

    <div class="header-right">
        <a href="{{ route('director.dashboard') }}" class="home-button" title="Volver al menú principal">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
    </div>

</header>

<!-- MENÚ HORIZONTAL (Director, sin mapas) -->
<nav class="menu-horizontal">
    <ul class="mh">
        <li class="submenu">
            <a href="#" class="submenu-toggle">
                <i class="fa-solid fa-file-circle-check"></i>
                <span>Obligaciones</span>
                <span class="flecha"><i class="fas fa-angle-down"></i></span>
            </a>
            <ul class="submenu-items">
                <li>
                    <a href="{{ route('director.obligaciones.listadoGeneral') }}">
                        <i class="fa-solid fa-list-ul"></i> Listado general
                    </a>
                </li>
                <li>
                    <a href="{{ route('director.obligaciones.listadoAsignado') }}">
                        <i class="fa-solid fa-list-ul"></i> Listado asignado
                    </a>
                </li>
                <li>
                    <a href="{{ route('director.obligaciones.listadoPendiente') }}">
                        <i class="fa-solid fa-list-ul"></i> Listado pendiente
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggles = document.querySelectorAll('.submenu-toggle');
        toggles.forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const parent = this.closest('.submenu');
                document.querySelectorAll('.submenu.active').forEach(s => {
                    if (s !== parent) s.classList.remove('active');
                });
                parent.classList.toggle('active');
            });
        });
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.menu-horizontal')) {
                document.querySelectorAll('.submenu.active').forEach(s => s.classList.remove('active'));
            }
        });
    });
</script>
