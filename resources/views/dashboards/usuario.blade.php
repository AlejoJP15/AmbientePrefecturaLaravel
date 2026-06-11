@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('styles/menu.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&family=Open+Sans&display=swap" rel="stylesheet">
@endpush

@section('content')
    <div class="main-container">

        <!-- MENÚ LATERAL -->
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
                <li>
                    <a href="{{ route('usuario.inbox') }}">
                        <i class="fas fa-inbox"></i>
                        <span>Bandeja de entrada</span>
                        @if (($unreadInboxCount ?? 0) > 0)
                            <span class="badge-notificacion">{{ $unreadInboxCount }}</span>
                        @endif
                    </a>
                </li>

                <li class="submenu">
                    <a href="#">
                        <i class="fas fa-project-diagram"></i>
                        <span>Proyectos</span>
                        <span class="flecha"><i class="fas fa-angle-down"></i></span>
                    </a>
                    <ul class="submenu-items">
                        <li><a href="{{ route('usuario.proyectos.create') }}"><i class="fas fa-plus"></i> Crear proyecto</a>
                        </li>
                        <li><a href="{{ route('usuario.proyectos.index') }}"><i class="fas fa-folder-open"></i> Listado</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <!-- CONTENIDO Y PERFIL -->
        <div class="contenido">
            <div class="perfil">
                <button type="button" class="perfil-btn">Usuario externo ▼</button>
                <div class="menu-perfil" id="menuPerfil">
                    <a href="{{ route('persona.perfil') }}">Perfil</a>
                    <a href="#">Configuración</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="logout-link">Cerrar sesión</button>
                    </form>
                </div>
            </div>

            <center>
                <div class="bienvenida">
                    <h1>BIENVENIDOS</h1>
                    <h1>SOACH <br>Sistema de Obligaciones Ambientales</br></h1>
                    <h1>HGADPCH</h1>
                </div>
            </center>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Toggle de submenús
        document.querySelectorAll('.submenu > a').forEach(function(trigger) {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.parentElement;
                parent.classList.toggle('activo');
                const sub = parent.querySelector('.submenu-items');
                if (sub) sub.style.display = (sub.style.display === 'block') ? 'none' : 'block';
            });
        });

        // Menú de perfil
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
@endpush
