@extends('layouts.app')

@section('content')
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
            <h3>SOACH - Sistema de Obligaciones Ambientales - HGADPCH </h1>
        </div>

        <div class="header-right">
            <a href="{{ route('usuario.dashboard') }}" class="home-button" title="Volver al menú principal">
                <i class="fas fa-home"></i>
                <span>Inicio</span>
            </a>
        </div>

    </header>

    <!-- MENÚ HORIZONTAL -->
    <nav class="menu-horizontal">
        <ul class="mh">
            <li>
                <a href="{{ route('usuario.inbox') }}">
                    <i class="fas fa-tasks"></i> <span>Bandeja de entrada</span>
                    @if (($unreadInboxCount ?? 0) > 0)
                        <span class="badge-notificacion">{{ $unreadInboxCount }}</span>
                    @endif
                </a>
            </li>

            <li class="submenu">
                <a href="#" class="submenu-toggle">
                    <i class="fas fa-cogs"></i>
                    <span>Proyectos</span>
                    <span class="flecha"><i class="fas fa-angle-down"></i></span>
                </a>
                <ul class="submenu-items">
                    <li><a href="{{ route('usuario.proyectos.create') }}"><i class="fas fa-file"></i> Crear Proyectos</a>
                    </li>
                    <li> <a href="{{ route('usuario.proyectos.index') }}"><i class="fas fa-chart-bar"></i> Listado</a></li>
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
                    e
                .stopPropagation(); // <- clave: evita que el click llegue al document y cierre inmediatamente

                    const parent = this.parentElement;

                    // Cerrar otros submenús abiertos
                    document.querySelectorAll('.submenu.active').forEach(s => {
                        if (s !== parent) s.classList.remove('active');
                    });

                    // Alternar el submenú actual
                    parent.classList.toggle('active');
                });
            });

            // Cerrar submenús al hacer clic fuera del menú
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.menu-horizontal')) {
                    document.querySelectorAll('.submenu.active').forEach(s => s.classList.remove('active'));
                }
            });

            // Cerrar con ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.submenu.active').forEach(s => s.classList.remove('active'));
                }
            });
        });
    </script>
