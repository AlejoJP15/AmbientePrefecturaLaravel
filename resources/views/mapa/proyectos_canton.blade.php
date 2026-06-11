@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('styles/archivo.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="main-container">
    @include('menu_general.menu_general')

    <div class="archivo-wrapper">
        <div class="soca-container">
            <div class="header-with-button">
                <h1>Listado de Obligaciones Registradas Para El Cantón TODOS</h1>
            </div>
            <div class="summary-info">

                <!-- Selector de registros (centro) -->
                <div class="records-selector">
                    <i class="fas fa-list-ol"></i>
                    <span>Mostrar</span>
                    <select class="records-select">
                        <option>10</option>
                        <option>20</option>
                        <option>25</option>
                    </select>
                    <span>registros</span>
                </div>

                <!-- Filtros para Provincia y Cantón -->
                <div class="filters-container">
                    <select class="filter-select" name="provincia" id="provincia">
                        <option value="" selected disabled>Seleccione Provincia</option>
                        <option value="prov1">Provincia 1</option>
                        <option value="prov2">Provincia 2</option>
                        <option value="prov3">Provincia 3</option>
                    </select>

                    <select class="filter-select" name="canton" id="canton">
                        <option value="" selected disabled>Seleccione Cantón</option>
                        <option value="canton1">Cantón 1</option>
                        <option value="canton2">Cantón 2</option>
                        <option value="canton3">Cantón 3</option>
                    </select>
                </div>

                <!-- Barra de búsqueda (derecha) -->
                <div class="right-actions">
                    <div class="search-box">
                        <input type="text" placeholder="Buscar...">
                        <button class="search-button">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

            </div>


            <div class="tramites-table-container">
                <table class="tramites-table">
                    <thead>
                        <tr>
                            <th>Código Proyecto</th>
                            <th>Nombre del Proyecto</th>
                            <th>Nombre del Proyecto</th>
                            <th>Proponente</th>
                            <th>Actividad</th>
                            <th>Cantón</th>
                            <th>Opción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="empty-row">
                            <td colspan="10">No hay trámites archivados disponibles</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

        <script src="{{ asset('js/archivo.js') }}"></script>
@endsection