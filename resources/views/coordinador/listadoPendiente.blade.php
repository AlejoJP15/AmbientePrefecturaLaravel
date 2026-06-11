@extends('layouts.app')

@section('content')
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('styles/archivo.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    @include('menu_general.menu_general_coordinador')

    <div class="main-container">
        <div class="container-fluid py-4">
            <div class="bg-white rounded shadow-sm p-4">
                <h2 class="mb-3 text-center">Listado de Obligaciones Pendientes de Asignar</h2>

                <div class="table-responsive">
                    <table id="obligacionesTable" class="table table-striped table-hover w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Código</th>
                                <th>Proyecto</th>
                                <th>Sector</th>
                                <th>Actividad</th>
                                <th>Tipo de Obligación</th>
                                <th>Acción</th> <!-- Solo 7 columnas -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($obligaciones as $obl)
                                <tr>
                                    <td>{{ $loop->iteration + ($obligaciones->currentPage() - 1) * $obligaciones->perPage() }}
                                    </td>
                                    <td>
                                        <a href="{{ route('coordinador.obligaciones.resumen', $obl->id_obligacion) }}">
                                            HGADPCH-DOC-{{ $obl->id_obligacion }}
                                        </a>
                                    </td>
                                    <td>{{ $obl->proyecto_nombre ?? '—' }}</td>
                                    <td>{{ $obl->proyecto_sector ?? '—' }}</td>
                                    <td>{{ $obl->actividad_descripcion ?? '—' }}</td>
                                    <td>{{ $obl->tipo_obligacion ?? '—' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary asignar-btn"
                                            data-id-obligacion="{{ $obl->id_obligacion }}"
                                            data-id-proyecto="{{ $obl->id_proyecto }}" data-bs-toggle="modal"
                                            data-bs-target="#asignarModal">
                                            <i class="fas fa-user-plus"></i> Asignar técnico
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de asignación --}}
    <div class="modal fade" id="asignarModal" tabindex="-1" aria-labelledby="asignarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="asignarModalLabel">Asignar Técnico</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAsignar">
                    <div class="modal-body">
                        <input type="hidden" id="id_proyecto" name="id_proyecto">
                        <input type="hidden" id="id_obligacion" name="id_obligacion">
                        <input type="hidden" id="id_asignador" name="id_asignador"
                            value="{{ auth()->user()->id_persona ?? '' }}">

                        <div class="mb-3">
                            <label for="id_asignado" class="form-label">Técnico</label>
                            <select class="form-select" id="id_asignado" name="id_asignado" required>
                                <option value="">-- Seleccione un técnico --</option>
                                @foreach ($tecnicos as $tecnico)
                                    <option value="{{ $tecnico->id_persona }}">{{ $tecnico->nombres }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="comentario" class="form-label">Comentario (opcional)</label>
                            <textarea class="form-control" id="comentario" name="comentario" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Asignar</button>
                    </div>
                </form>
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
            $('#obligacionesTable').DataTable({
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "No hay proyectos pendientes de asignar",
                    "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando 0 a 0 de 0 registros",
                    "sInfoFiltered": "(filtrado de _MAX_ total)",
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
                autoWidth: false
            });

            // Llenar el modal con los datos del registro
            $('.asignar-btn').on('click', function() {
                const idProyecto = $(this).data('id-proyecto');
                const idObligacion = $(this).data('id-obligacion');
                $('#id_proyecto').val(idProyecto);
                $('#id_obligacion').val(idObligacion);
            });

            // Envío del formulario por AJAX
            $('#formAsignar').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('coordinador.obligacion.asignar') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        alert('Asignación realizada con éxito.');
                        $('#asignarModal').modal('hide');
                        location.reload(); // O actualizar solo la fila si prefieres
                    },
                    error: function(xhr) {
                        alert('Error al asignar: ' + (xhr.responseJSON?.message ||
                            'Intente nuevamente.'));
                    }
                });
            });
        });
    </script>
@endsection
