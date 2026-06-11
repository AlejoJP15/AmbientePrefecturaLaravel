@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!--
<style>
    
    .page-wrapper {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        min-height: 80vh;
    }

    h1 {
        color: #2c3e50;
        margin-bottom: 30px;
        font-size: 28px;
        border-bottom: 3px solid #667eea;
        padding-bottom: 15px;
        font-weight: 600;
    }

    .formatos-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }

    .formatos-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .formatos-table th {
        padding: 16px 20px;
        text-align: left;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 0.5px;
    }

    .formatos-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        color: #333;
        font-size: 14px;
    }

    .formatos-table tbody tr {
        transition: all 0.3s ease;
    }

    .formatos-table tbody tr:hover {
        background-color: #f8f9ff;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }

    .formatos-table tbody tr:last-child td {
        border-bottom: none;
    }

    .btn-editar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 10px 24px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(102, 126, 234, 0.3);
    }

    .btn-editar:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        text-decoration: none;
        color: white;
    }

    .btn-editar i {
        font-size: 15px;
    }

    .badge-actualizado {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        margin-left: 8px;
        box-shadow: 0 2px 5px rgba(40, 167, 69, 0.3);
    }

    .badge-pendiente {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: #333;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        margin-left: 8px;
        box-shadow: 0 2px 5px rgba(255, 193, 7, 0.3);
    }

    .fecha-col {
        color: #666;
        font-size: 13px;
    }

    .descripcion-col {
        font-weight: 500;
        color: #2c3e50;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .formatos-table {
            font-size: 12px;
        }

        .formatos-table th,
        .formatos-table td {
            padding: 12px 10px;
        }

        .btn-editar {
            padding: 8px 16px;
            font-size: 12px;
        }
    }
</style>
-->
<style>
    body {
        background: #f5f6fa;
        font-family: "Segoe UI", Roboto, sans-serif;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        margin: 0;
        padding: 40px 0;
    }

    .page-wrapper {
        background: white;
        border-radius: 15px;
        padding: 25px 30px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        width: 90%;
        max-width: 1000px; /* Evita que ocupe toda la pantalla */
        margin: 0 auto;
    }

    h1 {
        color: #2c3e50;
        margin-bottom: 25px;
        font-size: 26px;
        border-bottom: 3px solid #667eea;
        padding-bottom: 10px;
        font-weight: 600;
        text-align: center;
    }

    .formatos-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .formatos-table thead {
        background: linear-gradient(135deg, #667eea 0%, #2c18e4ff 100%);
        color: white;
    }

    .formatos-table th {
        padding: 12px 18px;
        text-align: left;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 0.5px;
    }

    .formatos-table td {
        padding: 12px 18px;
        border-bottom: 1px solid #f0f0f0;
        color: #333;
        font-size: 14px;
    }

    .formatos-table tbody tr {
        transition: all 0.3s ease;
    }

    .formatos-table tbody tr:hover {
        background-color: #f8f9ff;
        transform: translateX(3px);
        box-shadow: 0 1px 6px rgba(102, 126, 234, 0.1);
    }

    .btn-editar {
        background: linear-gradient(135deg, #667eea 0%, #2c18e4ff 100%);
        color: white;
        padding: 8px 18px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(102, 126, 234, 0.3);
    }

    .btn-editar:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        color: white;
    }

    .badge-actualizado {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        margin-left: 6px;
        box-shadow: 0 2px 5px rgba(40, 167, 69, 0.25);
    }

    .badge-pendiente {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: #333;
        padding: 4px 10px;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
        margin-left: 6px;
        box-shadow: 0 2px 5px rgba(255, 193, 7, 0.25);
    }

    .fecha-col {
        color: #666;
        font-size: 13px;
    }

    .descripcion-col {
        font-weight: 500;
        color: #2c3e50;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-wrapper {
            width: 95%;
            padding: 20px;
        }

        h1 {
            font-size: 22px;
        }

        .formatos-table th,
        .formatos-table td {
            padding: 10px;
            font-size: 12px;
        }

        .btn-editar {
            padding: 6px 12px;
            font-size: 12px;
        }
    }
</style>


<div class="main-container">
    {{-- Menú lateral --}}
    @include('menu_general.menu_general')
    <br>
    <div class="page-wrapper">
        <h1><i class="fas fa-file-alt" style="margin-right: 10px;"></i>Formatos Para Informe, Memo Y Oficio</h1>

        <table class="formatos-table">
            <thead>
                <tr>
                    <th style="width: 40%;">DESCRIPCIÓN</th>
                    <th style="width: 35%;">ÚLTIMA ACTUALIZACIÓN</th>
                    <th style="text-align: center; width: 25%;">ACCIÓN</th>
                </tr>
            </thead>
            <tbody>
                <!-- INFORMES -->
                <tr>
                    <td class="descripcion-col">
                        <i class="fas fa-file-contract" style="color: #667eea; margin-right: 8px;"></i>
                        Informe aprobación
                    </td>
                    <td class="fecha-col">
                        @php
                            $infAprobacion = $formatos->where('tipo_documento', 'informe_aprobacion')->first();
                        @endphp
                        @if($infAprobacion && $infAprobacion->updated_at)
                            {{ $infAprobacion->updated_at->format('d/m/Y H:i') }}
                            <span class="badge-actualizado">Actualizado</span>
                        @else
                            <span class="badge-pendiente">Sin configurar</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('formatos.aprobacion') }}" class="btn-editar">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>

                <tr>
                    <td class="descripcion-col">
                        <i class="fas fa-file-contract" style="color: #667eea; margin-right: 8px;"></i>
                        Informe observado
                    </td>
                    <td class="fecha-col">
                        @php
                            $infObservado = $formatos->where('tipo_documento', 'informe_observado')->first();
                        @endphp
                        @if($infObservado && $infObservado->updated_at)
                            {{ $infObservado->updated_at->format('d/m/Y H:i') }}
                            <span class="badge-actualizado">Actualizado</span>
                        @else
                            <span class="badge-pendiente">Sin configurar</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('formatos.observado') }}" class="btn-editar">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>

                <tr>
                    <td class="descripcion-col">
                        <i class="fas fa-file-contract" style="color: #667eea; margin-right: 8px;"></i>
                        Informe pronunciamiento favorable
                    </td>
                    <td class="fecha-col">
                        @php
                            $infPron = $formatos->where('tipo_documento', 'informe_pronunciamiento')->first();
                        @endphp
                        @if($infPron && $infPron->updated_at)
                            {{ $infPron->updated_at->format('d/m/Y H:i') }}
                            <span class="badge-actualizado">Actualizado</span>
                        @else
                            <span class="badge-pendiente">Sin configurar</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('formatos.infpronunciamiento') }}" class="btn-editar">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>

                <!-- MEMOS -->
                <tr>
                    <td class="descripcion-col">
                        <i class="fas fa-sticky-note" style="color: #764ba2; margin-right: 8px;"></i>
                        Memo aprobación
                    </td>
                    <td class="fecha-col">
                        @php
                            $memoApro = $formatos->where('tipo_documento', 'memo_aprobacion')->first();
                        @endphp
                        @if($memoApro && $memoApro->updated_at)
                            {{ $memoApro->updated_at->format('d/m/Y H:i') }}
                            <span class="badge-actualizado">Actualizado</span>
                        @else
                            <span class="badge-pendiente">Sin configurar</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('formatos.memoaprobacion') }}" class="btn-editar">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>

                <tr>
                    <td class="descripcion-col">
                        <i class="fas fa-sticky-note" style="color: #764ba2; margin-right: 8px;"></i>
                        Memo observado
                    </td>
                    <td class="fecha-col">
                        @php
                            $memoObs = $formatos->where('tipo_documento', 'memo_observado')->first();
                        @endphp
                        @if($memoObs && $memoObs->updated_at)
                            {{ $memoObs->updated_at->format('d/m/Y H:i') }}
                            <span class="badge-actualizado">Actualizado</span>
                        @else
                            <span class="badge-pendiente">Sin configurar</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('formatos.memoobservado') }}" class="btn-editar">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>

                <tr>
                    <td class="descripcion-col">
                        <i class="fas fa-sticky-note" style="color: #764ba2; margin-right: 8px;"></i>
                        Memo pronunciamiento favorable
                    </td>
                    <td class="fecha-col">
                        @php
                            $memoPron = $formatos->where('tipo_documento', 'memo_pronunciamiento')->first();
                        @endphp
                        @if($memoPron && $memoPron->updated_at)
                            {{ $memoPron->updated_at->format('d/m/Y H:i') }}
                            <span class="badge-actualizado">Actualizado</span>
                        @else
                            <span class="badge-pendiente">Sin configurar</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('formatos.memopronunciamiento') }}" class="btn-editar">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>

                <!-- OFICIOS -->
                <tr>
                    <td class="descripcion-col">
                        <i class="fas fa-file-signature" style="color: #28a745; margin-right: 8px;"></i>
                        Oficio aprobación
                    </td>
                    <td class="fecha-col">
                        @php
                            $ofiApro = $formatos->where('tipo_documento', 'oficio_aprobacion')->first();
                        @endphp
                        @if($ofiApro && $ofiApro->updated_at)
                            {{ $ofiApro->updated_at->format('d/m/Y H:i') }}
                            <span class="badge-actualizado">Actualizado</span>
                        @else
                            <span class="badge-pendiente">Sin configurar</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('formatos.ofiaprobacion') }}" class="btn-editar">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>

                <tr>
                    <td class="descripcion-col">
                        <i class="fas fa-file-signature" style="color: #28a745; margin-right: 8px;"></i>
                        Oficio observado
                    </td>
                    <td class="fecha-col">
                        @php
                            $ofiObs = $formatos->where('tipo_documento', 'oficio_observado')->first();
                        @endphp
                        @if($ofiObs && $ofiObs->updated_at)
                            {{ $ofiObs->updated_at->format('d/m/Y H:i') }}
                            <span class="badge-actualizado">Actualizado</span>
                        @else
                            <span class="badge-pendiente">Sin configurar</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('formatos.ofiobservado') }}" class="btn-editar">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>

                <tr>
                    <td class="descripcion-col">
                        <i class="fas fa-file-signature" style="color: #28a745; margin-right: 8px;"></i>
                        Oficio pronunciamiento favorable
                    </td>
                    <td class="fecha-col">
                        @php
                            $ofiPron = $formatos->where('tipo_documento', 'oficio_pronunciamiento')->first();
                        @endphp
                        @if($ofiPron && $ofiPron->updated_at)
                            {{ $ofiPron->updated_at->format('d/m/Y H:i') }}
                            <span class="badge-actualizado">Actualizado</span>
                        @else
                            <span class="badge-pendiente">Sin configurar</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('formatos.ofipronunciamiento') }}" class="btn-editar">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>

                <!-- RESOLUCIÓN -->
                <tr>
                    <td class="descripcion-col">
                        <i class="fas fa-gavel" style="color: #dc3545; margin-right: 8px;"></i>
                        Resolución
                    </td>
                    <td class="fecha-col">
                        @php
                            $resolucion = $formatos->where('tipo_documento', 'resolucion')->first();
                        @endphp
                        @if($resolucion && $resolucion->updated_at)
                            {{ $resolucion->updated_at->format('d/m/Y H:i') }}
                            <span class="badge-actualizado">Actualizado</span>
                        @else
                            <span class="badge-pendiente">Sin configurar</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <a href="{{ route('formatos.resolucion') }}" class="btn-editar">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection