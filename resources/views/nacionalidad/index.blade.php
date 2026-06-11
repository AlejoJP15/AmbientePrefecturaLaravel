@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('styles/nacionalidad.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="main-container">
    @include('menu_general.menu_general')
    
    <div class="nacionalidades-wrapper">
        <div class="soca-container">
            <div class="header-with-button">
                <h1>Gestión de Nacionalidades</h1>
            </div>

            <hr class="soca-divider">

            <div class="soca-content">
                <div class="soca-form">
                    <h2>Crear nuevo</h2>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <input type="text" id="descripcion" class="form-control" placeholder="Ecuatoriana">
                    </div>
                    <button class="btn btn-primary">Guardar</button>
                </div>

                <div class="soca-list">
                    <h2>Listado De Nacionalidades</h2>
                    <table class="soca-table">
                        <thead>
                            <tr>
                                <th>DESCRIPCIÓN</th>
                                <th>ACCIÓN</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Ecuatoriana</td>
                                <td><a href="#">Editar</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
