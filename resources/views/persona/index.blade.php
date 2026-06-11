@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('styles/archivo.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="main-container">
    @include('menu_general.menu_general')

    <div class="archivo-wrapper">
        <div class="soca-container">
            <div class="header-with-button">
                <h1>Listado de Usuarios</h1>
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

            <h2>Listado Usuarios</h2>

            <div class="tramites-table-container">
                <table class="tramites-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Nombres y Apellidos</th>
                            <th>Rol</th>
                            <th>Email</th>
                            <th>Estado</th>
                            <th>Editar</th>
                            <!-- <th>Eliminar</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($personas as $p)
                        <tr>
                            <td>{{ $p->id_persona }}</td>
                            <td>{{ $p->usuario }}</td>
                            <td>{{ optional($p->usuarioExterno)->nombre_representante ?? optional($p->usuarioExterno)->organizacion ?? trim(($p->nombres ?? '').' '.($p->apellidos ?? '')) }}</td>
                            <td>{{ $p->perfil->descripcion ?? '-' }}</td>
                            <td>{{ $p->email }}</td>
                            <td>{{ $p->estado->descripcion ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.personas.edit', $p) }}" class="btn-editar" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                            <!-- <td>
                                <form action="{{ route('personas.destroy', $p) }}" method="POST" class="form-eliminar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-eliminar" title="Eliminar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td> -->
                        </tr>
                        @empty
                        <tr class="empty-row">
                            <td colspan="8">No hay usuarios registrados disponibles</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script src="{{ asset('js/archivo.js') }}"></script>
@endsection
