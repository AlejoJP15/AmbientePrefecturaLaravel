@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('styles/listau.css') }}">

<div class="usuarios-container">
    <h2>Listado de Usuarios</h2>

    <table class="tabla-usuarios">
        <thead>
            <tr>
                <th>Usuario</th>
                <th>Nombres y Apellidos</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acción</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>jperez</td>
                <td>Juan Pérez</td>
                <td>Administrador</td>
                <td>Activo</td>
                <td><a href="#" class="btn-editar">Editar</a></td>
                <td><a href="#" class="btn-eliminar">Eliminar</a></td>
            </tr>
            <tr>
                <td>mlopez</td>
                <td>María López</td>
                <td>Usuario</td>
                <td>Inactivo</td>
                <td><a href="#" class="btn-editar">Editar</a></td>
                <td><a href="#" class="btn-eliminar">Eliminar</a></td>
            </tr>
            <!-- Puedes añadir más registros -->
        </tbody>
    </table>

    <div class="volver">
        <a href="{{ route('admin.dashboard') }}" class="btn-volver">Volver al Menú</a>
    </div>
</div>
@endsection