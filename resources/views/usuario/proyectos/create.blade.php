@extends('layouts.app')

@section('content')
@push('styles')
<link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
<link rel="stylesheet" href="{{ asset('css/proyecto-form.css') }}">
<link rel="stylesheet" href="{{ asset('css/actividades') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  #mapPicker {
    height: 380px;
    border: 1px solid #e5e7eb;
    border-radius: .5rem;
  }
  /* MUY IMPORTANTE: evita que reglas globales "img { max-width:100% }" rompan Leaflet */
  #mapPicker img { max-width: none !important; }

  .points-badge {font-size: .875rem; color:#374151;}
  .points-badge strong {color:#111827;}
</style>
@endpush


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

<div class="main-container">

    <div class="archivo-wrapper">
        <div class="soca-container">


          <div class="container" style="max-width: 960px;">
            <h1 style="margin-top:1rem;">
                Creación del proyecto — <small style="font-weight:normal;">por favor ingrese los datos solicitados</small>
            </h1>

            <div class="selector-container">
        <button id="btnAbrirSelector" type="button" class="selector-boton">
          Seleccione la actividad <br><br>
        </button>

        <span id="actividadSeleccionada" class="selector-resultado"></span>
      </div>

      {{-- Selector jerárquico --}}
      <div id="panelSelector" class="selector-panel">
        <div class="selector-header">
          <strong>Jerarquía:</strong>
          <div id="breadcrumbs" class="selector-breadcrumbs"></div>
        </div>

        <div id="listaActual" class="selector-lista">
          {{-- items dinámicos --}}
        </div>

        <div class="selector-footer">
          <button id="btnCerrarSelector" type="button" class="selector-cerrar">
            Cerrar
          </button>
        </div>
      </div>

      </div>
      {{-- Formulario creación de proyecto --}}
      <form id="formProyecto" action="{{ route('usuario.proyectos.store') }}" method="POST" style="display:none; margin-top:1.25rem;">
        @csrf

        {{-- Hidden: vienen de tu selector de actividad --}}
        <input type="hidden" name="id_actividad" id="id_actividad">
        <input type="hidden" id="actividad_codigo" name="actividad_codigo">
        <input type="hidden" id="actividad_descripcion" name="actividad_descripcion">

        @if (session('ok'))
          <div style="margin:.5rem 0; padding:.6rem; background:#ecfdf5; border:1px solid #a7f3d0; border-radius:.4rem; color:#065f46;">
            {{ session('ok') }}
          </div>
        @endif

        <div style="display:grid; gap:.8rem;">
          {{-- 1) Descripción de la actividad (llenada por tu selector) --}}
          <div>
            <label style="display:block; font-weight:600;">Descripción de la actividad</label>
            <div id="actividadResumen" style="padding:.5rem; border:1px dashed #cbd5e1; border-radius:.4rem; background:#f8fafc;">
              {{-- se llena al seleccionar hoja --}}
            </div>
          </div>

          {{-- 3) Nombre del proyecto --}}
          <div>
            <label for="nombre" style="display:block; font-weight:600;">Nombre del proyecto, obra o actividad <span style="color:#dc2626">*</span></label>
            <input type="text" id="nombre" name="nombre" required
                  value="{{ old('nombre') }}"
                  placeholder="Ingrese el nombre del proyecto"
                  style="width:100%; padding:.55rem; border:1px solid #cbd5e1; border-radius:.4rem;">
            @error('nombre')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
          </div>

          {{-- 4) Descripción del proyecto (usa tu campo "resumen") --}}
          <div>
            <label for="resumen" style="display:block; font-weight:600;">Descripción del proyecto, obra o actividad</label>
            <textarea id="resumen" name="resumen" rows="3"
                      placeholder="Describa el proyecto en detalle"
                      style="width:100%; padding:.55rem; border:1px solid #cbd5e1; border-radius:.4rem;">{{ old('resumen') }}</textarea>
            @error('resumen')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
          </div>

          {{-- 5) Tipo de permiso (select con 4 opciones) --}}
          <div>
            <label for="tipo_permiso" style="display:block; font-weight:600;">Tipo de permiso <span style="color:#dc2626">*</span></label>
            <select id="tipo_permiso" name="tipo_permiso" required>
              <option value="" disabled selected hidden>Seleccione el tipo de permiso</option>
              <option value="Ficha Ambiental"       @selected(old('tipo_permiso')==='Ficha Ambiental')>Ficha Ambiental</option>
              <option value="Certificado Ambiental" @selected(old('tipo_permiso')==='Certificado Ambiental')>Certificado Ambiental</option>
              <option value="Registro Ambiental"    @selected(old('tipo_permiso')==='Registro Ambiental')>Registro Ambiental</option>
              <option value="Licencia Ambiental"    @selected(old('tipo_permiso')==='Licencia Ambiental')>Licencia Ambiental</option>
            </select>
            @error('tipo_permiso')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
          </div>

          {{-- 6) Código SUIA --}}
          <div>
            <label for="codigo_suia" style="display:block; font-weight:600;">Código SUIA</label>
            <input type="text" id="codigo_suia" name="codigo_suia" value="{{ old('codigo_suia') }}"
                  placeholder="Código asignado por la plataforma SUIA"
                  style="width:100%; padding:.55rem; border:1px solid #cbd5e1; border-radius:.4rem;">
            @error('codigo_suia')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
          </div>

          {{-- 7) Tipo de estudio (radios) --}}
          <div>
            <label style="display:block; font-weight:600;">Tipo de estudio <span style="color:#dc2626">*</span></label>
            <div style="display:flex; gap:1rem; flex-wrap:wrap; padding:.35rem 0;">
             <label><input type="radio" name="tipo_estudio"
                  value="Antes del proyecto (Ex-ante)"
                  {{ old('tipo_estudio')==='Antes del proyecto (Ex-ante)' ? 'checked' : '' }} required>
                  Antes del proyecto (Ex-ante)</label>

              <label><input type="radio" name="tipo_estudio"
                  value="Después del proyecto (Ex-post)"
                  {{ old('tipo_estudio','Después del proyecto (Ex-post)')==='Después del proyecto (Ex-post)' ? 'checked' : '' }} required>
                  Después del proyecto (Ex-post)</label>
            </div>
            @error('tipo_estudio')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
          </div>

          {{-- 8) Ubicación dependiente: Provincia -> Cantón -> Parroquia --}}
          <div>
            <label style="display:block; font-weight:600;">Ubicación</label>
            <div style="display:flex; gap:.8rem; flex-wrap:wrap;">
              <div style="flex:1 1 220px;">
                <label for="id_provincia" style="display:block; font-weight:600;">Provincia</label>
                <select id="id_provincia" name="id_provincia"
                        style="width:100%; padding:.55rem; border:1px solid #cbd5e1; border-radius:.4rem;"></select>
                @error('id_provincia')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
              </div>
              <div style="flex:1 1 220px;">
                <label for="id_canton" style="display:block; font-weight:600;">Cantón</label>
                <select id="id_canton" name="id_canton"
                        style="width:100%; padding:.55rem; border:1px solid #cbd5e1; border-radius:.4rem;"></select>
                @error('id_canton')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
              </div>
              <div style="flex:1 1 220px;">
                <label for="id_parroquia" style="display:block; font-weight:600;">Parroquia</label>
                <select id="id_parroquia" name="id_parroquia"
                        style="width:100%; padding:.55rem; border:1px solid #cbd5e1; border-radius:.4rem;"></select>
                @error('id_parroquia')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
              </div>
            </div>
          </div>
          {{-- 9) Sector --}}
          <div>
            <label for="sector" style="display:block; font-weight:600;">
              Sector <span style="color:#dc2626">*</span>
            </label>
            <input
              type="text"
              id="sector"
              name="sector"
              required
              value="{{ old('sector') }}"
              style="width:100%; padding:.55rem; border:1px solid #cbd5e1; border-radius:.4rem;"
            >
            @error('sector')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
          </div>

          {{-- 9) Dirección exacta --}}
          <div>
            <label for="direccion" style="display:block; font-weight:600;">Dirección exacta</label>
            <input type="text" id="direccion" name="direccion" value="{{ old('direccion') }}"
                  style="width:100%; padding:.55rem; border:1px solid #cbd5e1; border-radius:.4rem;">
            @error('direccion')<small style="color:#b91c1c;">{{ $message }}</small>@enderror
          </div>
          <div style="margin-top:1.25rem; padding:1rem; border:1px solid #e5e7eb; border-radius:.75rem;">
            <h3 style="margin:0 0 .5rem 0;">Ubicación geográfica (elige 5 puntos)</h3>
            <p class="points-badge">Puntos seleccionados: <strong id="countPts">0</strong> / 5</p>

            <div id="mapPicker"></div>

            <div style="display:flex; gap:.5rem; margin-top:.75rem; flex-wrap:wrap;">
              <button type="button" id="btnUndoPt"
                      style="padding:.45rem .75rem; border:1px solid #d1d5db; border-radius:.5rem; background:#fafafa; cursor:pointer;">
                Deshacer último
              </button>
              <button type="button" id="btnClearPts"
                      style="padding:.45rem .75rem; border:1px solid #d1d5db; border-radius:.5rem; background:#fafafa; cursor:pointer;">
                Limpiar
              </button>
              <span style="font-size:.875rem; color:#6b7280;">
                *Haz clic en el mapa para añadir puntos. Deben ser exactamente 5.
              </span>
            </div>

            {{-- Aquí guardaremos el JSON con [{lat, lng}, ...] --}}
            <input type="hidden" name="coordenadas_json" id="coordenadas_json" value="">
          </div>
          {{-- 10) Guardar --}}
          <div>
            <button type="submit"
                    style="padding:.65rem 1rem; background:#2563eb; color:#fff; border:none; border-radius:.5rem; cursor:pointer;">
              Guardar
            </button>
          </div>
        </div>
      </form>
      
    </div>
    

</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
  const panel = document.getElementById('panelSelector');
  const btnAbrir = document.getElementById('btnAbrirSelector');
  const btnCerrar = document.getElementById('btnCerrarSelector');
  const listaActual = document.getElementById('listaActual');
  const breadcrumbs = document.getElementById('breadcrumbs');
  const actividadSeleccionada = document.getElementById('actividadSeleccionada');

  const formProyecto = document.getElementById('formProyecto');
  const idActividadInput = document.getElementById('id_actividad');
  const actCodigoInput   = document.getElementById('actividad_codigo');
  const actDescInput     = document.getElementById('actividad_descripcion');
  const actResumenDiv    = document.getElementById('actividadResumen');
  
  const jerarquiaLabel = breadcrumbs?.parentElement?.querySelector('strong');
  if (jerarquiaLabel) jerarquiaLabel.style.display = 'none';

  let path = []; // [{id, codigo, descripcion}]
  const leafCache = new Map(); // cache de {codigo -> true/false}

  btnAbrir.addEventListener('click', async () => {
    panel.style.display = 'block';
    formProyecto.style.display = 'none';
    actividadSeleccionada.textContent = '';
    path = [];
    renderBreadcrumbs();
    await loadRoots();
  });

  btnCerrar.addEventListener('click', () => panel.style.display = 'none');

  function crumbButton(node, idx) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.textContent = node.codigo + ' — ' + node.descripcion;
    btn.style = 'padding:.25rem .5rem; border:1px solid #e5e7eb; border-radius:.4rem; background:#fff; cursor:pointer;';
    btn.addEventListener('click', async () => {
      path = path.slice(0, idx + 1);
      renderBreadcrumbs();
      await loadChildren(node.codigo);
    });
    return btn;
  }

  function renderBreadcrumbs() {
    breadcrumbs.innerHTML = '';
    if (path.length === 0) return; // no mostrar "Nivel actual: SECCION"

    path.forEach((node, idx) => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.textContent = node.descripcion; // <-- SIN código
      btn.style = 'padding:.25rem .5rem; border:1px solid #e5e7eb; border-radius:.4rem; background:#fff; cursor:pointer;';
      btn.addEventListener('click', async () => {
        path = path.slice(0, idx + 1);
        renderBreadcrumbs();
        await loadChildren(node.codigo);
      });
      breadcrumbs.appendChild(btn);

      if (idx < path.length - 1) {
        const sep = document.createElement('span');
        sep.textContent = '›';
        sep.style.margin = '0 .25rem';
        breadcrumbs.appendChild(sep);
      }
    });
  }


  async function loadRoots() {
    listaActual.innerHTML = 'Cargando...';
    const res = await fetch('{{ route('usuario.actividades.roots') }}');
    const data = await res.json();
    await renderList(data, null);
  }

  async function loadChildren(codigo) {
    listaActual.innerHTML = 'Cargando...';
    const res = await fetch('{{ url('usuario/actividades/children') }}/' + encodeURIComponent(codigo));
    const data = await res.json();
    await renderList(data, codigo);
  }

  async function isLeaf(codigo) {
    if (leafCache.has(codigo)) return leafCache.get(codigo);
    const res = await fetch('{{ url('usuario/actividades/is-leaf') }}/' + encodeURIComponent(codigo));
    const data = await res.json();
    leafCache.set(codigo, !!data.leaf);
    return !!data.leaf;
  }

  async function renderList(items, parentCodigo) {
    listaActual.innerHTML = '';

    if (!items || items.length === 0) {
      if (parentCodigo) {
        const current = path[path.length - 1];
        showLeafActions(current);
      } else {
        listaActual.textContent = 'No hay actividades disponibles.';
      }
      return;
    }

    // Consulta en paralelo qué items son hoja
    const leafFlags = await Promise.all(items.map(i => isLeaf(i.codigo_actividad)));

    items.forEach((item, i) => {
      const card = document.createElement('div');
      card.style = 'border:1px solid #e5e7eb; border-radius:.5rem; padding:.75rem; background:#fff;';

      const title = document.createElement('div');
      title.style = 'font-weight:600; margin-bottom:.25rem;';
      title.textContent = item.descripcion_actividad;

      const level = document.createElement('div');
      level.style = 'font-size:.85rem; color:#6b7280;';

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.style = 'margin-top:.5rem; padding:.4rem .7rem; border:1px solid #ddd; border-radius:.4rem; background:#f9fafb; cursor:pointer;';

      if (leafFlags[i]) {
        // Es hoja -> mostramos "Seleccionar"
        btn.textContent = 'Seleccionar';
        btn.addEventListener('click', () => {
          selectLeaf({ id: item.id_actividad, codigo: item.codigo_actividad, descripcion: item.descripcion_actividad });
        });
      } else {
        // Tiene hijos -> "Abrir subniveles"
        btn.textContent = 'Abrir subniveles';
        btn.addEventListener('click', async () => {
          path.push({ id: item.id_actividad, codigo: item.codigo_actividad, descripcion: item.descripcion_actividad });
          renderBreadcrumbs();
          await loadChildren(item.codigo_actividad);
        });
      }

      card.appendChild(title);
      card.appendChild(level);
      card.appendChild(btn);
      listaActual.appendChild(card);
    });
  }

  function selectLeaf(node) {
    // pintar selección y mostrar formulario
    idActividadInput.value = node.id;
    actCodigoInput.value   = node.codigo;
    actDescInput.value     = node.descripcion;

    if (document.getElementById('actividadResumen')) {
      document.getElementById('actividadResumen').textContent = `${node.codigo} — ${node.descripcion}`;
    }
    actividadSeleccionada.textContent = `Actividad seleccionada:${node.descripcion}`;

    formProyecto.style.display = 'block';
    panel.style.display = 'none';
  }

  function showLeafActions(node) {
    const box = document.createElement('div');
    box.style = 'border:1px dashed #cbd5e1; border-radius:.5rem; padding:1rem; background:#f8fafc;';
    const p = document.createElement('p');
    p.innerHTML = `<strong>Subnivel final seleccionado:</strong> ${node.codigo} — ${node.descripcion}`;

    const btnSelect = document.createElement('button');
    btnSelect.type = 'button';
    btnSelect.textContent = 'Seleccionar';
    btnSelect.style = 'margin-top:.75rem; padding:.55rem .9rem; background:#16a34a; color:#fff; border:none; border-radius:.5rem; cursor:pointer;';
    btnSelect.addEventListener('click', () => selectLeaf(node));

    box.appendChild(p);
    box.appendChild(btnSelect);
    listaActual.appendChild(box);
  }
});
</script>
@push('scripts')
<script>
  // ——— helper: fetch JSON con cabecera adecuada
  async function getJSON(url) {
    const r = await fetch(url, { headers: { 'Accept': 'application/json' }});
    if (!r.ok) throw new Error(`HTTP ${r.status}`);
    return r.json();
  }

  // ——— limpia comillas/paréntesis/códigos y deja solo el nombre
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
      .replace(/&#34;/g, '"').replace(/&#39;/g, "'");

    // ❗ Elimina TODAS las comillas (dobles, simples y tipográficas) en cualquier parte
    s = s.replace(/["'“”‘’`´«»‹›„‚″′＂＇]/g, '');

    // Quita paréntesis/corchetes/bordes
    s = s.replace(/^[\s()[\]{}〈〉《》]+|[\s()[\]{}〈〉《》]+$/g, '');

    // Si hay separadores, usa la parte con letras
    const parts = s.split(/[,\-|;:]/).map(t => t.trim()).filter(Boolean);
    s = parts.find(t => /[A-Za-zÁÉÍÓÚÜÑáéíóúüñ]/.test(t)) || s;

    // Quita códigos numéricos al inicio/fin
    s = s.replace(/^\d+\s*[-–.:)]*\s*/, '').replace(/\s*[-–.:(]*\s*\d+\s*$/, '');

    return s.trim();
  }


  // ——— endpoints para CREAR PROYECTO (con auth)
  const API = {
    provincias: "{{ route('usuario.api.provincias') }}",
    cantones:   idProv   => "{{ route('usuario.api.cantones',   ['provincia' => '___']) }}".replace('___', encodeURIComponent(idProv)),
    parroquias: idCanton => "{{ route('usuario.api.parroquias', ['canton'    => '___']) }}".replace('___', encodeURIComponent(idCanton)),
  };

  async function cargarProvincias() {
    const sel = document.getElementById('id_provincia');
    if (!sel) return;
    sel.innerHTML = '<option value="" disabled selected hidden>Seleccione provincia</option>';
    try {
      const data = await getJSON(API.provincias);
      const oldProv = @json(old('id_provincia'));
      data.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id_provincia;
        opt.textContent = soloNombre(p.provincia || p.nombre_provincia); // ← limpiar aquí
        if (oldProv && String(oldProv) === String(p.id_provincia)) opt.selected = true;
        sel.appendChild(opt);
      });
      if (sel.value) await cargarCantones(sel.value);
    } catch {
      sel.innerHTML = '<option value="">No se pudo cargar</option>';
    }
  }

  async function cargarCantones(idProvincia) {
    const sel = document.getElementById('id_canton');
    const parroquiaSel = document.getElementById('id_parroquia');
    if (!sel) return;
    sel.innerHTML = '<option value="" disabled selected hidden>Seleccione cantón</option>';
    parroquiaSel.innerHTML = '<option value="" disabled selected hidden>Seleccione parroquia</option>';
    if (!idProvincia) return;
    try {
      const data = await getJSON(API.cantones(idProvincia));
      const oldCanton = @json(old('id_canton'));
      data.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id_canton;
        opt.textContent = soloNombre(c.canton || c.nombre_canton); // ← limpiar aquí
        if (oldCanton && String(oldCanton) === String(c.id_canton)) opt.selected = true;
        sel.appendChild(opt);
      });
      if (sel.value) await cargarParroquias(sel.value);
    } catch {
      sel.innerHTML = '<option value="">No se pudo cargar</option>';
    }
  }

  async function cargarParroquias(idCanton) {
    const sel = document.getElementById('id_parroquia');
    if (!sel) return;
    sel.innerHTML = '<option value="" disabled selected hidden>Seleccione parroquia</option>';
    if (!idCanton) return;
    try {
      const data = await getJSON(API.parroquias(idCanton));
      const oldParroquia = @json(old('id_parroquia'));
      data.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id_parroquia;
        opt.textContent = soloNombre(p.parroquia || p.nombre_parroquia); // ← limpiar aquí
        if (oldParroquia && String(oldParroquia) === String(p.id_parroquia)) opt.selected = true;
        sel.appendChild(opt);
      });
    } catch {
      sel.innerHTML = '<option value="">No se pudo cargar</option>';
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    const provSel = document.getElementById('id_provincia');
    const cantSel = document.getElementById('id_canton');
    if (provSel) {
      cargarProvincias();
      provSel.addEventListener('change', e => cargarCantones(e.target.value));
    }
    if (cantSel) {
      cantSel.addEventListener('change', e => cargarParroquias(e.target.value));
    }
  });
</script>

<script src="{{ asset('leaflet/leaflet.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // --- Mapa Leaflet ---
  const mapEl = document.getElementById('mapPicker');
  if (!mapEl) return;

  // Centra por defecto en Quito
  const initialCenter = [-0.1807, -78.4678]; // [lat, lng]
  const map = L.map('mapPicker', { preferCanvas: true }).setView(initialCenter, 12);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
  }).addTo(map);

  // ============ NUEVO: Geocodificación por selección (Provincia/Cantón/Parroquia) ============
  // IDs de los selects (ajusta si usas otros):
  const PROV_SEL_ID = 'id_provincia';
  const CANT_SEL_ID = 'id_canton';
  const PARR_SEL_ID = 'id_parroquia';

  function selTextById(idSel) {
    const el = document.getElementById(idSel);
    if (!el) return '';
    const opt = el.options[el.selectedIndex];
    return opt ? (opt.text || '').trim() : '';
  }

  // AbortController para cancelar requests si el usuario cambia rápido
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

      const res = await fetch(url.toString(), {
        headers: { 'Accept': 'application/json' },
        signal: geoAbortCtrl.signal
      });
      if (!res.ok) return false;

      const data = await res.json();
      if (!data.features || data.features.length === 0) return false;

      // Prioriza límites administrativos con bbox → cualquiera con bbox → el primero
      let f = data.features.find(x =>
        x?.properties?.category === 'boundary' &&
        x?.properties?.type === 'administrative' &&
        Array.isArray(x?.bbox)
      );
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
    } catch {
      return false;
    }
  }

  function debounce(fn, ms = 450) {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), ms);
    };
  }

const updateMapToSelection = debounce(async () => {
    const prov = selTextById(PROV_SEL_ID);
    const cant = selTextById(CANT_SEL_ID);
    const parr = selTextById(PARR_SEL_ID);

    if (!(prov || cant || parr)) return;

    const qParr = [parr, cant, prov, 'Ecuador'].filter(Boolean).join(', ');
    const qCant = [cant, prov, 'Ecuador'].filter(Boolean).join(', ');
    const qProv = [prov, 'Ecuador'].filter(Boolean).join(', ');

    let ok = false;
    if (parr) ok = await geocodeAndCenter(qParr);   // 1) parroquia
    if (!ok && cant) ok = await geocodeAndCenter(qCant); // 2) cantón
    if (!ok && prov) await geocodeAndCenter(qProv);      // 3) provincia
  }, 450);

  const provSel = document.getElementById(PROV_SEL_ID);
  const cantSel = document.getElementById(CANT_SEL_ID);
  const parrSel = document.getElementById(PARR_SEL_ID);

  // Enganches: cada vez que cambie, recentra
  provSel?.addEventListener('change', updateMapToSelection);
  cantSel?.addEventListener('change', updateMapToSelection);
  parrSel?.addEventListener('change', updateMapToSelection);

  // Al cargar (por si llega con old() seleccionado):
  window.addEventListener('load', updateMapToSelection);
  // =================================================================================================

  const MAX_POINTS = 5;
  const markers = [];       // array de L.Marker
  const coords = [];        // array de {lat, lng}
  const inp = document.getElementById('coordenadas_json');
  const lblCount = document.getElementById('countPts');
  const form = document.getElementById('formProyecto');

  function refreshHidden() {
    if (inp) inp.value = JSON.stringify(coords);
    if (lblCount) lblCount.textContent = String(coords.length);
  }

  function addPoint(latlng) {
    if (coords.length >= MAX_POINTS) {
      alert('Ya seleccionaste 5 puntos. Usa "Deshacer" o "Limpiar" para cambiar.');
      return;
    }
    const m = L.marker(latlng, {draggable: false}).addTo(map);
    markers.push(m);
    coords.push({lat: Number(latlng.lat), lng: Number(latlng.lng)});
    refreshHidden();
  }

  function undoPoint() {
    if (!markers.length) return;
    const m = markers.pop();
    map.removeLayer(m);
    coords.pop();
    refreshHidden();
  }

  function clearPoints() {
    while (markers.length) {
      map.removeLayer(markers.pop());
    }
    coords.splice(0, coords.length);
    refreshHidden();
  }

  map.on('click', (e) => addPoint(e.latlng));

  document.getElementById('btnUndoPt')?.addEventListener('click', undoPoint);
  document.getElementById('btnClearPts')?.addEventListener('click', clearPoints);

  // Si el form está oculto y luego aparece, invalida el tamaño del mapa
  if (form) {
    const obs = new MutationObserver(() => {
      setTimeout(() => map.invalidateSize(), 250);
    });
    obs.observe(form, { attributes: true, attributeFilter: ['style', 'class'] });
  }

  // Validación dura en submit: deben ser EXACTAMENTE 5
  form?.addEventListener('submit', function(e) {
    if (coords.length !== MAX_POINTS) {
      e.preventDefault();
      alert('Debes seleccionar exactamente 5 puntos en el mapa antes de guardar.');
      return false;
    }
    // Guarda el JSON en el input hidden
    if (inp) inp.value = JSON.stringify(coords);
  });
});
</script>


<script>
const btnAbrir = document.getElementById("btnAbrirSelector");
const btnCerrar = document.getElementById("btnCerrarSelector");
const panel = document.getElementById("panelSelector");

btnAbrir.addEventListener("click", () => {
  panel.style.display = panel.style.display === "block" ? "none" : "block";
});

btnCerrar.addEventListener("click", () => {
  panel.style.display = "none";
});
</script>


@endpush


@endsection


