@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('styles/archivo.css') }}">
@push('styles')
<link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
<style>
  /* 👇 Solución: darle tamaño al mapa */
  #mapShow {
    width: 100%;
    height: 300px; /* puedes subirlo si quieres más alto */
    border-radius: 8px;
  }
</style>
@endpush

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
    @include('menu_general.menu_general')            {{-- menú para admin --}}
  @else
    @include('menu_general.menu_general_usuario')    {{-- menú para usuario --}}
  @endif

    <div class="archivo-wrapper">
        <div class="soca-container">
            <div class="header-with-button">
                <h1>Resumen del proyecto</h1>
            </div>
    <div style="display:flex; gap:1.25rem; align-items:flex-start;">

        
        {{-- Contenido principal --}}
        <div style="flex:1;">
            @if (session('ok'))
            <div style="margin:.5rem 0; padding:.6rem; background:#ecfdf5; border:1px solid #a7f3d0; border-radius:.4rem; color:#065f46;">
                {{ session('ok') }}
            </div>
            @endif

            <div style="display:grid; gap:1rem;">

            {{-- Cabecera: Actividad y nombre --}}
            <div style="border:1px solid #e5e7eb; border-radius:.6rem; padding:1rem; background:#fff;">
                <div style="font-weight:600; color:#374151;">Actividad</div>
                <div style="margin-top:.25rem;">
                {{ optional($proyecto->actividad)->descripcion_actividad ?: '—' }}
                </div>

                <div style="margin-top:.75rem; font-weight:600; color:#374151;">Nombre del proyecto</div>
                <div style="margin-top:.25rem;">{{ $proyecto->nombre }}</div>
            </div>

            {{-- Detalles --}}
            <div style="border:1px solid #e5e7eb; border-radius:.6rem; padding:1rem; background:#fff;">
                <div style="display:grid; grid-template-columns:1fr 2fr; gap:.5rem .75rem;">
                <div style="color:#6b7280;">Descripción</div>
                <div>{{ $proyecto->resumen ?: '—' }}</div>

                <div style="color:#6b7280;">Tipo de permiso</div>
                <div>{{ $permiso !== null && $permiso !== '' ? $permiso : '—' }}</div>

                <div style="color:#6b7280;">Tipo de estudio</div>
                <div>{{ $estudio !== null && $estudio !== '' ? $estudio : '—' }}</div>

                <div style="color:#6b7280;">Provincia/Cantón/Parroquia</div>
                <div>
                {{ optional($proyecto->provincia)->provincia ?? '—' }}
                / {{ optional($proyecto->canton)->canton ?? '—' }}
                / {{ optional($proyecto->parroquia)->parroquia ?? '—' }}
                </div>

                <div style="color:#6b7280;">Dirección</div>
                <div>{{ $proyecto->direccion ?: '—' }}</div>

                <div style="color:#6b7280;">Fecha de creación</div>
                <div>{{ optional($proyecto->fecha_creacion)->format('Y-m-d') }}</div>
                </div>
            </div>

            {{-- Acciones --}}
            <div style="display:flex; gap:.5rem;">
                <a href="{{ route('usuario.proyectos.create') }}"
                style="padding:.6rem .9rem; background:#2563eb; color:#fff; border-radius:.5rem; text-decoration:none;">
                Crear otro proyecto
                </a>
            </div>
            {{-- Columna lateral con el mapa --}}
            <div style="width:420px; position:sticky; top:1rem;">
                <div style="border:1px solid #e5e7eb; border-radius:.6rem; padding:1rem; background:#fff;">
                    <div style="font-weight:600; color:#374151; margin-bottom:.5rem;">Mapa del proyecto</div>
                    <div id="mapShow"></div>
                    <div style="font-size:.875rem; color:#6b7280; margin-top:.5rem;">
                    Se muestran los puntos P1..Pn y el polígono conectado.
                    </div>
                </div>
            </div>
        </div>



        {{-- === Observaciones (comentarios) === --}}
        <div style="margin-top:16px; margin-bottom:16px;">
          <h3 style="margin:0 0 8px;">Observaciones</h3>

          @if(isset($comentarios) && $comentarios->count())
            <ul style="list-style:none; padding:0; margin:0;">
              @foreach($comentarios as $c)
                <li style="background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:.75rem; margin-bottom:.5rem;">
                  <div style="font-size:.95rem; color:#111827;">{{ $c->descripcion }}</div>
                  <div style="font-size:.8rem; color:#6b7280; margin-top:.35rem;">
                    <i class="fas fa-clock"></i>
                    {{ \Carbon\Carbon::parse($c->fecha_comentario)->format('d/m/Y H:i') }}
                  </div>
                </li>
              @endforeach
            </ul>
          @else
            <div style="padding:.6rem; border:1px dashed #bbb; color:#555; border-radius:8px;">
              No hay observaciones.
            </div>
          @endif
        </div>
        {{-- === /Observaciones === --}}








        {{-- === Obligaciones === --}}
        <div style="margin-bottom:16px;">
          <h3 style="margin:0 0 8px;">Obligaciones</h3>

          @if(isset($obligaciones) && $obligaciones->count())
            <div style="overflow:auto;">
              <table style="width:100%; border-collapse:collapse;">
                <thead>
                  <tr>
                    <th style="border:1px solid #ccc; padding:6px; background:#f7f7f7;"></th>
                    <th style="border:1px solid #ccc; padding:6px; background:#f7f7f7;">Código</th>
                    <th style="border:1px solid #ccc; padding:6px; background:#f7f7f7;">Tipo de documento</th>
                    <th style="border:1px solid #ccc; padding:6px; background:#f7f7f7;">Periodo</th>
                    <th style="border:1px solid #ccc; padding:6px; background:#f7f7f7;">Estado</th>
                    <th style="border:1px solid #ccc; padding:6px; background:#f7f7f7;">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($obligaciones as $o)
                    <tr>
                      <td style="border:1px solid #ccc; padding:6px;">
                        <a href="{{ route('obligaciones.documentos', $o->id_obligacion) }}"
                          style="display:inline-block; margin-bottom:4px; font-size:.9rem; text-decoration:underline;">
                          Adjuntar documentos
                        </a><br>
                      </td>
                      <td style="border:1px solid #ccc; padding:6px;">HGADPCH-DOC-{{ $o->id_obligacion }}</td>
                      <td style="border:1px solid #ccc; padding:6px;">{{ $o->descripcion }}</td>
                      <td style="border:1px solid #ccc; padding:6px;">{{ $o->periodo ?? '—' }}</td>
                      <td style="border:1px solid #ccc; padding:6px;">{{ $o->estado }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div style="padding:8px; border:1px dashed #bbb; color:#555; border-radius:8px;">
              Aún no hay obligaciones registradas.
            </div>
          @endif
        </div>
        {{-- === /Obligaciones === --}}

        {{-- ===== BOTÓN: Tipo obligación + ===== --}}
          <div style="display:flex; justify-content:flex-end; margin-bottom:.75rem;">
            <button type="button" onclick="window.oblOpen()" 
                    style="border:1px solid transparent; background:#2563eb; color:#fff; border-radius:.5rem;
                          padding:.4rem .65rem; font-size:.85rem; cursor:pointer;">
              Tipo obligación <span style="font-weight:700;">+</span>
            </button>
          </div>
        {{-- Backdrop (oscurece) --}}
          <div id="oblBackdrop" class="obl-modal-backdrop" aria-hidden="true"></div>

          {{-- Modal --}}
          <div id="oblModal" class="obl-modal" role="dialog" aria-modal="true" aria-labelledby="oblTitle" aria-hidden="true">
            <div class="obl-modal__panel">
              <div class="obl-modal__header">
                <style>
  /* ==== CONTENEDOR PRINCIPAL ==== */
  .obl-card {
    max-width: 600px;
    margin: 2rem auto;
    background: #f9fafb;
    border-radius: 1rem;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    border: 1px solid #e5e7eb;
    animation: fadeIn .4s ease-in-out;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
  }

  /* ==== CABECERA ==== */
  .obl-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: white;
    padding: 1rem 1.25rem;
    font-size: 1.1rem;
    font-weight: 600;
    border-bottom: 3px solid #3b82f6;
  }

  .obl-btn-close {
    background: transparent;
    border: none;
    color: white;
    font-size: 1.4rem;
    cursor: pointer;
    transition: 0.2s;
  }

  .obl-btn-close:hover {
    color: #dbeafe;
  }

  /* ==== CUERPO DEL FORM ==== */
  .obl-modal__body {
    padding: 1.25rem 1.5rem;
  }

  .obl-row {
    margin-bottom: 1rem;
  }

  .obl-row label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: .3rem;
  }

  .obl-row select,
  .obl-row input[type="date"] {
    width: 100%;
    padding: .5rem .75rem;
    border: 1px solid #d1d5db;
    border-radius: .5rem;
    background: white;
    transition: border-color .2s, box-shadow .2s;
  }

  .obl-row select:focus,
  .obl-row input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px #bfdbfe;
    outline: none;
  }

  /* ==== PIE (BOTONES) ==== */
  .obl-modal__footer {
    padding: 1rem 1.5rem;
    background: #f3f4f6;
    border-top: 1px solid #e5e7eb;
    text-align: right;
  }

  .obl-actions {
    display: flex;
    justify-content: flex-end;
    gap: .75rem;
  }

  .obl-btn {
    padding: .6rem 1.2rem;
    border-radius: .5rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all .2s ease-in-out;
  }

  .obl-btn--ghost {
    background: #e5e7eb;
    color: #374151;
  }

  .obl-btn--ghost:hover {
    background: #d1d5db;
  }

  .obl-btn {
    background: linear-gradient(135deg, #2563eb, #1e40af);
    color: white;
  }

  .obl-btn:hover {
    background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
  }
</style>

<div class="obl-card">
  <div class="obl-header">
    <div class="obl-modal__title" id="oblTitle">Nueva obligación</div>
    <button type="button" class="obl-btn-close" id="btnCerrarObl" aria-label="Cerrar">×</button>
  </div>

  <form class="obl-form" method="POST" action="{{ route('obligaciones.store', ($proyecto->id ?? $proyecto->id_proyecto)) }}">
    @csrf

    <div class="obl-modal__body">
      {{-- Errores --}}
      @if ($errors->any())
        <div style="padding:.5rem; background:#fee2e2; border:1px solid #fecaca; color:#991b1b; border-radius:.5rem; margin-bottom:.5rem;">
          <ul style="margin:0; padding-left:1rem;">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- Tipo --}}
      <div class="obl-row">
        <label>Tipo <span style="color:#dc2626">*</span></label>
        <select name="id_tipo" id="oblTipo" required>
          <option value="">Cargando…</option>
        </select>
        <input type="hidden" name="tipo_obligacion" id="oblTipoTexto" value="{{ old('tipo_obligacion') }}">
      </div>

      {{-- Ítem / Documento --}}
      <div class="obl-row" id="oblGrupoSeleccione">
        <label>Seleccione <span style="color:#dc2626">*</span></label>
        <select name="id_item" id="oblDescripcion" required disabled>
          <option value="">Seleccione…</option>
        </select>
        <input type="hidden" name="descripcion" id="oblDescTexto" value="{{ old('descripcion') }}">
      </div>

      {{-- Periodo --}}
      <div class="obl-row" id="oblGrupoPeriodo" style="display:none;">
        <label>Periodo</label>
        <div style="display:flex; gap:12px; align-items:end;">
          <div>
            <small>Desde</small><br>
            <input type="date" id="oblDesde" name="periodo_desde" value="{{ old('periodo_desde') }}" disabled>
          </div>
          <div>
            <small>Hasta</small><br>
            <input type="date" id="oblHasta" name="periodo_hasta" value="{{ old('periodo_hasta') }}" disabled>
          </div>
        </div>
        <input type="hidden" id="oblPeriodo" name="periodo" value="{{ old('periodo') }}">
      </div>
    </div>

    <div class="obl-modal__footer">
      <div class="obl-actions">
        <button type="button" class="obl-btn obl-btn--ghost" id="btnCancelarObl">Cancelar</button>
        <button type="submit" class="obl-btn">Guardar</button>
      </div>
    </div>
  </form>
</div>


</div>
</div>

@push('scripts')
<script src="{{ asset('leaflet/leaflet.js') }}"></script>
<script>
(function() {
  const puntos = @json($puntos ?? []);            // [{lat,lng,label}, ...]
  const polyGeojsonStr = @json($polyGeojson ?? null); // string JSON o null

  function init() {
    const map = L.map('mapShow', { preferCanvas: true }).setView([-1.67, -78.65], 12); // fallback Riobamba
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const layers = [];

    // 1) Marcadores P1..Pn
    puntos.forEach(p => {
      if (Number.isFinite(p.lat) && Number.isFinite(p.lng)) {
        const m = L.marker([p.lat, p.lng]).addTo(map).bindTooltip(p.label || '', {direction:'top'});
        layers.push(m);
      }
    });

    // 2) Polígono: si existe en DB úsalo; si no, conéctalo con P1..Pn
    if (polyGeojsonStr) {
      try {
        const gj = JSON.parse(polyGeojsonStr);
        layers.push(L.geoJSON(gj, { style: { weight: 2, fillOpacity: 0.1 } }).addTo(map));
      } catch (e) {
        console.warn('GeoJSON inválido del polígono:', e);
      }
    } else if (puntos.length >= 3) {
      const latlngs = puntos.map(p => [p.lat, p.lng]);
      latlngs.push(latlngs[0]); // cerrar anillo
      layers.push(L.polygon(latlngs, { weight: 2, fillOpacity: 0.1 }).addTo(map));
    }

    // 3) Auto-zoom y recalcular tamaño por si el flex/sticky afecta
    setTimeout(() => {
      if (layers.length) {
        const group = L.featureGroup(layers);
        map.fitBounds(group.getBounds().pad(0.2));
      }
      map.invalidateSize();
    }, 0);
  }

  if (document.readyState === 'complete' || document.readyState === 'interactive') {
    init();
  } else {
    document.addEventListener('DOMContentLoaded', init);
  }
})();
</script>

@endpush
@push('scripts')
<script>
(function () {
  // —— Referencias
  const modal      = document.getElementById('oblModal');
  const backdrop   = document.getElementById('oblBackdrop');
  const btnCerrar  = document.getElementById('btnCerrarObl');
  const btnCanc    = document.getElementById('btnCancelarObl');
  const selTipo    = document.getElementById('oblTipo');          // select id_tipo
  const selItem    = document.getElementById('oblDescripcion');   // select id_item
  const tipoTxt    = document.getElementById('oblTipoTexto');     // snapshot texto
  const descTxt    = document.getElementById('oblDescTexto');     // snapshot texto
  const grupoPer   = document.getElementById('oblGrupoPeriodo');
  const inpDesde   = document.getElementById('oblDesde');
  const inpHasta   = document.getElementById('oblHasta');
  const periodoInp = document.getElementById('oblPeriodo');
  const form       = modal ? modal.querySelector('form') : null;

  // —— Olds del backend (para reabrir modal y mantener selección)
  const oldTipoId  = {!! json_encode(old('id_tipo')) !!} || '';
  const oldItemId  = {!! json_encode(old('id_item')) !!} || '';
  const oldPeriodo = {!! json_encode(old('periodo')) !!} || '';

  // — helpers
  const opt = (l,v,s=false)=>`<option value="${v}" ${s?'selected':''}>${l}</option>`;
  function fmtDMY(iso) { if(!iso) return ''; const [y,m,d]=iso.split('-'); return `${d.padStart(2,'0')}/${m.padStart(2,'0')}/${y}`; }

  // —— Abrir / Cerrar
  function abrirModal() {
    if (!modal || !backdrop) return;
    document.body.classList.add('obl-modal-open');
    modal.classList.add('is-open');
    backdrop.classList.add('is-open');
    setTimeout(() => { if (selTipo) selTipo.focus(); }, 30);
  }
  function cerrarModal() {
    if (!modal || !backdrop) return;
    document.body.classList.remove('obl-modal-open');
    modal.classList.remove('is-open');
    backdrop.classList.remove('is-open');
  }
  window.oblOpen = abrirModal;

  if (btnCerrar) btnCerrar.addEventListener('click', cerrarModal);
  if (btnCanc)   btnCanc.addEventListener('click', cerrarModal);
  if (backdrop)  backdrop.addEventListener('click', cerrarModal);
  if (modal) modal.addEventListener('keydown', (e) => { if (e.key === 'Escape') cerrarModal(); });

  // —— Estado en memoria
  // Guardamos los tipos así: { id_tipo, nombre, requiere_periodo }
  let tipos = [];
  // Guardamos los items del tipo seleccionado: { id_item, descripcion }
  let items = [];

  // —— Carga tipos desde backend
  async function cargarTipos() {
    selTipo.innerHTML = opt('Cargando…','');
    try {
      const res = await fetch("{{ route('catalogos.tipos') }}", { headers: {'X-Requested-With':'XMLHttpRequest'} });
      tipos = await res.json(); // [{id_tipo,nombre,requiere_periodo}]
      selTipo.innerHTML = opt('Seleccione…','');
      tipos.forEach(t => {
        selTipo.insertAdjacentHTML('beforeend', opt(t.nombre, t.id_tipo, String(t.id_tipo)===String(oldTipoId)));
      });
      if (oldTipoId) await onTipoChange(); // para restaurar items y periodo si aplica
      // Si hubo old periodo y el tipo lo requiere, rehidratar
      if (oldPeriodo && grupoPer.style.display !== 'none') periodoInp.value = oldPeriodo;
    } catch(err) {
      selTipo.innerHTML = opt('Error al cargar','');
      console.error(err);
    }
  }

  // —— Al cambiar Tipo: cargar items y manejar periodo según requiere_periodo (del tipo)
  async function onTipoChange() {
    const idTipo = selTipo.value;
    tipoTxt.value = selTipo.options[selTipo.selectedIndex]?.text || '';
    selItem.innerHTML = opt('Seleccione…',''); selItem.disabled = true;
    items = [];

    // periodo: por defecto oculto hasta verificar el tipo
    grupoPer.style.display = 'none';
    inpDesde.disabled = true; inpHasta.disabled = true;
    inpDesde.value = ''; inpHasta.value = ''; periodoInp.value = '';

    if (!idTipo) return;

    // Mostrar periodo si el tipo lo requiere
    const t = tipos.find(x => String(x.id_tipo) === String(idTipo));
    const requiere = !!(t && t.requiere_periodo);
    grupoPer.style.display = requiere ? '' : 'none';
    inpDesde.disabled = !requiere; inpHasta.disabled = !requiere;

    // Cargar items
    try {
      const res = await fetch("{{ route('catalogos.items') }}?id_tipo="+encodeURIComponent(idTipo), { headers: {'X-Requested-With':'XMLHttpRequest'} });
      items = await res.json(); // [{id_item,descripcion}]
      selItem.disabled = false;
      selItem.innerHTML = opt('Seleccione…','');
      items.forEach(it => {
        selItem.insertAdjacentHTML('beforeend', opt(it.descripcion, it.id_item, String(it.id_item)===String(oldItemId)));
      });
      if (oldItemId) onItemChange(); // restaura descripcion snapshot
    } catch(err) {
      console.error(err);
    }
  }

  // —— Al cambiar Ítem: snapshot de texto y (si quisieras granularidad) podrías decidir periodo por ítem
  function onItemChange() {
    descTxt.value = selItem.options[selItem.selectedIndex]?.text || '';
    // Si manejas requiere_periodo a nivel ÍTEM, trae ese flag en el JSON y decide aquí
    // (Actualmente lo manejamos por TIPO)
  }

  // —— Antes de enviar: armar "periodo" y asegurar snapshots texto
  function onSubmit() {
    // Snapshot de textos (por compatibilidad con tu tabla actual)
    tipoTxt.value = selTipo.options[selTipo.selectedIndex]?.text || '';
    descTxt.value = selItem.options[selItem.selectedIndex]?.text || '';

    // Periodo "dd/mm/yyyy - dd/mm/yyyy" sólo si el grupo está visible
    if (grupoPer.style.display !== 'none' && inpDesde.value && inpHasta.value) {
      periodoInp.value = `${fmtDMY(inpDesde.value)} - ${fmtDMY(inpHasta.value)}`;
    } else {
      periodoInp.value = '';
    }
  }

  // —— Init
  function init() {
    if (selTipo) selTipo.addEventListener('change', onTipoChange, { passive: true });
    if (selItem) selItem.addEventListener('change', onItemChange, { passive: true });

    // Reabrir modal si hubo errores de validación
    @if ($errors->any())
      abrirModal();
    @endif

    if (form) form.addEventListener('submit', onSubmit);

    cargarTipos();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }
})();
</script>



@endpush

@endsection




