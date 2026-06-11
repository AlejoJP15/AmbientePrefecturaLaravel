document.addEventListener('DOMContentLoaded', function() {
    const tabla = document.getElementById('tablaObligaciones');
    let nextId = 12; 
    
    // Datos iniciales 
    const obligaciones = [
        { id: 1, descripcion: 'Auditoría ambiental de cumplimiento' },
        { id: 2, descripcion: 'Cambio de representante legal' },
        { id: 3, descripcion: 'Estudio de impacto ambiental' },
        { id: 4, descripcion: 'Informe ambiental de cumplimiento' },
        { id: 5, descripcion: 'Informe de monitoreo ambiental' },
        { id: 6, descripcion: 'Informe de plan de acción' },
        { id: 7, descripcion: 'Informe de plan de cierre' },
        { id: 8, descripcion: 'Informe de plan emergente' },
        { id: 9, descripcion: 'Plan de acción' },
        { id: 10, descripcion: 'Plan de cierre' },
        { id: 11, descripcion: 'Plan emergente' },
        { id: 12, descripcion: 'Proceso de participación ciudadana' },
        { id: 13, descripcion: 'Términos de referencia para auditorías ambientales de cumplimiento' }
    ];
    
    function renderTable() {
        const tbody = tabla.querySelector('tbody');
        tbody.innerHTML = '';
        
        obligaciones.forEach(obligacion => {
            const row = document.createElement('tr');
            row.dataset.id = obligacion.id;
            
            row.innerHTML = `
                <td class="descripcion">${obligacion.descripcion}</td>
                <td class="acciones">
                    <button class="btn-editar">Editar</button>
                    <div class="edicion" style="display: none;">
                        <button class="btn-guardar">Guardar</button>
                        <button class="btn-cancelar">Cancelar</button>
                    </div>
                </td>
            `;
            
            tbody.appendChild(row);
        });
    }
    
    tabla.addEventListener('click', function(e) {
        const target = e.target;
        
        if (target.classList.contains('btn-editar')) {
            iniciarEdicion(target);
        } else if (target.classList.contains('btn-guardar')) {
            guardarEdicion(target);
        } else if (target.classList.contains('btn-cancelar')) {
            cancelarEdicion(target);
        }
    });
    
    function iniciarEdicion(btnEditar) {
        const fila = btnEditar.closest('tr');
        const descripcion = fila.querySelector('.descripcion');
        const acciones = fila.querySelector('.acciones');
        
        // Guardar valor original
        fila.dataset.originalValue = descripcion.textContent;
        
        // Crear input de edición
        const input = document.createElement('input');
        input.type = 'text';
        input.className = 'edit-input';
        input.value = descripcion.textContent;
        
        // Reemplazar texto con input
        descripcion.innerHTML = '';
        descripcion.appendChild(input);
        input.focus();
        
        // Mostrar botones de guardar/cancelar
        btnEditar.style.display = 'none';
        acciones.querySelector('.edicion').style.display = 'inline-flex';
    }
    
    function guardarEdicion(btnGuardar) {
        const fila = btnGuardar.closest('tr');
        const id = parseInt(fila.dataset.id);
        const nuevoValor = fila.querySelector('.edit-input').value;
        const descripcion = fila.querySelector('.descripcion');
        const acciones = fila.querySelector('.acciones');
        
        // Actualizar en el array
        const index = obligaciones.findIndex(o => o.id === id);
        if (index !== -1) {
            obligaciones[index].descripcion = nuevoValor;
        }
        
        // Actualizar visualmente
        descripcion.textContent = nuevoValor;
        acciones.querySelector('.btn-editar').style.display = 'inline-block';
        acciones.querySelector('.edicion').style.display = 'none';
    }
    
    function cancelarEdicion(btnCancelar) {
        const fila = btnCancelar.closest('tr');
        const descripcion = fila.querySelector('.descripcion');
        const acciones = fila.querySelector('.acciones');
        
        // Restaurar valor original
        descripcion.textContent = fila.dataset.originalValue;
        
        // Restaurar botones
        acciones.querySelector('.btn-editar').style.display = 'inline-block';
        acciones.querySelector('.edicion').style.display = 'none';
    }
    
    // Para el botón de guardar nuevo
    document.getElementById('btnGuardarNuevo').addEventListener('click', function() {
        const nuevaDescripcion = document.getElementById('nuevaDescripcion').value;
        
        if (!nuevaDescripcion) {
            alert('Por favor ingrese una descripción');
            return;
        }
        
        // Agregar al array
        obligaciones.push({
            id: nextId++,
            descripcion: nuevaDescripcion
        });
        
        // Renderizar tabla nuevamente
        renderTable();
        
        // Limpiar input
        document.getElementById('nuevaDescripcion').value = '';
    });
    
    // Renderizar tabla inicial
    renderTable();
});