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
                <h2 class="mb-4 text-center">
                    <i class="fas fa-inbox"></i> Bandeja de Entrada
                </h2>

                {{-- Filtros --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="filtro" id="filtroTodos" value="todos" checked>
                            <label class="btn btn-outline-primary" for="filtroTodos">
                                <i class="fas fa-list"></i> Todos
                            </label>

                            <input type="radio" class="btn-check" name="filtro" id="filtroAprobado" value="aprobado">
                            <label class="btn btn-outline-success" for="filtroAprobado">
                                <i class="fas fa-check-circle"></i> Aprobados
                            </label>

                            <input type="radio" class="btn-check" name="filtro" id="filtroRechazado" value="rechazado">
                            <label class="btn btn-outline-danger" for="filtroRechazado">
                                <i class="fas fa-times-circle"></i> Rechazados
                            </label>

                            <input type="radio" class="btn-check" name="filtro" id="filtroObservacion" value="observacion">
                            <label class="btn btn-outline-warning" for="filtroObservacion">
                                <i class="fas fa-eye"></i> Observaciones
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Mensaje si no hay proyectos --}}
                @if($proyectos->isEmpty())
                    <div class="alert alert-info text-center" role="alert">
                        <i class="fas fa-envelope-open"></i>
                        <p class="mb-0">No tienes proyectos registrados.</p>
                    </div>
                @else
                    {{-- Tabla de proyectos con comentarios --}}
                    <div class="table-responsive">
                        <table id="notificacionesTable" class="tramites-table w-100">
                            <thead>
                                <tr>
                                    <th class="text-center">CÓDIGO</th>
                                    <th class="text-center">PROYECTO</th>
                                    <th class="text-center">ESTADO</th>
                                    <th class="text-center">FECHA REGISTRO</th>
                                    <th class="text-center">OBSERVACIONES</th>
                                    <th class="text-center">ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($proyectos as $p)
                                    <tr class="notificacion-row {{ ($p->unread_count ?? 0) > 0 ? 'tr-has-unread' : '' }}"
                                        data-proyecto-id="{{ $p->id_proyecto }}"
                                        data-unread="{{ $p->unread_count ?? 0 }}"
                                        data-modal-id="modalComentarios{{ $p->id_proyecto }}"
                                        data-row>

                                        <td>
                                            <span class="codigo-badge">
                                                HGADPCH-PRO-{{ $p->id_proyecto }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $p->nombre ?? '—' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $p->sector ?? '—' }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $estado = $p->estadoProyecto?->descripcion ?? 'Pendiente';
                                                $badgeClass = match($estado) {
                                                    'Aprobado' => 'bg-success',
                                                    'Rechazado' => 'bg-danger',
                                                    'Observaciones' => 'bg-warning text-dark',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                @switch($estado)
                                                    @case('Aprobado')
                                                        <i class="fas fa-check-circle"></i> Aprobado
                                                        @break
                                                    @case('Rechazado')
                                                        <i class="fas fa-times-circle"></i> Rechazado
                                                        @break
                                                    @case('Observaciones')
                                                        <i class="fas fa-eye"></i> Observación
                                                        @break
                                                    @default
                                                        <i class="fas fa-clock"></i> {{ $estado }}
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>
                                            {{ optional($p->fecha_creacion)->format('d/m/Y H:i') ?? '—' }}
                                        </td>
                                        <td>
                                            @if($p->comentarios->isNotEmpty())
                                                <button class="btn btn-sm btn-info position-relative js-open-comments"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalComentarios{{ $p->id_proyecto }}"
                                                        data-proyecto-id="{{ $p->id_proyecto }}">
                                                    <i class="fas fa-comment-dots"></i> Ver
                                                    @if(($p->unread_count ?? 0) > 0)
                                                    <span class="notify-dot" title="{{ $p->unread_count }} nuevo(s)"></span>
                                                    {{-- o si prefieres número, usa: <span class="notify-num">{{ $p->unread_count }}</span> --}}
                                                    @endif
                                                </button>
                                                @else
                                                <span class="text-muted">—</span>
                                                @endif

                                        </td>
                                        <td>
                                            <a href="{{ route('usuario.proyectos.show', $p->id_proyecto) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Detalles
                                            </a>
                                        </td>
                                    </tr>

                                    {{-- Modal de comentarios --}}
                                    @if($p->comentarios->isNotEmpty())
                                        <div class="modal fade" id="modalComentarios{{ $p->id_proyecto }}" 
                                             tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-comment-dots"></i> Comentarios del Proyecto
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" 
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p><strong>Código:</strong> HGADPCH-PRO-{{ $p->id_proyecto }}</p>
                                                        <p><strong>Proyecto:</strong> {{ $p->nombre }}</p>
                                                        <hr>
                                                        @foreach($p->comentarios as $comentario)
                                                            <div class="alert alert-light border mb-3">
                                                                <div class="d-flex justify-content-between">
                                                                    <strong>Comentario #{{ $comentario->id_comentario }}</strong>
                                                                    <small class="text-muted">
                                                                        {{ optional($comentario->fecha_comentario)->format('d/m/Y H:i') }}
                                                                    </small>
                                                                </div>
                                                                <div class="mt-2">
                                                                    {!! nl2br(e($comentario->descripcion)) !!}
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" 
                                                                data-bs-dismiss="modal">Cerrar</button>
                                                        <a href="{{ route('usuario.proyectos.show', $p->id_proyecto) }}"
                                                            class="btn btn-primary position-relative">
                                                            <i class="fas fa-eye"></i> Ver Detalles del Proyecto

                                                            @if(($p->unread_count ?? 0) > 0)
                                                                <span class="notify-dot" title="{{ $p->unread_count }} nuevo(s)"></span>
                                                            @endif
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación (si usas paginación) --}}
                    {{-- Si no estás usando paginación, puedes omitir esto --}}
                    {{-- @if(method_exists($proyectos, 'links'))
                        <div style="margin-top: 1.5rem;">
                            {{ $proyectos->links('pagination::bootstrap-5') }}
                        </div>
                    @endif --}}
                @endif
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
        .notificacion-row {
            transition: background-color 0.3s ease;
        }

        .notificacion-row:hover {
            background-color: #f8f9fa;
        }

        .codigo-badge {
            background-color: #e7f3ff;
            color: #0066cc;
            padding: 0.5rem 0.75rem;
            border-radius: 0.25rem;
            font-weight: 600;
            font-size: 0.85rem;
        }

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

            .btn-group {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .btn-group .btn {
                flex: 1 1 calc(50% - 0.25rem);
                min-width: 120px;
            }
        }

    </style>

    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            const table = $('#notificacionesTable').DataTable({
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "No tienes proyectos registrados.",
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
                columnDefs: [
                    { orderable: false, targets: 5 }
                ],
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });

            // Filtrado por estado
            $('input[name="filtro"]').on('change', function() {
                const filtro = $(this).val();
                
                if (filtro === 'todos') {
                    table.column(2).search('').draw();
                } else {
                    let searchTerm = '';
                    if (filtro === 'aprobado') searchTerm = 'Aprobado';
                    else if (filtro === 'rechazado') searchTerm = 'Rechazado';
                    else if (filtro === 'observacion') searchTerm = 'Observación';
                    
                    table.column(2).search(searchTerm).draw();
                }
            });
        });
    </script>

    
    <script>
        (function(){
        const marked = new Set(); // para no repetir el POST

        async function marcarLeidos(id) {
            
            try {
                await fetch(`/usuario/proyectos/${id}/comentarios/marcar-leidos`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': `{{ csrf_token() }}`, 'Accept': 'application/json' }
                });
            } catch (_) { /* no bloqueamos */ }

            // UI optimista: quitar resaltado y puntitos
            const tr = document.querySelector(`tr[data-proyecto-id="${id}"][data-row]`);
            if (tr) tr.classList.remove('tr-has-unread');

            // Botón "Ver" en la tabla
            document.querySelectorAll(`.js-open-comments[data-proyecto-id="${id}"] .notify-dot, 
                                    .js-open-comments[data-proyecto-id="${id}"] .notify-num`).forEach(el => el.remove());

            // Puntito en el botón "Ver Detalles del Proyecto" dentro del modal (si lo dejaste ahí)
            const modal = document.getElementById(`modalComentarios${id}`);
            if (modal) modal.querySelectorAll('.notify-dot, .notify-num').forEach(el => el.remove());
        }

        // A) Cuando el usuario hace clic en "Ver"
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.js-open-comments');
            if (!btn) return;
            const id = btn.getAttribute('data-proyecto-id');
            if (id) marcarLeidos(id); // disparamos ya; el modal abre igual
        });

        // B) Seguridad extra: si el modal llega a abrirse por otros medios
        document.addEventListener('shown.bs.modal', (ev) => {
            const id = (ev.target.id || '').replace('modalComentarios', '');
            if (id && /^\d+$/.test(id)) marcarLeidos(id);
        });
        })();
    </script>






    <style>
        .tr-has-unread { background:#fff7ed; }             /* fila ámbar suave */
        .tr-has-unread td:first-child { border-left:4px solid #fb923c; }
        .notify-dot{
            position:absolute; top:-4px; right:-4px;
            width:12px; height:12px; border-radius:9999px;
            background:#dc3545; box-shadow:0 0 0 2px #fff;
        }
        .notify-num{
            position:absolute; top:-7px; right:-7px;
            min-width:18px; height:18px; padding:0 4px;
            font-size:11px; line-height:18px; text-align:center;
            border-radius:9999px; color:#fff; background:#dc3545;
            box-shadow:0 0 0 2px #fff;
        }
    </style>


@endsection