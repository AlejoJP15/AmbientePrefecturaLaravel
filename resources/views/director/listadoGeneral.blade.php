@extends('layouts.app')

@section('content')
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('styles/archivo.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    {{-- DataTables CSS con Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    @include('menu_general.menu_general_director')

    <div class="main-container">
        <div class="container-fluid py-4">
            <div class="bg-white rounded shadow-sm p-4">
                <h2 class="mb-3 text-center">Listado de Obligaciones Registradas</h2>

                <div class="table-responsive">
                    <table id="obligacionesTable" class="table table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Proyecto</th>
                                <th>F. Registro</th>
                                <th>Sector</th>
                                <th>Actividad</th>
                                <th>Tipo de obligación</th>
                                <th>Periodo</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($obligaciones as $i => $obl)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <a href="{{ route('director.obligaciones.resumen', $obl->id_obligacion) }}">
                                            HGADPCH-DOC-{{ $obl->id_obligacion }}
                                        </a>
                                    </td>
                                    <td>{{ $obl->proyecto_nombre ?? '—' }}</td>
                                    <td data-order="{{ optional($obl->fecha_registro)->timestamp ?? 0 }}">
                                        {{ optional($obl->fecha_registro)->format('Y-m-d H:i') ?? '—' }}
                                    </td>
                                    <td>{{ $obl->proyecto_sector ?? '—' }}</td>
                                    <td>{{ $obl->actividad_descripcion ?? '—' }}</td>
                                    <td>{{ $obl->tipo_obligacion ?? '—' }}</td>
                                    <td>{{ $obl->periodo ?? '—' }}</td>
                                    <td>{{ $obl->estado_proyecto ?? '—' }}</td>
                                </tr>
                            @empty
                                {{-- Do NOT output a <tr> here --}}
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- jQuery (requerido por DataTables) --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    {{-- DataTables JS con Bootstrap 5 --}}
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <style>
        .dataTables_wrapper .dataTables_filter {
            float: right;
            text-align: right;
            margin-bottom: 10px;
            margin-right: 80px;
            /* Mover hacia la izquierda */
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
                /* Menos margen en móvil */
            }

            .dataTables_wrapper .dataTables_filter input {
                width: 150px;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#obligacionesTable').DataTable({
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sSearch": "Buscar:",
                    "sUrl": "",
                    "sInfoThousands": ",",
                    "sLoadingRecords": "Cargando...",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "oAria": {
                        "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                    }
                },
                pageLength: 10,
                order: [
                    [3, 'desc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: 0
                }],
                responsive: true,
                scrollX: false,
                autoWidth: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });
        });
    </script>
@endsection
