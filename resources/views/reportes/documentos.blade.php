@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('styles/documentos.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


<div class="main-container">
    {{-- Menú lateral --}}
    @include('menu_general.menu_general')
    
    <div class="reportes-wrapper">
        <div class="soca-container">

            <!-- Contenido principal -->
            <div class="report-section">
                <h2 class="section-title">Parámetros De Filtración</h2>

                <div class="filter-card">
                    <div class="filter-group">
                        <h3><i class="fas fa-file-alt"></i> Formato</h3>
                        <div class="format-options">
                            <label class="format-option excel">
                                <input type="radio" name="formato" value="excel" checked>
                                <div class="format-content">
                                    <i class="fas fa-file-excel"></i> EXCEL
                                </div>
                            </label>
                            <label class="format-option word">
                                <input type="radio" name="formato" value="word">
                                <div class="format-content">
                                    <i class="fas fa-file-word"></i> WORD
                                </div>
                            </label>
                            <label class="format-option html">
                                <input type="radio" name="formato" value="html">
                                <div class="format-content">
                                    <i class="fas fa-file-code"></i> HTML
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="filter-group">
                        <h3><i class="fas fa-clipboard-check"></i> Estado de documento</h3>
                        <select class="state-select">
                            <option value="todos">Todos</option>
                            <option value="iniciado">Iniciado</option>
                            <option value="observado">Observado</option>
                            <option value="tramite">En trámite</option>
                            <option value="aprobado">Aprobado</option>
                            <option value="favorable">Pronunciamiento favorable</option>
                            <option value="archivado">Archivado</option>
                        </select>
                    </div>
                </div>

                <button class="generate-btn">
                    <i class="fas fa-download"></i> Generar Reporte
                </button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/docs.js') }}"></script>
