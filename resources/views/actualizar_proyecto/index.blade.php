@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('styles/archivo.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="main-container">
  @include('menu_general.menu_general')

  <div class="archivo-wrapper">
    <div class="soca-container">
      <div class="header-with-button">
        <h1>Listado de Proyectos</h1>
      </div>

      <form method="GET" action="{{ route('admin.proyectos.index') }}" class="summary-info" style="gap:.75rem;">
        <!-- Imprimir -->
        <div class="left-actions">
          <button type="button" class="print-button" onclick="window.print()">
            <i class="fas fa-print"></i> Imprimir
          </button>
        </div>

        <!-- Registros por página -->
        <div class="records-selector">
          <i class="fas fa-list-ol"></i>
          <span>Mostrar</span>
          <select name="per_page" class="records-select" onchange="this.form.submit()">
            @foreach([10,20,25] as $opt)
              <option value="{{ $opt }}" {{ (int)($perPage ?? 10) === $opt ? 'selected' : '' }}>
                {{ $opt }}
              </option>
            @endforeach
          </select>
          <span>registros</span>
        </div>

        <!-- Búsqueda -->
        <div class="right-actions">
          <div class="search-box">
            <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Buscar por código, nombre o actividad...">
            <button class="search-button"><i class="fas fa-search"></i></button>
          </div>
        </div>
      </form>

      <div class="tramites-table-container">
        <table class="tramites-table">
          <thead>
            <tr>
              <th>Código</th>
              <th>Nombre del Proyecto</th>
              <th>Fecha de Registro</th>
              <th>Actividad</th>
            </tr>
          </thead>
          <tbody>
            @forelse($proyectos as $p)
              <tr>
                <td>
                  <a href="{{ route('admin.proyectos.edit', $p->id_proyecto) }}" class="link">
                    HGAD-PCH-PRO-{{ $p->id_proyecto }}
                  </a>
                </td>
                <td>{{ $p->nombre }}</td>
                <td>
                  @php
                    // fecha_creacion puede ser string; normalizamos
                    $fc = $p->fecha_creacion ?? null;
                    try { $fc = $fc ? \Illuminate\Support\Carbon::parse($fc)->format('Y-m-d') : '—'; }
                    catch (\Exception $e) { $fc = is_string($fc) ? substr($fc,0,10) : '—'; }
                  @endphp
                  {{ $fc }}
                </td>
                <td>{{ $p->actividad ?? '—' }}</td>
              </tr>
            @empty
              <tr class="empty-row">
                <td colspan="10">No hay proyectos disponibles</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div style="margin-top:10px;">
        {{ $proyectos->links() }}
      </div>
    </div>
  </div>
</div>

<script src="{{ asset('js/archivo.js') }}"></script>
@endsection
