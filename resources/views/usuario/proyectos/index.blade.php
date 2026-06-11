@extends('layouts.app')

@section('content')
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('styles/archivo.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    {{-- DataTables CSS con Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

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
        @include('menu_general.menu_general_usuario')
    @endif

    <div class="main-container">
        <div class="archivo-wrapper">
            <div class="soca-container">
                <h2 class="mb-3 text-center">Listado de Obligaciones Registradas</h2>

                <table id="obligacionesTable" class="tramites-table w-100">
                    <thead>
                        <tr>
                            <th class="text-center">CÓDIGO</th>
                            <th class="text-center">NOMBRE DEL PROYECTO</th>
                            <th class="text-center">FECHA REGISTRO</th>
                            <th class="text-center">SECTOR</th>
                            <th class="text-center">ACTIVIDAD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proyectos as $p)
                            <tr>
                                <td>
                                    <a href="{{ route('usuario.proyectos.show', $p->id_proyecto) }}">
                                        HGADPCH-PRO-{{ $p->id_proyecto }}
                                    </a>
                                </td>
                                <td>{{ $p->nombre ?? '—' }}</td>
                                <td>{{ optional($p->fecha_creacion)->format('Y-m-d H:i:s') ?? '—' }}</td>
                                <td>{{ $p->sector ?? '—' }}</td>
                                <td>{{ optional($p->actividad)->descripcion_actividad ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- DataTables JS --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <style>
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

        .btn-success {
            background-color: #198754;
            border-color: #198754;
        }

        .btn-success:hover {
            background-color: #157347;
            border-color: #146c43;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#obligacionesTable').DataTable({
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Aún no tienes proyectos registrados.",
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
        });
    </script>
@endsection
