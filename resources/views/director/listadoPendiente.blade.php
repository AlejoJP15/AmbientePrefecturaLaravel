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
                <h2 class="mb-3 text-center">Listado de Obligaciones Pendientes</h2>

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
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $contador = 0;
                            @endphp
                            @foreach($obligaciones as $obl)
                                @if($obl->id_estado_proyecto == 1)
                                    @php
                                        $contador++;
                                    @endphp
                                    <tr>
                                        <td>{{ $contador }}</td>
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
                                        <td>
                                            <button class="btn btn-success btn-sm asignar-btn" 
                                                    data-proyecto-id="{{ $obl->id_proyecto }}"
                                                    data-proyecto-nombre="{{ $obl->proyecto_nombre }}">
                                                <i class="fas fa-user-check"></i> Asignar
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmarAsignacionModal" tabindex="-1" aria-labelledby="confirmarAsignacionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmarAsignacionModalLabel">Confirmar Asignación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="mensajeConfirmacion">¿Seguro que desea asignar el proyecto "<span id="nombreProyecto"></span>" al coordinador?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="formAsignarProyecto" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">Sí, Asignar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- jQuery (requerido por DataTables) --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- DataTables JS con Bootstrap 5 --}}
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
            // Inicializar DataTable
            const table = $('#obligacionesTable').DataTable({
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "No hay obligaciones pendientes disponibles",
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
                    targets: [0, 9] // Columna # y Acción no ordenables
                }],
                responsive: true,
                scrollX: false,
                autoWidth: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            // Manejar clic en botón Asignar
            $(document).on('click', '.asignar-btn', function() {
                const proyectoId = $(this).data('proyecto-id');
                const proyectoNombre = $(this).data('proyecto-nombre');
                
                // Actualizar el modal con la información del proyecto
                $('#nombreProyecto').text(proyectoNombre);
                $('#formAsignarProyecto').attr('action', '/director/proyectos/' + proyectoId + '/asignar');
                
                // Mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('confirmarAsignacionModal'));
                modal.show();
            });

            // Manejar envío del formulario
            $('#formAsignarProyecto').on('submit', function(e) {
                e.preventDefault();
                
                const form = $(this);
                const url = form.attr('action');
                
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Cerrar modal
                            bootstrap.Modal.getInstance(document.getElementById('confirmarAsignacionModal')).hide();
                            
                            // Mostrar mensaje de éxito
                            alert('Proyecto asignado correctamente');
                            
                            // Recargar la página para actualizar la tabla
                            location.reload();
                        } else {
                            alert('Error al asignar el proyecto: ' + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Error al asignar el proyecto');
                        console.error(xhr);
                    }
                });
            });
        });
    </script>
@endsection