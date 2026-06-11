@extends('layouts.app')

@section('title', 'Gestión de Tipos e Ítems de Obligación')

@push('styles')
<link rel="stylesheet" href="{{ asset('styles/obligacion.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
  .header-with-button{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:12px}
  .soca-content{display:grid;grid-template-columns:320px 1fr;gap:16px}
  .soca-form .form-group{margin-bottom:.75rem}
  .soca-table{width:100%;border-collapse:collapse}
  .soca-table th,.soca-table td{border:1px solid #e5e7eb;padding:.6rem .7rem;vertical-align:top}
  .soca-table th{background:#f8fafc;font-weight:600}
  .row-actions{display:flex;gap:.5rem;flex-wrap:wrap}
  .muted{color:#6b7280}
  .switch{display:inline-flex;align-items:center;gap:.4rem}
  .items-wrap{background:#fbfbfd;border:1px solid #eef2f7;border-radius:.5rem;padding:.6rem;margin-top:.5rem}
  .inline-input{width:100%;max-width:520px}
  .btn-icon{padding:.35rem .55rem}
  .tag{display:inline-block;padding:.1rem .45rem;border:1px solid #e5e7eb;border-radius:.35rem;font-size:.75rem;background:#fff}
</style>
@endpush

@section('content')
<div class="main-container">
  {{-- Menú lateral --}}
  @include('menu_general.menu_general')

  <div class="obligacion-wrapper">
    <div class="soca-container">
      <div class="header-with-button">
        <h1>Gestión de Tipos de Obligación</h1>
      </div>

      <hr class="soca-divider">

      <div class="soca-content">
        {{-- Panel crear/editar rápido --}}
        <div class="soca-form">
          <h2 class="h5 mb-2">Crear nuevo tipo</h2>
          <div class="form-group">
            <label for="tipoNombre" class="form-label">Nombre</label>
            <input type="text" id="tipoNombre" class="form-control" placeholder="Ej.: Auditoría ambiental de cumplimiento">
          </div>
          <div class="form-group form-check switch">
            <input type="checkbox" id="tipoReqPeriodo" class="form-check-input">
            <label for="tipoReqPeriodo" class="form-check-label">Requiere período</label>
          </div>
          <div class="form-group form-check switch">
            <input type="checkbox" id="tipoActivo" class="form-check-input" checked>
            <label for="tipoActivo" class="form-check-label">Activo</label>
          </div>
          <button class="btn btn-primary w-100" id="btnCrearTipo"><i class="fa fa-plus"></i> Guardar tipo</button>

          <hr class="soca-divider my-3">

          <h2 class="h5 mb-2">Crear nuevo ítem</h2>
          <div class="form-group">
            <label class="form-label">Tipo</label>
            <select id="itemTipo" class="form-select"></select>
          </div>
          <div class="form-group">
            <label class="form-label">Descripción</label>
            <input type="text" id="itemDescripcion" class="form-control" placeholder="Ej.: Informe de monitoreo trimestral">
          </div>
          <div class="form-group form-check switch">
            <input type="checkbox" id="itemActivo" class="form-check-input" checked>
            <label for="itemActivo" class="form-check-label">Activo</label>
          </div>
          <button class="btn btn-primary w-100" id="btnCrearItem"><i class="fa fa-plus"></i> Guardar ítem</button>

          <p class="muted mt-3">Nota: estos catálogos alimentan el formulario de creación de obligaciones.</p>
        </div>

        {{-- Listado principal --}}
        <div class="soca-list">
          <h2 class="h5 mb-2">Listado de Tipos e Ítems</h2>
          <table class="soca-table" id="tablaTipos">
            <thead>
              <tr>
                <th style="width:40%">Tipo</th>
                <th style="width:20%">Requiere período</th>
                <th style="width:15%">Estado</th>
                <th style="width:25%">Acciones</th>
              </tr>
            </thead>
            <tbody id="tbodyTipos">
              <tr><td colspan="4" class="muted">Cargando…</td></tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function(){
  // Bases REST (colección)
  const BASE_TIPOS = @json(route('admin.tipos-obligacion.index', [], false)); // "/admin/tipos-obligacion"
  const BASE_ITEMS = @json(route('admin.items-obligacion.index', [], false)); // "/admin/items-obligacion"

  const ENDPOINTS = {
    // Lectura JSON
    listTipos:  @json(route('catalogos.tipos')),
    listItems:  (idTipo) => @json(route('catalogos.items')) + '?id_tipo=' + encodeURIComponent(idTipo),

    // Escritura (update/destroy agregan "/{id}")
    tipo: {
      store:   BASE_TIPOS,                                         // POST   /admin/tipos-obligacion
      update:  (id) => BASE_TIPOS.replace(/\/+$/,'') + '/' + id,   // PUT    /admin/tipos-obligacion/{id}
      destroy: (id) => BASE_TIPOS.replace(/\/+$/,'') + '/' + id,   // DELETE /admin/tipos-obligacion/{id}
    },
    item: {
      store:   BASE_ITEMS,                                         // POST   /admin/items-obligacion
      update:  (id) => BASE_ITEMS.replace(/\/+$/,'') + '/' + id,   // PUT    /admin/items-obligacion/{id}
      destroy: (id) => BASE_ITEMS.replace(/\/+$/,'') + '/' + id,   // DELETE /admin/items-obligacion/{id}
    }
  };

  // ===== Utils =====
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
  async function getJSON(url){
    const r = await fetch(url, { headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' }});
    if(!r.ok) throw new Error('HTTP '+r.status);
    return r.json();
  }
  async function sendForm(url, method, data){
    const r = await fetch(url, {
      method,
      headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN': CSRF,'Content-Type':'application/json' },
      body: JSON.stringify(data)
    });
    if (r.status >= 200 && r.status < 400) return true;
    let msg = 'Error al guardar';
    try { const j = await r.json(); msg = j.message || msg; } catch(_) {}
    throw new Error(msg);
  }
  function bool(v){ return v ? 1 : 0; }

  // ===== Estado =====
  let cacheTipos = [];
  const tbody = document.getElementById('tbodyTipos');
  const selTipoForItem = document.getElementById('itemTipo');

  // ===== Render =====
  async function loadTipos(){
    try{
      tbody.innerHTML = '<tr><td colspan="4" class="muted">Cargando…</td></tr>';
      const tipos = await getJSON(ENDPOINTS.listTipos); // [{id_tipo,nombre,requiere_periodo}]
      cacheTipos = tipos;

      // Poblar select del panel "nuevo ítem"
      selTipoForItem.innerHTML = '<option value="" disabled selected hidden>Seleccione tipo…</option>';
      tipos.forEach(t=>{
        const o = document.createElement('option');
        o.value = t.id_tipo; o.textContent = t.nombre;
        selTipoForItem.appendChild(o);
      });

      // Tabla
      tbody.innerHTML = '';
      if (!tipos.length){
        tbody.innerHTML = '<tr><td colspan="4" class="muted">Sin tipos aún.</td></tr>';
        return;
      }
      tipos.forEach(t=> tbody.appendChild(renderTipoRow(t)));
    }catch(err){
      tbody.innerHTML = '<tr><td colspan="4" class="text-danger">No se pudieron cargar los tipos.</td></tr>';
      console.error(err);
      toast('No se pudieron cargar los tipos','error');
    }
  }

  function renderTipoRow(t){
    const frag = document.createDocumentFragment();

    // --- fila principal
    const tr = document.createElement('tr');
    tr.dataset.id = t.id_tipo;

    const tdNombre = document.createElement('td');
    const tdReq    = document.createElement('td');
    const tdActivo = document.createElement('td');
    const tdAcc    = document.createElement('td');

    const nameSpan = document.createElement('div');
    nameSpan.textContent = t.nombre;
    nameSpan.className = 'fw-semibold';

    const editName = document.createElement('input');
    editName.type = 'text';
    editName.className = 'form-control inline-input';
    editName.value = t.nombre;
    editName.style.display = 'none';

    tdNombre.append(nameSpan, editName);

    const reqSpan = document.createElement('span');
    reqSpan.className = 'tag';
    reqSpan.textContent = t.requiere_periodo ? 'Sí' : 'No';

    const reqCheck = document.createElement('input');
    reqCheck.type='checkbox'; reqCheck.checked = !!t.requiere_periodo; reqCheck.style.display='none';

    tdReq.append(reqSpan, reqCheck);

    const actSpan = document.createElement('span');
    actSpan.className = 'tag';
    actSpan.textContent = 'Activo';
    tdActivo.appendChild(actSpan);

    const btnEdit   = btn('Editar','fa-pen-to-square','btn-outline-secondary btn-sm');
    const btnSave   = btn('Guardar','fa-floppy-disk','btn-success btn-sm'); btnSave.style.display='none';
    const btnCancel = btn('Cancelar','fa-xmark','btn-light btn-sm'); btnCancel.style.display='none';
    const btnItems  = btn('Ítems','fa-list','btn-primary btn-sm');
    const btnDelete = btn('Eliminar','fa-trash','btn-danger btn-sm');

    tdAcc.className='row-actions';
    tdAcc.append(btnEdit, btnSave, btnCancel, btnItems, btnDelete);

    tr.append(tdNombre, tdReq, tdActivo, tdAcc);

    // --- fila plegable de ítems (hermana, no hija)
    const trItems = document.createElement('tr');
    trItems.style.display='none';
    const tdItems = document.createElement('td');
    tdItems.colSpan = 4;
    tdItems.appendChild(renderItemsPanel(t.id_tipo));
    trItems.appendChild(tdItems);

    // Eventos
    btnEdit.addEventListener('click', ()=>{
      nameSpan.style.display='none';
      editName.style.display='';
      reqSpan.style.display='none';
      reqCheck.style.display='';
      btnEdit.style.display='none';
      btnSave.style.display='';
      btnCancel.style.display='';
    });
    btnCancel.addEventListener('click', ()=>{
      editName.value = t.nombre;
      reqCheck.checked = !!t.requiere_periodo;
      nameSpan.style.display=''; editName.style.display='none';
      reqSpan.style.display=''; reqCheck.style.display='none';
      btnEdit.style.display=''; btnSave.style.display='none'; btnCancel.style.display='none';
    });
    btnSave.addEventListener('click', async ()=>{
      const payload = { nombre: editName.value.trim(), requiere_periodo: bool(reqCheck.checked), activo: 1 };
      if (!payload.nombre) { return toast('Ingrese el nombre del tipo', 'warning'); }
      try{
        await sendForm(ENDPOINTS.tipo.update(t.id_tipo), 'PUT', payload);
        toast('Tipo actualizado','success');
        await loadTipos();
      }catch(e){ toast(e.message||'Error al actualizar','error'); }
    });
    btnDelete.addEventListener('click', async ()=>{
      const ok = await confirmBox('¿Eliminar este tipo?','Se eliminará el tipo. Los ítems asociados podrían quedar huérfanos si no lo manejas en backend.');
      if (!ok) return;
      try{
        await fetch(ENDPOINTS.tipo.destroy(t.id_tipo), {
          method:'POST',
          headers:{'X-CSRF-TOKEN': CSRF},
          body:new URLSearchParams({_method:'DELETE'})
        });
        toast('Tipo eliminado','success');
        await loadTipos();
      }catch(_){ toast('No se pudo eliminar','error'); }
    });
    btnItems.addEventListener('click', async ()=>{
      if (trItems.style.display==='none'){
        await populateItemsPanel(tdItems, t.id_tipo);
        trItems.style.display='';
        btnItems.classList.remove('btn-primary'); btnItems.classList.add('btn-outline-primary');
      } else {
        trItems.style.display='none';
        btnItems.classList.add('btn-primary'); btnItems.classList.remove('btn-outline-primary');
      }
    });

    // Agregamos ambas filas al fragmento (hermanas)
    frag.append(tr, trItems);
    return frag;
  }

  function renderItemsPanel(idTipo){
    const wrap = document.createElement('div');
    wrap.className = 'items-wrap';
    wrap.innerHTML = `
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div><strong>Ítems del tipo</strong> <span class="muted">(ID ${idTipo})</span></div>
        <div class="muted">Usa el botón <i class="fa fa-plus"></i> para agregar ítems</div>
      </div>
      <table class="table table-sm mb-2">
        <thead><tr><th style="width:70%">Descripción</th><th style="width:30%">Acciones</th></tr></thead>
        <tbody class="tbody-items"><tr><td colspan="2" class="muted">Cargando…</td></tr></tbody>
      </table>
      <div class="d-flex gap-2">
        <input type="text" class="form-control flex-fill" placeholder="Nuevo ítem…">
        <button class="btn btn-primary btn-icon btn-add"><i class="fa fa-plus"></i></button>
      </div>
    `;
    return wrap;
  }

  async function populateItemsPanel(tdItems, idTipo){
    const wrap = tdItems.firstElementChild;
    const tbody = wrap.querySelector('.tbody-items');
    const nuevo = wrap.querySelector('input');
    const btnAdd= wrap.querySelector('.btn-add');

    try{
      const items = await getJSON(ENDPOINTS.listItems(idTipo));
      tbody.innerHTML = '';
      if (!items.length) tbody.innerHTML = '<tr><td colspan="2" class="muted">Sin ítems.</td></tr>';
      items.forEach(it=>{
        tbody.appendChild(renderItemRow(idTipo, it));
      });
    }catch(err){
      tbody.innerHTML = '<tr><td colspan="2" class="text-danger">No se pudieron cargar los ítems.</td></tr>';
      console.error(err);
      toast('No se pudieron cargar los ítems','error');
    }

    btnAdd.onclick = async (e)=>{
      e.preventDefault();
      const desc = (nuevo.value||'').trim();
      if (!desc) return toast('Ingrese la descripción','warning');
      try{
        await sendForm(ENDPOINTS.item.store, 'POST', { id_tipo: idTipo, descripcion: desc, activo: 1 });
        toast('Ítem creado','success');
        nuevo.value='';
        await populateItemsPanel(tdItems, idTipo);
      }catch(err){ toast(err.message||'Error al crear','error'); }
    }
  }

  function renderItemRow(idTipo, it){
    const tr = document.createElement('tr');
    const tdDesc = document.createElement('td');
    const tdAcc  = document.createElement('td');

    const span = document.createElement('div');
    span.textContent = it.descripcion;

    const input = document.createElement('input');
    input.type='text'; input.className='form-control'; input.value=it.descripcion; input.style.display='none';

    tdDesc.append(span, input);

    const btnEdit   = btn('Editar','fa-pen-to-square','btn-outline-secondary btn-sm');
    const btnSave   = btn('Guardar','fa-floppy-disk','btn-success btn-sm'); btnSave.style.display='none';
    const btnCancel = btn('Cancelar','fa-xmark','btn-light btn-sm'); btnCancel.style.display='none';
    const btnDelete = btn('Eliminar','fa-trash','btn-danger btn-sm');

    tdAcc.className='row-actions';
    tdAcc.append(btnEdit, btnSave, btnCancel, btnDelete);

    btnEdit.onclick = ()=>{
      span.style.display='none'; input.style.display='';
      btnEdit.style.display='none'; btnSave.style.display=''; btnCancel.style.display='';
    };
    btnCancel.onclick = ()=>{
      input.value = it.descripcion;
      span.style.display=''; input.style.display='none';
      btnEdit.style.display=''; btnSave.style.display='none'; btnCancel.style.display='none';
    };
    btnSave.onclick = async ()=>{
      const desc = (input.value||'').trim();
      if (!desc) return toast('Ingrese la descripción','warning');
      try{
        await sendForm(ENDPOINTS.item.update(it.id_item), 'PUT', { id_tipo: idTipo, descripcion: desc, activo: 1 });
        toast('Ítem actualizado','success');
        await loadTipos();
      }catch(e){ toast(e.message||'Error al actualizar','error'); }
    };
    btnDelete.onclick = async ()=>{
      const ok = await confirmBox('¿Eliminar este ítem?','Esta acción no se puede deshacer.');
      if (!ok) return;
      try{
        await fetch(ENDPOINTS.item.destroy(it.id_item), {
          method:'POST',
          headers:{'X-CSRF-TOKEN': CSRF},
          body:new URLSearchParams({_method:'DELETE'})
        });
        toast('Ítem eliminado','success');
        await loadTipos();
      }catch(_){ toast('No se pudo eliminar','error'); }
    };

    tr.append(tdDesc, tdAcc);
    return tr;
  }

  // Botones helper
  function btn(text, icon, cls){
    const b = document.createElement('button');
    b.type='button';
    b.className = 'btn '+(cls||'btn-light')+' btn-icon';
    b.innerHTML = `<i class="fa ${icon}"></i> <span>${text}</span>`;
    return b;
  }

  // Notificaciones
  function toast(text, icon='success'){
    try{
      Swal.fire({icon, text, timer:1800, showConfirmButton:false});
    }catch(_){ alert(text); }
  }
  function confirmBox(title, text){
    return Swal.fire({icon:'warning', title, text, showCancelButton:true, confirmButtonText:'Sí', cancelButtonText:'No'})
      .then(r=>r.isConfirmed);
  }

  // Crear tipo / ítem (panel izquierdo)
  document.getElementById('btnCrearTipo').onclick = async ()=>{
    const nombre = (document.getElementById('tipoNombre').value||'').trim();
    const req    = document.getElementById('tipoReqPeriodo').checked;
    const activo = document.getElementById('tipoActivo').checked;
    if(!nombre) return toast('Ingresa el nombre del tipo','warning');
    try{
      await sendForm(ENDPOINTS.tipo.store, 'POST', { nombre, requiere_periodo: bool(req), activo: bool(activo) });
      toast('Tipo creado','success');
      document.getElementById('tipoNombre').value='';
      document.getElementById('tipoReqPeriodo').checked=false;
      document.getElementById('tipoActivo').checked=true;
      await loadTipos();
    }catch(e){ toast(e.message||'No se pudo crear','error'); }
  };

  document.getElementById('btnCrearItem').onclick = async ()=>{
    const id_tipo = document.getElementById('itemTipo').value;
    const descripcion = (document.getElementById('itemDescripcion').value||'').trim();
    const activo = document.getElementById('itemActivo').checked;
    if(!id_tipo) return toast('Selecciona el tipo','warning');
    if(!descripcion) return toast('Ingresa la descripción','warning');
    try{
      await sendForm(ENDPOINTS.item.store, 'POST', { id_tipo, descripcion, activo: bool(activo) });
      toast('Ítem creado','success');
      document.getElementById('itemDescripcion').value='';
      document.getElementById('itemActivo').checked=true;
      await loadTipos();
    }catch(e){ toast(e.message||'No se pudo crear','error'); }
  };

  // Init
  loadTipos();
})();
</script>
@endpush
