@extends('layouts.app')

@section('title', 'Editar proyecto')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
<link rel="stylesheet" href="{{ asset('styles/proyecto-form.css') }}">
<style>
  #mapPicker { height: 420px; width:80%; border:1px solid #e5e7eb; border-radius:.5rem; }
  /* Si tienes un global `img { max-width: 100% }`, rompe Leaflet; neutraliza sólo dentro del mapa */
  #mapPicker img { max-width: none !important; }
  .pt-label {
    background:#fff; border:1px solid #e5e7eb; padding:2px 6px; border-radius:4px; font-size:12px;
    box-shadow:0 1px 2px rgba(0,0,0,.05);
  }
</style>
@endpush

<div class="main-container">
  @include('menu_general.menu_general')

  <div class="archivo-wrapper">
    <div class="soca-container">
      <div class="header-with-button">
        <h1>Editar proyecto</h1>
        <a href="{{ route('admin.proyectos.index') }}" class="btn btn-outline-secondary btn-sm">← Volver al listado</a>
      </div>

      @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      <form id="formProyecto" method="POST" action="{{ route('admin.proyectos.update', $proyecto->id_proyecto) }}">
        @csrf
        @method('PUT')

        <div class="grid" style="display:grid; grid-template-columns: 1fr 420px; gap: 16px;">
          {{-- Columna principal --}}
          <div>
             {{-- ACTIVIDAD (lista estática) --}}
            <div style="margin-bottom:1rem;">
              <label class="form-label">Actividad <span class="text-danger">*</span></label>
              <select name="id_actividad" class="form-select" required>
                <option value="">Seleccione actividad…</option>
                @foreach($actividades as $a)
                  <option value="{{ $a->id_actividad }}"
                    {{ (int)old('id_actividad', $proyecto->id_actividad) === (int)$a->id_actividad ? 'selected' : '' }}>
                    {{ $a->descripcion_actividad }}
                  </option>
                @endforeach
              </select>
              @error('id_actividad')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

              {{-- NOMBRE --}}
            <div style="margin-bottom:1rem;">
              <label class="form-label">Nombre del proyecto, obra o actividad <span class="text-danger">*</span></label>
              <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $proyecto->nombre) }}" required>
              @error('nombre')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            {{-- DESCRIPCION / RESUMEN --}}
            <div style="margin-bottom:1rem;">
              <label class="form-label">Descripcion del proyecto, obra o actividad</label>
              <textarea name="resumen" rows="4" class="form-control">{{ old('resumen', $proyecto->resumen) }}</textarea>
            </div>
            {{-- TIPO DE PERMISO (como en create) --}}
            <div style="margin-bottom:1rem;">
              <label class="form-label">Tipo de permiso <span class="text-danger">*</span></label>
              <select name="tipo_permiso" class="form-select" required>
                <option value="" disabled hidden>Seleccione el tipo de permiso</option>
                @php $tp = old('tipo_permiso', $proyecto->tipo_permiso); @endphp
                <option value="Ficha Ambiental"       @selected($tp==='Ficha Ambiental')>Ficha Ambiental</option>
                <option value="Certificado Ambiental" @selected($tp==='Certificado Ambiental')>Certificado Ambiental</option>
                <option value="Registro Ambiental"    @selected($tp==='Registro Ambiental')>Registro Ambiental</option>
                <option value="Licencia Ambiental"    @selected($tp==='Licencia Ambiental')>Licencia Ambiental</option>
              </select>
              @error('tipo_permiso')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            {{-- CODIGO SUIA --}}
            <div style="margin-bottom:1rem;">
              <label class="form-label">Código SUIA</label>
              <input type="text" name="codigo_suia" class="form-control"
                    value="{{ old('codigo_suia', $proyecto->codigo_suia) }}"
                    placeholder="Código asignado por la plataforma SUIA">
              @error('codigo_suia')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            {{-- TIPO DE ESTUDIO (como en create) --}}
            <div style="margin-bottom:1rem;">
              <label class="form-label">Tipo de estudio <span class="text-danger">*</span></label>
              @php $te = old('tipo_estudio', $proyecto->tipo_estudio); @endphp
              <div class="form-check">
                <input class="form-check-input" type="radio" id="te_exante" name="tipo_estudio"
                      value="Antes del proyecto (Ex-ante)" @checked($te==='Antes del proyecto (Ex-ante)') required>
                <label class="form-check-label" for="te_exante">Antes del proyecto (Ex-ante)</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" id="te_expost" name="tipo_estudio"
                      value="Después del proyecto (Ex-post)" @checked($te==='Después del proyecto (Ex-post)') required>
                <label class="form-check-label" for="te_expost">Después del proyecto (Ex-post)</label>
              </div>
              @error('tipo_estudio')<small class="text-danger">{{ $message }}</small>@enderror
            </div>
          
            {{-- UBICACION --}}
            <div style="margin-bottom:1rem;">
              <label class="form-label">Provincia / Cantón / Parroquia</label>
              <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
                <select id="id_provincia" name="id_provincia" class="form-select" style="min-width: 220px;"></select>
                <select id="id_canton"    name="id_canton"    class="form-select" style="min-width: 220px;" disabled></select>
                <select id="id_parroquia" name="id_parroquia" class="form-select" style="min-width: 220px;" disabled></select>
              </div>
              <small class="text-muted">Al cambiar la ubicación, el mapa se centrará allí y se <strong>limpiarán</strong> los puntos para que coloques 5 nuevos.</small>
            </div>
            {{-- SECTOR (nuevo, va después de dirección) --}}
            <div style="margin-bottom:1rem;">
              <label class="form-label">Sector</label>
              <input type="text" name="sector" class="form-control" value="{{ old('sector', $proyecto->sector) }}">
              @error('sector')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            {{-- DIRECCION --}}
            <div style="margin-bottom:1rem;">
              <label class="form-label">Dirección Exacta</label>
              <input type="text" name="direccion" class="form-control" value="{{ old('direccion', $proyecto->direccion) }}">
            </div>

            {{-- PUNTOS (como en create) --}}
            <div style="margin-top:1.25rem; padding:1rem; border:1px solid #e5e7eb; border-radius:.75rem;">
              <h3 style="margin:0 0 .5rem 0;">Ubicación geográfica (elige 5 puntos)</h3>
              <p class="points-badge">Puntos seleccionados: <strong id="countPts">0</strong> / 5</p>
              <div id="mapPicker"></div>

              <div style="display:flex; gap:.5rem; margin-top:.75rem; flex-wrap:wrap;">
                <button type="button" id="btnUndoPt"  class="btn btn-light btn-sm">Deshacer último</button>
                <button type="button" id="btnClearPts" class="btn btn-light btn-sm">Limpiar</button>
                <span style="font-size:.875rem; color:#6b7280;">*Haz clic en el mapa para añadir puntos. Deben ser exactamente 5.</span>
              </div>
            </div>
          </div>

          {{-- Columna lateral: (si quieres más campos, agrégalos aquí) --}}
          <div>
            {{-- inputs ocultos --}}
            <input type="hidden" id="coordenadas_json" name="coordenadas_json" value="">
            {{-- puedes añadir otros ocultos si los usas en create --}}
          </div>
        </div>

        <div class="mt-3">
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script src="{{ asset('leaflet/leaflet.js') }}"></script>
<script>
(function(){
  // ===== Helpers =====
  const getJSON = async (url) => {
    const res = await fetch(url, { headers: {'X-Requested-With':'XMLHttpRequest'} });
    if (!res.ok) throw new Error('Network');
    return await res.json();
  };

  // MISMA LIMPIEZA QUE EN "CREAR PROYECTO"
  function soloNombre(raw) {
    if (raw == null) return '';
    let s = String(raw);

    // Normaliza y quita invisibles
    if (s.normalize) s = s.normalize('NFKC');
    s = s.replace(/\uFEFF|[\u200B-\u200D\u2060]/g, '').trim();

    // Entidades HTML → caracteres
    s = s
      .replace(/&(quot|ldquo|rdquo|laquo|raquo|bdquo|lsaquo|rsaquo);/gi, '"')
      .replace(/&(apos|lsquo|rsquo|sbquo);/gi, "'")
      .replace(/&#34;/g, '"')
      .replace(/&#39;/g, "'");

    // Elimina TODAS las comillas (dobles, simples y tipográficas)
    s = s.replace(/["'“”‘’`´«»‹›„‚″′＂＇]/g, '');

    // Quita paréntesis/corchetes SOLO en bordes
    s = s.replace(/^[\s()[\]{}〈〉《》]+|[\s()[\]{}〈〉《》]+$/g, '');

    // Si hay separadores, usa la parte con letras
    const parts = s.split(/[,\-|;:]/).map(t => t.trim()).filter(Boolean);
    s = parts.find(t => /[A-Za-zÁÉÍÓÚÜÑáéíóúüñ]/.test(t)) || s;

    // Quita códigos numéricos al inicio/fin
    s = s.replace(/^\d+\s*[-–.:)]*\s*/, '').replace(/\s*[-–.:(]*\s*\d+\s*$/, '');

    return s.trim();
  }

  // Devuelve el texto visible del <option> seleccionado (como en crear)
  function selTextById(idSel) {
    const el = document.getElementById(idSel);
    if (!el) return '';
    const opt = el.options[el.selectedIndex];
    return opt ? (opt.text || '').trim() : '';
  }

  // ===== API endpoints (versión ADMIN, como en tu editar) =====
  const API = {
    provincias: "{{ route('admin.api.provincias') }}",
    cantones:   idProv   => "{{ route('admin.api.cantones',   ['provincia' => '___']) }}".replace('___', encodeURIComponent(idProv)),
    parroquias: idCanton => "{{ route('admin.api.parroquias', ['canton'    => '___']) }}".replace('___', encodeURIComponent(idCanton)),
  };

  // ===== Carga de selects (igual a crear, aplicando soloNombre) =====
  const oldProv = @json(old('id_provincia', $proyecto->id_provincia));
  const oldCant = @json(old('id_canton',    $proyecto->id_canton));
  const oldParr = @json(old('id_parroquia', $proyecto->id_parroquia));

  async function cargarProvincias() {
    const sel = document.getElementById('id_provincia');
    sel.innerHTML = '<option value="" disabled selected hidden>Seleccione provincia</option>';
    try {
      const data = await getJSON(API.provincias);
      data.forEach(p => {
        const opt = document.createElement('option');
        const raw = p.provincia || p.nombre_provincia;
        const lbl = soloNombre(raw) || String(raw || '');
        opt.value = p.id_provincia;
        opt.textContent = lbl;
        if (String(oldProv) === String(p.id_provincia)) opt.selected = true;
        sel.appendChild(opt);
      });
      sel.disabled = false;
      if (sel.value) await cargarCantones(sel.value);
    } catch {
      sel.innerHTML = '<option value="">No se pudo cargar</option>';
    }
  }

  async function cargarCantones(idProvincia) {
    const sel = document.getElementById('id_canton');
    const parr = document.getElementById('id_parroquia');
    sel.innerHTML = '<option value="" disabled selected hidden>Seleccione cantón</option>';
    parr.innerHTML = '<option value="" disabled selected hidden>Seleccione parroquia</option>';
    parr.disabled = true;
    try {
      const data = await getJSON(API.cantones(idProvincia));
      data.forEach(c => {
        const opt = document.createElement('option');
        const raw = c.canton || c.nombre_canton;
        const lbl = soloNombre(raw) || String(raw || '');
        opt.value = c.id_canton;
        opt.textContent = lbl;
        if (String(oldCant) === String(c.id_canton)) opt.selected = true;
        sel.appendChild(opt);
      });
      sel.disabled = false;
      if (sel.value) await cargarParroquias(sel.value);
    } catch {
      sel.innerHTML = '<option value="">No se pudo cargar</option>';
    }
  }

  async function cargarParroquias(idCanton) {
    const sel = document.getElementById('id_parroquia');
    sel.innerHTML = '<option value="" disabled selected hidden>Seleccione parroquia</option>';
    try {
      const data = await getJSON(API.parroquias(idCanton));
      data.forEach(p => {
        const opt = document.createElement('option');
        const raw = p.parroquia || p.nombre_parroquia;
        const lbl = soloNombre(raw) || String(raw || '');
        opt.value = p.id_parroquia;
        opt.textContent = lbl;
        if (String(oldParr) === String(p.id_parroquia)) opt.selected = true;
        sel.appendChild(opt);
      });
      sel.disabled = false;
    } catch {
      sel.innerHTML = '<option value="">No se pudo cargar</option>';
    }
  }

  // ===== Mapa Leaflet (igual que tu editar, manteniendo tus reglas de 5 puntos) =====
  const MAX_POINTS = 5;
  let map, markers = [], coords = [];
  const lblCount = document.getElementById('countPts');
  const inpJSON  = document.getElementById('coordenadas_json');
  const btnUndo  = document.getElementById('btnUndoPt');
  const btnClear = document.getElementById('btnClearPts');

  function refreshHidden() {
    if (inpJSON) inpJSON.value = JSON.stringify(coords);
    if (lblCount) lblCount.textContent = String(coords.length);
  }
  function clearAll() {
    markers.forEach(m => map.removeLayer(m));
    markers = []; coords = []; refreshHidden();
  }
  function addPoint(latlng) {
    if (coords.length >= MAX_POINTS) return;
    coords.push({ lat: latlng.lat, lng: latlng.lng });
    const m = L.marker(latlng).addTo(map)
      .bindTooltip('P'+coords.length, {permanent:true, direction:'right', offset:[8,0], className:'pt-label'});
    markers.push(m);
    refreshHidden();
  }

  // ===== Geocodificación (MISMA BÚSQUEDA QUE EN "CREAR PROYECTO") =====
let geoAbortCtrl = null;

  async function geocodeAndCenter(q) {
    try {
      if (geoAbortCtrl) geoAbortCtrl.abort();
      geoAbortCtrl = new AbortController();

      const url = new URL('https://nominatim.openstreetmap.org/search');
      url.searchParams.set('format', 'geojson');
      url.searchParams.set('limit', '5');          // ← varios candidatos
      url.searchParams.set('polygon_geojson', '1');
      url.searchParams.set('countrycodes', 'ec');
      url.searchParams.set('q', q);

      const res = await fetch(url.toString(), { headers:{'Accept':'application/json'}, signal: geoAbortCtrl.signal });
      if (!res.ok) return false;

      const data = await res.json();
      if (!data.features || data.features.length === 0) return false;

      // Prioriza límites administrativos con bbox → cualquiera con bbox → el primero
      let f = data.features.find(x => x?.properties?.category === 'boundary' && x?.properties?.type === 'administrative' && Array.isArray(x?.bbox));
      if (!f) f = data.features.find(x => Array.isArray(x?.bbox));
      if (!f) f = data.features[0];

      if (Array.isArray(f.bbox) && f.bbox.length === 4) {
        const [minLng, minLat, maxLng, maxLat] = f.bbox;
        map.fitBounds(L.latLngBounds([minLat, minLng], [maxLat, maxLng]), { padding:[20,20] });
        return true;
      }
      if (f.geometry?.type === 'Point') {
        const [lng, lat] = f.geometry.coordinates;
        map.flyTo([lat, lng], 13, { duration: 0.6 });
        return true;
      }
      return false;
    } catch (e) {
      // console.warn('Geocode error:', e);
      return false;
    }
  }

  function debounce(fn, ms = 450) {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), ms); };
  }

  // MISMA COMPOSICIÓN DE QUERY QUE EN CREAR: parroquia, cantón, provincia, Ecuador
 const updateMapToSelection = debounce(async () => {
    const prov = selTextById('id_provincia');
    const cant = selTextById('id_canton');
    const parr = selTextById('id_parroquia');

    if (!(prov || cant || parr)) return;

    // Limpia los puntos porque cambió ubicación
    clearAll();

    // Construye queries
    const qParr = [parr, cant, prov, 'Ecuador'].filter(Boolean).join(', ');
    const qCant = [cant, prov, 'Ecuador'].filter(Boolean).join(', ');
    const qProv = [prov, 'Ecuador'].filter(Boolean).join(', ');

    let ok = false;
    if (parr) ok = await geocodeAndCenter(qParr);   // 1) parroquia
    if (!ok && cant) ok = await geocodeAndCenter(qCant); // 2) cantón
    if (!ok && prov) await geocodeAndCenter(qProv);      // 3) provincia (último recurso)
  }, 450);

  document.addEventListener('DOMContentLoaded', async () => {
    // Mapa base
    const initialCenter = [-0.1807, -78.4678]; // Quito fallback
    map = L.map('mapPicker', { preferCanvas:true }).setView(initialCenter, 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:19}).addTo(map);

    // Puntos ya guardados (si los hay)
    const puntos = @json($puntos ?? []);
    if (Array.isArray(puntos) && puntos.length) {
      puntos.forEach(p => addPoint(L.latLng(p.lat, p.lng)));
      const group = L.featureGroup(markers);
      if (markers.length) map.fitBounds(group.getBounds().pad(0.2));
    }

    // Click para añadir puntos
    map.on('click', (e) => addPoint(e.latlng));

    // Botones
    btnUndo?.addEventListener('click', () => {
      const m = markers.pop();
      if (m) map.removeLayer(m);
      coords.pop();
      refreshHidden();
    });
    btnClear?.addEventListener('click', clearAll);

    // Selects (carga + geocentrado como en crear)
    await cargarProvincias();
    document.getElementById('id_provincia')?.addEventListener('change', e => {
      document.getElementById('id_canton').disabled = true;
      document.getElementById('id_parroquia').disabled = true;
      cargarCantones(e.target.value);
      updateMapToSelection();
    });
    document.getElementById('id_canton')?.addEventListener('change', e => {
      document.getElementById('id_parroquia').disabled = true;
      cargarParroquias(e.target.value);
      updateMapToSelection();
    });
    document.getElementById('id_parroquia')?.addEventListener('change', updateMapToSelection);

    // Igual que en crear: autocentrar si hay old() al cargar
    window.addEventListener('load', updateMapToSelection);

    // Validación al enviar (mantengo tu regla de edición)
    document.getElementById('formProyecto').addEventListener('submit', (e) => {
      if (coords.length > 0 && coords.length !== 5) {
        e.preventDefault();
        alert('Debes seleccionar exactamente 5 puntos.');
        return false;
      }
      inpJSON.value = JSON.stringify(coords);
    });
  });
})();
</script>



@endpush
@endsection
