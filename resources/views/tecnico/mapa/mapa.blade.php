@extends('layouts.app')

@section('title', 'Mapa del Proyecto')

@push('styles')
<link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
<style>
  #mapFull {
    width: 100%;
    height: calc(100vh - 70px); /* ajusta según la altura de tu navbar */
  }
  #mapFull img { max-width: none !important; }
  .pt-label {
    background:#fff; border:1px solid #e5e7eb; padding:2px 6px; border-radius:4px; font-size:12px;
    box-shadow:0 1px 2px rgba(0,0,0,.05);
  }
</style>
<link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}">
<style>
  .map-wrap{
    position: relative;
    width: 100%;
  }
  #mapFull{
    position: relative;
    width: 100%;
    height: calc(100vh - 70px); /* ajusta según tu navbar */
  }
  #mapFull img{ max-width: none !important; }

  /* Cuadro informativo (overlay) */
  .map-info{
    position: absolute;
    left: 12px;          /* esquina: cambia a right/top/bottom si prefieres */
    bottom: 12px;
    z-index: 1000;
    max-width: min(90vw, 420px);
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 10px 12px;
    box-shadow: 0 8px 18px rgba(0,0,0,.08);
    font-size: 14px;
    line-height: 1.25rem;
  }
  .map-info__title{
    font-weight: 700;
    color: #111827;
    margin-bottom: 2px;
  }
  .map-info__loc{
    color: #374151;
    margin-bottom: 2px;
  }
  .map-info__addr{
    color: #6b7280;
  }

  /* Etiquetas de puntos si las usas permanentes */
  .pt-label{
    background:#fff; border:1px solid #e5e7eb; padding:2px 6px; border-radius:4px;
    font-size:12px; box-shadow:0 1px 2px rgba(0,0,0,.05);
  }
</style>

@endpush

@section('content')
<div class="container-fluid p-0">
  <a href="{{ route('tecnico.proyectos.index') }}" class="btn-back-map">
    <i class="fa-solid fa-arrow-left"></i> Volver
  </a>
</div>

@push('styles')
<style>
  .btn-back-map {
    position: absolute;
    top: 12px;
    right: 12px;
    z-index: 1000;

    display: inline-flex;
    align-items: center;
    gap: 6px;

    background: #0055ffff;
    color: #fefeffff;
    font-size: 20px;
    font-weight: 500;
    padding: 6px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;

    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    text-decoration: none;
    transition: all 0.2s ease-in-out;
  }

  .btn-back-map:hover {
    background: #0557fbff;
    color: #111827;
    border-color: #9ca3af;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.12);
  }

  .btn-back-map i {
    font-size: 14px;
  }
</style>
@endpush

  <div class="map-wrap">
    <div id="mapFull"></div>

    {{-- Cuadro con nombre --}}
    <div class="map-info">
      <div class="map-info__title">
        {{ $proyecto->nombre ?? 'Proyecto sin nombre' }}
      </div>
      
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('leaflet/leaflet.js') }}"></script> {{-- igual que en show --}}
<script>
(function() {
  const puntos = @json($puntos ?? []);              // [{lat,lng,label}, ...] — MISMO formato que show
  const polyGeojsonStr = @json($polyGeojson ?? null); // string JSON o null

  function init() {
    // Fallback cualquiera (no importa, luego hacemos fitBounds)
    const map = L.map('mapFull', { preferCanvas: true }).setView([-1.67, -78.65], 7);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const layers = [];

    // 1) Marcadores P1..P5 (igual que en show)
    puntos.forEach(p => {
      if (Number.isFinite(p.lat) && Number.isFinite(p.lng)) {
        const m = L.marker([p.lat, p.lng])
          .bindTooltip(p.label || '', { direction:'top', permanent:true, className:'pt-label' })
          .addTo(map);
        layers.push(m);
      }
    });

    // 2) Polígono: si tienes en BD úsalo; si no, conecta P1..Pn como haces en show
    if (polyGeojsonStr) {
      try {
        const gj = JSON.parse(polyGeojsonStr);
        layers.push(L.geoJSON(gj, { style: { weight: 2, fillOpacity: 0.15 } }).addTo(map));
      } catch (e) {
        console.warn('GeoJSON inválido del polígono:', e);
      }
    } else if (puntos.length >= 3) {
      const latlngs = puntos.map(p => [p.lat, p.lng]);
      latlngs.push(latlngs[0]); // cerrar anillo
      layers.push(L.polygon(latlngs, { weight: 2, fillOpacity: 0.15 }).addTo(map));
    }

    // 3) Auto-zoom EXACTAMENTE igual que en show
    setTimeout(() => {
      if (layers.length) {
        const group = L.featureGroup(layers);
        map.fitBounds(group.getBounds().pad(0.2));
      }
      map.invalidateSize();
    }, 0);

    // Debug opcional
    console.log('VerMapa → puntos:', puntos);
  }

  if (document.readyState === 'complete' || document.readyState === 'interactive') init();
  else document.addEventListener('DOMContentLoaded', init);
})();

</script>

@endpush
