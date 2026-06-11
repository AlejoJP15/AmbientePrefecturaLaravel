@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('styles/catastro.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="{{ asset('js/catastro.js') }}"></script>

<div class="main-container">
    @include('menu_general.menu_general')

    <div class="wrapper">
        <div class="header-with-button">
            <h1>Matriz de Estudios de Impacto Ambiental</h1>
        </div>

        <hr class="soca-divider">

        <div class="file-upload-minimal">
            <form id="uploadForm">
                @csrf
                <div class="form-group">
                    <label for="fileInput" class="btn-file-input">
                        <i class="fas fa-paperclip"></i> Seleccionar archivo
                        <input type="file" id="fileInput" class="hidden-file-input" accept="*.*">
                    </label>
                    <span id="fileName" class="file-name">Ningún archivo seleccionado</span>
                </div>
                <button type="submit" class="btn-process">
                    <i class="fas fa-upload"></i> Cargar Datos
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
