@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('styles/coordenadas.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="{{ asset('js/coordenadas.js') }}"></script>


<div class="main-container">
    {{-- Menú lateral --}}
    @include('menu_general.menu_general')

    <div class="coordenadas-wrapper">
        <div class="soca-container">
            <div class="header-with-button">
                <h1>Sistema de Control y Calidad Ambiental</h1>
            </div>
        
            <div class="filtros-container">
                <h3><i class="fas fa-file-invoice"></i> Tipo</h3>
                <div class="filter-group">
                    <label class="radio-option">
                        <input type="radio" name="tipo" value="simplificado" checked>
                        <span>Simplificado</span>
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="tipo" value="completo">
                        <span>Completo</span>
                    </label>
                </div>
        
                <div class="filter-group">
                    <h3><i class="fas fa-file-alt"></i> Formato</h3>
                    <label class="radio-option">
                        <input type="radio" name="formato" value="excel" checked>
                        <i class="fas fa-file-excel"></i> EXCEL
                    </label>
                </div>
        
                <button class="btn-generar">
                    <i class="fas fa-file-export"></i> Reporte
                </button>
            </div>
        </div>
    </div>
</div>
