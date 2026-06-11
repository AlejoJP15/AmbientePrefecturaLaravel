@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('styles/archivo.css') }}">
    <link rel="stylesheet" href="{{ asset('styles/resumenT.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .decision-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 2rem;
            margin: 2rem 0;
        }

        .decision-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .decision-title i {
            color: #007bff;
        }

        .decision-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .decision-card {
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .decision-card input[type="radio"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .decision-card-content {
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 1.25rem;
            text-align: center;
            background: white;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .decision-card input[type="radio"]:checked+.decision-card-content {
            border-color: #007bff;
            background: #e7f1ff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
        }

        .decision-card-content i {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            transition: color 0.3s ease;
        }

        .decision-card:nth-child(1) .decision-card-content i {
            color: #28a745;
        }

        .decision-card:nth-child(1) input[type="radio"]:checked+.decision-card-content i {
            color: #20c997;
        }

        .decision-card:nth-child(2) .decision-card-content i {
            color: #dc3545;
        }

        .decision-card:nth-child(2) input[type="radio"]:checked+.decision-card-content i {
            color: #e74c3c;
        }

        .decision-card:nth-child(3) .decision-card-content i {
            color: #ffc107;
        }

        .decision-card:nth-child(3) input[type="radio"]:checked+.decision-card-content i {
            color: #ff9800;
        }

        .decision-card-label {
            font-weight: 600;
            font-size: 1rem;
            color: #333;
            margin-bottom: 0.5rem;
            display: block;
        }

        .decision-card-description {
            font-size: 0.85rem;
            color: #666;
            margin: 0;
        }

        .comment-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e0e0e0;
            display: none;
        }

        .comment-section.active {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .comment-label {
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: #333;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        textarea.decision-textarea {
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.75rem;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            resize: vertical;
            min-height: 100px;
            transition: border-color 0.3s ease;
        }

        textarea.decision-textarea:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
            outline: none;
        }

        .button-group {
            display: flex;
            gap: 0.75rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .obl-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .obl-btn:not(:disabled) {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .obl-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .obl-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .obl-btn--primary {
            background: #007bff;
            color: white;
        }

        .obl-btn--secondary {
            background: #6c757d;
            color: white;
        }

        .obl-btn--ghost {
            background: transparent;
            color: #666;
            border: 1px solid #ddd;
        }

        .header-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-left: 4px solid #007bff;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .info-item {
            background: white;
            padding: 0.75rem;
            border-radius: 6px;
            border-left: 3px solid #007bff;
        }

        .info-label {
            font-size: 0.85rem;
            color: #666;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 0.95rem;
            color: #333;
            margin-top: 0.25rem;
            font-weight: 600;
        }

        .alert-message {
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            display: none;
        }

        .alert-message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }

        .alert-message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }

        .decision-card-content:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endpush

@section('content')

    {{-- Menú general del técnico --}}
    @include('menu_general.menu_general_tecnico')

    <div class="main-container">
        <div class="archivo-wrapper">
            <div class="soca-container">

                {{-- Header con información de la obligación --}}
                <div class="header-section">
                    <h2 style="margin: 0 0 1rem 0; color: #333;">
                        <i class="fas fa-file-contract" style="margin-right: 0.75rem; color: #007bff;"></i>
                        Obligación HGADPCH-DOC-{{ $obligacion->id_obligacion }}
                    </h2>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Proyecto</div>
                            <div class="info-value">{{ $proyecto->nombre ?? '—' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Sector</div>
                            <div class="info-value">{{ $proyecto->sector ?? '—' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Actividad</div>
                            <div class="info-value">{{ $proyecto->actividad->descripcion_actividad ?? '—' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Tipo de Obligación</div>
                            <div class="info-value">{{ $obligacion->descripcion ?? '—' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Período</div>
                            <div class="info-value">{{ $obligacion->periodo ?? '—' }}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">F. Registro</div>
                            <div class="info-value">{{ optional($obligacion->fecha_registro)->format('Y-m-d H:i') ?? '—' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Documentos enviados --}}
                <div style="margin: 2rem 0;">
                    <h3 style="margin-bottom: 1rem; color: #333; font-weight: 700;">
                        <i class="fas fa-folder-open" style="margin-right: 0.5rem; color: #007bff;"></i>
                        Documentos enviados
                    </h3>
                    <div style="overflow: auto; background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-bottom: 2px solid #dee2e6;">
                                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; font-weight: 600; color: white;">
                                        TÍTULO</th>
                                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; font-weight: 600; color: white;">
                                        TIPO</th>
                                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; font-weight: 600; color: white;">
                                        TÉCNICO ASIGNADO</th>
                                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: left; font-weight: 600; color: white;">
                                        FECHA CREACIÓN</th>
                                    <th style="border: 1px solid #dee2e6; padding: 12px; text-align: center; font-weight: 600; color: white;">
                                        ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($files as $f)
                                    <tr style="border-bottom: 1px solid #dee2e6; transition: background 0.2s;">
                                        <td style="border: 1px solid #dee2e6; padding: 12px;">
                                            <i class="fas fa-file-pdf" style="color: #dc3545; margin-right: 0.5rem;"></i>
                                            {{ $f['name'] }}
                                        </td>
                                        <td style="border: 1px solid #dee2e6; padding: 12px;">
                                            <span style="background: #e7f1ff; color: #0056b3; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                                                {{ $f['tipo'] }}
                                            </span>
                                        </td>
                                        <td style="border: 1px solid #dee2e6; padding: 12px;">
                                            @if($tecnicoAsignado)
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <i class="fas fa-user-tie" style="color: #667eea;"></i>
                                                    <span style="font-weight: 500; color: #333;">{{ $tecnicoAsignado->nombre_completo }}</span>
                                                </div>
                                            @else
                                                <span style="color: #999; font-style: italic;">
                                                    <i class="fas fa-user-slash" style="margin-right: 0.5rem;"></i>No asignado
                                                </span>
                                            @endif
                                        </td>
                                        <td style="border: 1px solid #dee2e6; padding: 12px;">
                                            <i class="fas fa-clock" style="color: #999; margin-right: 0.5rem; font-size: 0.85rem;"></i>
                                            {{ $f['created_at'] }}
                                        </td>
                                        <td style="border: 1px solid #dee2e6; padding: 12px; text-align: center; white-space: nowrap;">
                                            <a href="{{ route('obligaciones.documentos.download', $obligacion->id_obligacion) }}?file={{ urlencode($f['path']) }}"
                                                class="obl-btn"
                                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; font-size: 0.85rem; padding: 0.5rem 1rem; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s ease;">
                                                <i class="fas fa-download"></i>Descargar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="border: 1px solid #dee2e6; padding: 2rem; color: #999; text-align: center;">
                                            <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem; display: block; opacity: 0.5;"></i>
                                            <span style="font-size: 0.95rem;">Aún no hay archivos subidos.</span>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Formulario de evaluación --}}
                <form method="POST"
                    action="{{ route('tecnico.obligaciones.dictamen.update', $obligacion->id_obligacion) }}">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="firma_verificada" id="firmaVerificada" value="0">
                    <input type="hidden" name="decision" id="decisionInput" value="">

                    {{-- Sección de decisión --}}
                    <div class="decision-section">
                        <div class="decision-title">
                            <i class="fas fa-gavel"></i>
                            Decisión del técnico
                        </div>

                        <div class="decision-options">
                            <label class="decision-card">
                                <input type="radio" name="decision_radio" value="aprobado" data-decision="aprobado"
                                    required>
                                <div class="decision-card-content">
                                    <i class="fas fa-check-circle"></i>
                                    <span class="decision-card-label">Aprobado</span>
                                    <p class="decision-card-description">El proyecto cumple con todos los requisitos</p>
                                </div>
                            </label>

                            <label class="decision-card">
                                <input type="radio" name="decision_radio" value="rechazado" data-decision="rechazado"
                                    required>
                                <div class="decision-card-content">
                                    <i class="fas fa-times-circle"></i>
                                    <span class="decision-card-label">Rechazado</span>
                                    <p class="decision-card-description">El proyecto no cumple los requisitos</p>
                                </div>
                            </label>

                            <label class="decision-card">
                                <input type="radio" name="decision_radio" value="observaciones"
                                    data-decision="observaciones" required>
                                <div class="decision-card-content">
                                    <i class="fas fa-clipboard-list"></i>
                                    <span class="decision-card-label">Observaciones</span>
                                    <p class="decision-card-description">Requiere correcciones antes de aprobar</p>
                                </div>
                            </label>
                        </div>

                        {{-- Sección de comentario para la decisión --}}
                        <div class="comment-section" id="commentSection">
                            <label class="comment-label">
                                <i class="fas fa-comment-dots" style="color: #007bff;"></i>
                                Comentario sobre la decisión
                            </label>
                            <textarea name="comentario_decision" id="decisionComment" class="decision-textarea"
                                placeholder="Explica brevemente los motivos de tu decisión..."></textarea>
                        </div>
                    </div>

                    {{-- Sección de firma --}}
                    <div
                        style="background: white; border-radius: 8px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin: 2rem 0;">
                        <h3 style="margin-bottom: 1.5rem; color: #333; font-weight: 700;">
                            <i class="fas fa-signature" style="margin-right: 0.5rem; color: #007bff;"></i>
                            Firma electrónica
                        </h3>

                        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                            <button type="button" class="obl-btn obl-btn--secondary" data-bs-toggle="modal"
                                data-bs-target="#firmaModal">
                                <i class="fas fa-upload" style="margin-right: 0.5rem;"></i>
                                Subir Firma
                            </button>

                            <span id="badgeFirma" style="font-size: 0.95rem; color: #dc3545; font-weight: 600;">
                                <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                                Firma no verificada
                            </span>
                        </div>
                    </div>

                    {{-- Botones de acción --}}
                    <div class="button-group">
                        <button type="submit" id="btnGuardar" name="action" value="guardar"
                            class="obl-btn obl-btn--primary" disabled>
                            <i class="fas fa-save" style="margin-right: 0.5rem;"></i>
                            Guardar decisión
                        </button>

                        <button type="submit" id="btnDevolverCoordinador" name="action" value="devolver_coordinador"
                            class="obl-btn obl-btn--ghost" style="display: none;">
                            <i class="fas fa-undo" style="margin-right: 0.5rem;"></i>
                            Devolver al coordinador
                        </button>

                        <button type="submit" id="btnDevolverUsuario" name="action" value="devolver_usuario" class="btn btn-warning"
                            class="obl-btn obl-btn--ghost" style="display: none;">
                            <i class="fas fa-share-left" style="margin-right: 0.5rem;"></i>
                            Devolver al usuario externo
                        </button>
                    </div>

                    {{-- Mensajes de sesión --}}
                    @if (session('success'))
                        <div class="alert-message success">
                            <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert-message error">
                            <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>
                            {{ session('error') }}
                        </div>
                    @endif
                </form>

                {{-- Historial de comentarios --}}
                @isset($comentarios)
                    @if ($comentarios->count())
                        <div style="margin-top: 2rem;">
                            <h3 style="margin-bottom: 1rem; color: #333; font-weight: 700;">
                                <i class="fas fa-history" style="margin-right: 0.5rem; color: #007bff;"></i>
                                Últimos comentarios
                            </h3>
                            <ul style="list-style: none; padding-left: 0;">
                                @foreach ($comentarios as $c)
                                    <li
                                        style="background: white; border: 1px solid #dee2e6; padding: 1rem; border-radius: 8px; margin-bottom: 0.75rem; transition: all 0.2s;">
                                        <div style="font-size: 0.95rem; color: #333; margin-bottom: 0.5rem;">
                                            {{ $c->descripcion }}</div>
                                        <div style="font-size: 0.85rem; color: #999;">
                                            <i class="fas fa-clock" style="margin-right: 0.5rem;"></i>
                                            {{ \Carbon\Carbon::parse($c->fecha_comentario)->format('Y-m-d H:i') }}
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endisset

            </div>
        </div>
    </div>

    {{-- Modal: subida de firma electrónica --}}
    <div class="modal fade" id="firmaModal" tabindex="-1" aria-labelledby="firmaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content"
                style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                <div class="modal-header"
                    style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); color: white; border: none;">
                    <h5 class="modal-title" id="firmaModalLabel">
                        <i class="fas fa-certificate" style="margin-right: 0.75rem;"></i>
                        Subir firma electrónica
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <form id="formFirma" enctype="multipart/form-data" autocomplete="off">
                    @csrf
                    <div class="modal-body" style="display: flex; flex-direction: column; gap: 1rem; padding: 1.5rem;">
                        <div>
                            <label class="form-label" style="font-weight: 600; color: #333;">Archivo .p12</label>
                            <input type="file" id="archivoP12" name="archivo_p12" accept=".p12,application/x-pkcs12"
                                class="form-control"
                                style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 0.75rem;" required>
                            <div class="form-text" style="font-size: 0.85rem; color: #666; margin-top: 0.5rem;">
                                <i class="fas fa-info-circle" style="margin-right: 0.5rem;"></i>
                                Selecciona tu certificado en formato .p12
                            </div>
                        </div>

                        <div>
                            <label class="form-label" style="font-weight: 600; color: #333;">Contraseña</label>
                            <input type="password" id="passP12" name="pass_p12" class="form-control"
                                style="border-radius: 8px; border: 2px solid #e0e0e0; padding: 0.75rem;"
                                placeholder="Contraseña del certificado" required>
                        </div>

                        <div id="msgFirma"
                            style="display: none; font-size: 0.9rem; padding: 0.75rem; border-radius: 6px;"></div>
                    </div>

                    <div class="modal-footer"
                        style="border-top: 1px solid #e0e0e0; display: flex; justify-content: space-between; gap: 0.75rem; padding: 1.5rem;">
                        <button type="button" class="obl-btn obl-btn--ghost" data-bs-dismiss="modal">
                            <i class="fas fa-times" style="margin-right: 0.5rem;"></i>
                            Cancelar
                        </button>
                        <button type="submit" id="btnValidarFirma" class="obl-btn obl-btn--primary">
                            <i class="fas fa-check" style="margin-right: 0.5rem;"></i>
                            Validar firma
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const saveBtn = document.getElementById('btnGuardar');
            const badge = document.getElementById('badgeFirma');
            const hiddenFlag = document.getElementById('firmaVerificada');
            const decisionInput = document.getElementById('decisionInput');
            const formFirma = document.getElementById('formFirma');
            const archivoP12 = document.getElementById('archivoP12');
            const passP12 = document.getElementById('passP12');
            const msg = document.getElementById('msgFirma');
            const btnValidar = document.getElementById('btnValidarFirma');
            const commentSection = document.getElementById('commentSection');
            const decisionRadios = document.querySelectorAll('input[name="decision_radio"]');
            const btnDevolverCoordinador = document.getElementById('btnDevolverCoordinador');
            const btnDevolverUsuario = document.getElementById('btnDevolverUsuario');

            const validarUrl = `{{ route('tecnico.obligaciones.firma.validar', $obligacion->id_obligacion) }}`;
            const csrfToken = `{{ csrf_token() }}`;

            let currentDecision = null;

            const setVerified = (ok, textOk = 'Firma verificada') => {
                hiddenFlag.value = ok ? '1' : '0';
                updateSaveButton();
                badge.innerHTML = ok ?
                    '<i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i>Firma verificada' :
                    '<i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i>Firma no verificada';
                badge.style.color = ok ? '#28a745' : '#dc3545';
            };

            const updateSaveButton = () => {
                const hasDecision = Array.from(decisionRadios).some(r => r.checked);
                const requiresFirma = currentDecision === 'aprobado';
                const hasFirma = hiddenFlag.value === '1';
                const canSave = hasDecision && (!requiresFirma || hasFirma);
                saveBtn.disabled = !canSave;
                btnDevolverCoordinador.style.display = 'none';
                btnDevolverUsuario.style.display = 'none';
                if (currentDecision === 'rechazado' || currentDecision === 'observaciones') {
                    btnDevolverUsuario.style.display = 'inline-block';
                }
            };

            setVerified(false);

            decisionRadios.forEach(radio => {
                radio.addEventListener('change', (e) => {
                    currentDecision = e.target.value;
                    decisionInput.value = e.target.value;
                    commentSection.classList.add('active');
                    updateSaveButton();
                });
            });

            formFirma.addEventListener('submit', async (e) => {
                e.preventDefault();

                msg.style.display = 'none';
                msg.textContent = '';

                const fileOK = archivoP12.files && archivoP12.files.length === 1;
                const passOK = passP12.value.trim().length > 0;

                if (!fileOK || !passOK) {
                    msg.style.display = 'block';
                    msg.style.color = '#dc3545';
                    msg.style.background = '#f8d7da';
                    msg.style.border = '1px solid #f5c6cb';
                    msg.style.borderRadius = '6px';
                    msg.style.padding = '0.75rem';
                    msg.textContent = 'Adjunta el .p12 y escribe la contraseña.';
                    setVerified(false);
                    return;
                }

                const fd = new FormData();
                fd.append('archivo_p12', archivoP12.files[0]);
                fd.append('pass_p12', passP12.value);

                btnValidar.disabled = true;
                btnValidar.innerHTML =
                    '<i class="fas fa-spinner fa-spin" style="margin-right: 0.5rem;"></i>Validando...';

                try {
                    const res = await fetch(validarUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: fd
                    });

                    let data = {};
                    try {
                        data = await res.json();
                    } catch (_) {}

                    if (!res.ok || !data || data.ok !== true) {
                        const errMsg = (data && data.message) ? data.message :
                            'No fue posible validar el certificado.';
                        msg.style.display = 'block';
                        msg.style.color = '#721c24';
                        msg.style.background = '#f8d7da';
                        msg.style.border = '1px solid #f5c6cb';
                        msg.style.borderRadius = '6px';
                        msg.style.padding = '0.75rem';
                        msg.textContent = errMsg;
                        setVerified(false);
                        return;
                    }

                    const vigenteTxt = data.vigente ? '' : ' (fuera de vigencia)';
                    const subjectCN = data?.subject?.CN ? ` – ${data.subject.CN}` : '';
                    msg.style.display = 'block';
                    msg.style.color = data.vigente ? '#155724' : '#721c24';
                    msg.style.background = data.vigente ? '#d4edda' : '#f8d7da';
                    msg.style.border = data.vigente ? '1px solid #c3e6cb' : '1px solid #f5c6cb';
                    msg.style.borderRadius = '6px';
                    msg.style.padding = '0.75rem';
                    msg.textContent = `${data.message}${subjectCN}${vigenteTxt}`;

                    if (data.vigente) {
                        setVerified(true, 'Firma verificada');
                        setTimeout(() => {
                            const modalEl = document.getElementById('firmaModal');
                            if (window.bootstrap && bootstrap.Modal.getInstance) {
                                const instance = bootstrap.Modal.getInstance(modalEl) ||
                                    new bootstrap.Modal(modalEl);
                                instance.hide();
                            } else {
                                modalEl.classList.remove('show');
                                modalEl.style.display = 'none';
                                document.body.classList.remove('modal-open');
                                document.querySelector('.modal-backdrop')?.remove();
                            }
                        }, 600);
                    } else {
                        setVerified(false);
                    }
                } catch (err) {
                    msg.style.display = 'block';
                    msg.style.color = '#721c24';
                    msg.style.background = '#f8d7da';
                    msg.style.border = '1px solid #f5c6cb';
                    msg.style.borderRadius = '6px';
                    msg.style.padding = '0.75rem';
                    msg.textContent = 'Error de red o del servidor al validar la firma.';
                    setVerified(false);
                } finally {
                    btnValidar.disabled = false;
                    btnValidar.innerHTML =
                        '<i class="fas fa-check" style="margin-right: 0.5rem;"></i>Validar firma';
                }
            });

            archivoP12.addEventListener('change', () => setVerified(false));
            passP12.addEventListener('input', () => setVerified(false));

            const modalEl = document.getElementById('firmaModal');
            modalEl.addEventListener('hidden.bs.modal', () => {
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            });
        });
    </script>
@endpush