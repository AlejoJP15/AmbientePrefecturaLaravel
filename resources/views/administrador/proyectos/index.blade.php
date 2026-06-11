@extends('layouts.app')

<link rel="stylesheet" href="{{ asset('styles/archivo.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

@section('content')
<div class="main-container">

    {{-- Menú lateral --}}
    @include('menu_general.menu_general')

    <div class="archivo-wrapper">
        <div class="soca-container">
            
            <div class="header-with-button">
                <h2>Listado de Proyectos Registrados</h2>
            </div>

            <div class="tramites-table-container">
                <table class="tramites-table">
                    <thead>
                        <tr>
                            <th>CÓDIGO</th>
                            <th>NOMBRE DEL PROYECTO</th>
                            <th>FECHA REGISTRO</th>
                            <th>SECTOR</th>
                            <th>ACTIVIDAD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($proyectos as $p)
                            <tr>
                                <td>
                                    <a href="{{ route('usuario.proyectos.show', $p->id_proyecto) }}">
                                        HGADPCH-PRO-{{ $p->id_proyecto }}
                                    </a>
                                </td>
                                <td>{{ $p->nombre ?? '—' }}</td>
                                <td>
                                    {{ optional($p->fecha_creacion)->format('Y-m-d H:i:s') ?? '—' }}
                                </td>
                                <td>{{ $p->sector ?? '—' }}</td>
                                <td>{{ optional($p->actividad)->descripcion_actividad ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="5">
                                    No hay proyectos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($proyectos, 'links'))
                <div class="pagination-container">
                    {{ $proyectos->links() }}
                </div>
            @endif

        </div>
    </div>

</div>
@endsection
