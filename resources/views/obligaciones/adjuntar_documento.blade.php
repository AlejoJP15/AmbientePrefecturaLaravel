@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('styles/obl-proyecto.css') }}">

@section('content')
@if(session('success'))
  <div class="msg msg--ok">
    {{ session('success') }}
  </div>
@endif

@if(session('error'))
  <div class="msg msg--error">
    {{ session('error') }}
  </div>
@endif

@php
  $trim = " \t\n\r\0\x0B\"'“”‘’«»‹›„‚";
  $permiso = $proyecto->getRawOriginal('tipo_permiso');
  $estudio = $proyecto->getRawOriginal('tipo_estudio');
  $permiso = $permiso !== null ? trim((string)$permiso, $trim) : null;
  $estudio = $estudio !== null ? trim((string)$estudio, $trim) : null;
@endphp

<div class="main-container">

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
    @include('menu_general.menu_general_usuario')
  @endif

  <div class="obl-wrapper">
  {{-- Contenido principal --}}
  <div class="obl-content">
    @if (session('ok'))
      <div class="msg msg--ok">
        {{ session('ok') }}
      </div>
    @endif

    <h2>Resumen del proyecto</h2>

    <div class="obl-grid">
      {{-- Cabecera: Actividad y nombre --}}
      <div class="obl-card">
        <div class="obl-label">Actividad</div>
        <div class="obl-value">
          {{ optional($proyecto->actividad)->descripcion_actividad ?: '—' }}
        </div>

        <div class="obl-label mt">Nombre del proyecto</div>
        <div class="obl-value">{{ $proyecto->nombre }}</div>
      </div>

      {{-- Detalles --}}
      <div class="obl-card">
        <div class="obl-detail-grid">
          <div class="obl-key">Descripción</div>
          <div>{{ $proyecto->resumen ?: '—' }}</div>

          <div class="obl-key">Tipo de permiso</div>
          <div>{{ $permiso !== null && $permiso !== '' ? $permiso : '—' }}</div>

          <div class="obl-key">Tipo de estudio</div>
          <div>{{ $estudio !== null && $estudio !== '' ? $estudio : '—' }}</div>

          <div class="obl-key">Provincia/Cantón/Parroquia</div>
          <div>
            {{ optional($proyecto->provincia)->provincia ?? '—' }} /
            {{ optional($proyecto->canton)->canton ?? '—' }} /
            {{ optional($proyecto->parroquia)->parroquia ?? '—' }}
          </div>

          <div class="obl-key">Dirección</div>
          <div>{{ $proyecto->direccion ?: '—' }}</div>

          <div class="obl-key">Fecha de creación</div>
          <div>{{ optional($proyecto->fecha_creacion)->format('Y-m-d') }}</div>
        </div>
      </div>
    </div>

    {{-- Subir PDF --}}
    @unless($bloqueado)
      <form method="POST" action="{{ route('obligaciones.documentos.store', $obligacion->id_obligacion) }}" enctype="multipart/form-data" class="obl-upload">
        @csrf
        <input type="file" name="archivos[]" accept="application/pdf" multiple>
        <button type="submit" class="obl-btn">Subir ⬆️</button>
        @error('archivos')<div class="obl-error">{{ $message }}</div>@enderror
        @error('archivos.*')<div class="obl-error">{{ $message }}</div>@enderror
      </form>
    @endunless

    {{-- Tabla de archivos --}}
    <div class="obl-table">
      <table>
        <thead>
          <tr>
            <th>TÍTULO</th>
            <th>TIPO</th>
            <th>FECHA CREACIÓN</th>
            <th>ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @forelse($files as $f)
            <tr>
              <td>{{ $f['name'] }}</td>
              <td>{{ $f['tipo'] }}</td>
              <td>{{ $f['created_at'] }}</td>
              <td class="obl-actions-cell">
                {{-- Descargar --}}
                <a href="{{ route('obligaciones.documentos.download', $obligacion->id_obligacion) }}?file={{ urlencode($f['path']) }}" class="obl-btn obl-btn--sm">
                  Descargar
                </a>

                @unless($bloqueado)
                  {{-- Eliminar --}}
                  <form method="POST" action="{{ route('obligaciones.documentos.destroy', $obligacion->id_obligacion) }}" onsubmit="return confirm('¿Eliminar este archivo?');" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="file" value="{{ $f['path'] }}">
                    <button type="submit" class="obl-btn obl-btn--danger obl-btn--sm">Eliminar</button>
                  </form>
                @endunless
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="obl-empty">Aún no hay archivos.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Comentarios --}}
    @unless($bloqueado)
      <form method="POST" action="{{ route('obligaciones.comentarios.update', $obligacion->id_obligacion) }}" class="obl-comentarios">
        @csrf
        @method('PUT')
        <label><strong>Comentarios adicionales</strong></label>
        <textarea name="resumen" rows="4">{{ old('resumen', $obligacion->resumen) }}</textarea>
        <div class="obl-btns">
          <button type="submit" class="obl-btn">Enviar</button>
          <a href="{{ route('usuario.proyectos.show', $proyecto->id_proyecto) }}" class="obl-btn obl-btn--ghost">Volver al proyecto</a>
        </div>
      </form>
    @else
      <div class="obl-btns">
        <a href="{{ route('usuario.proyectos.show', $proyecto->id_proyecto) }}" class="obl-btn obl-btn--ghost">Volver al proyecto</a>
      </div>
    @endunless
  </div>
</div>
@endsection
