@extends('layouts.app')

@section('title', 'Proyectos — Mapas')

@section('content')
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('styles/archivo.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    {{-- DataTables CSS con Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    {{-- Menú lateral --}}
    @php
        $u = Auth::user();
        $isAdmin = false;
        if ($u) {
            if (method_exists($u, 'hasRole')) {
                $isAdmin = $u->hasRole('Administrador') || $u->hasRole('Admin');
            } else {
                $desc = trim((string) optional($u->perfil)->descripcion);
                $isAdmin = strcasecmp($desc, 'Administrador') === 0 || strcasecmp($desc, 'Admin') === 0;
            }
        }
    @endphp

    @if ($isAdmin)
        @include('menu_general.menu_general')
    @else
        @include('menu_general.menu_general_tecnico')
    @endif

    <div class="main-container">
        <div class="container-fluid py-4">
            <div class="bg-white rounded shadow-sm p-4">
                <h3 class="mb-3 text-center">Proyectos (Técnico) — Mapas</h3>

                <div class="table-responsive">
                    <table id="proyectosTable" class="table table-striped table-hover w-100">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Proyecto</th>
                                <th>Proponente</th>
                                <th>Actividad</th>
                                <th>Cantón</th>
                                <th>Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($proyectos as $p)
                                <tr>
                                    <td>{{ $p->id_proyecto }}</td>
                                    <td>{{ $p->nombre }}</td>
                                    <td>{{ $p->proponente }}</td>
                                    <td>{{ $p->actividad }}</td>
                                    <td>{{ $p->canton }}</td>
                                    <td>
                                        <a href="{{ route('tecnico.proyectos.mapa', $p->id_proyecto) }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fas fa-map-marked-alt"></i> Ver mapa
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No hay proyectos para mostrar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <style>
        /* Tus estilos personalizados */
        .dataTables_wrapper .dataTables_filter {
            float: right;
            text-align: right;
            margin-bottom: 10px;
            margin-right: 80px;
        }

        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5em;
            width: 200px;
            max-width: 100%;
        }

        .dataTables_wrapper .dataTables_length {
            float: left;
            margin-bottom: 10px;
        }

        .dataTables_wrapper .dataTables_info {
            clear: both;
            float: left;
            padding-top: 0.755em;
        }

        .dataTables_wrapper .dataTables_paginate {
            float: right;
            text-align: right;
            padding-top: 0.25em;
        }

        @media (max-width: 767px) {
            .dataTables_wrapper .dataTables_filter {
                margin-right: 20px;
            }

            .dataTables_wrapper .dataTables_filter input {
                width: 150px;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            @if ($proyectos->isNotEmpty())
                $('#proyectosTable').DataTable({
                    language: {
                        "sProcessing": "Procesando...",
                        "sLengthMenu": "Mostrar _MENU_ registros",
                        "sZeroRecords": "No se encontraron resultados",
                        "sEmptyTable": "Ningún dato disponible en esta tabla",
                        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                        "sSearch": "Buscar:",
                        "oPaginate": {
                            "sFirst": "Primero",
                            "sLast": "Último",
                            "sNext": "Siguiente",
                            "sPrevious": "Anterior"
                        }
                    },
                    pageLength: 10,
                    responsive: true,
                    autoWidth: false,
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
                });
            @endif
        });
    </script>
@endsection
