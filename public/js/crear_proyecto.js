document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const selectedActivityText = document.getElementById('selectedActivityText');
    const activityModal = document.getElementById('activityModal');
    const closeModal = document.getElementById('closeModal');
    const cancelSelection = document.getElementById('cancelSelection');
    const confirmSelection = document.getElementById('confirmSelection');
    const searchInput = document.getElementById('searchActivity');
    const projectForm = document.getElementById('projectForm');
    const fileUploadBtn = document.getElementById('fileUploadBtn');
    const coordinateFile = document.getElementById('coordinateFile');
    const fileName = document.getElementById('fileName');
    const addCoordinateBtn = document.getElementById('addCoordinate');
    const coordinatesBody = document.getElementById('coordinatesBody');
    const projectSummary = document.getElementById('projectSummary');
    const backToFormBtn = document.getElementById('backToForm');
    const confirmProjectBtn = document.getElementById('confirmProject');
    
    // Actividad seleccionada (valor inicial)
    let selectedActivity = "";
    
    // Abrir modal al hacer clic en la actividad seleccionada
    selectedActivityText.addEventListener('click', function() {
        openActivityModal();
    });
    
    // Función para abrir el modal
    function openActivityModal() {
        activityModal.style.display = 'flex';
        
        // 🔒 Cerrar todas las categorías y subcategorías al abrir
        document.querySelectorAll('.subcategory, .activity-list').forEach(el => {
            el.classList.remove('open');
        });

        // 🔒 Resetear todos los íconos
        document.querySelectorAll('.toggle-icon').forEach(icon => {
            icon.classList.remove('open');
        });

        // ❌ Quitar cualquier selección previa
        const prevSelected = document.querySelector('.activity-item.selected');
        if (prevSelected) {
            prevSelected.classList.remove('selected');
        }
    }
    
    // Cerrar modal
    function closeActivityModal() {
        activityModal.style.display = 'none';
    }
    
    closeModal.addEventListener('click', closeActivityModal);
    cancelSelection.addEventListener('click', closeActivityModal);
    
    // Cerrar modal al hacer clic fuera del contenido
    activityModal.addEventListener('click', function(e) {
        if (e.target === activityModal) {
            closeActivityModal();
        }
    });
    
    // Toggle para categorías y subcategorías
    const categoryTitles = document.querySelectorAll('.category-title, .subcategory-title');
    
    categoryTitles.forEach(title => {
        title.addEventListener('click', function() {
            const targetId = this.getAttribute('data-toggle');
            const targetElement = document.getElementById(targetId);
            const toggleIcon = this.querySelector('.toggle-icon');
            
            // Alternar visibilidad
            if (targetElement.classList.contains('open')) {
                targetElement.classList.remove('open');
                toggleIcon.classList.remove('open');
            } else {
                targetElement.classList.add('open');
                toggleIcon.classList.add('open');
            }
        });
    });
    
    // Seleccionar actividad
    const activityItems = document.querySelectorAll('.activity-item');
    let selectedItem = document.querySelector('.activity-item.selected');
    
    activityItems.forEach(item => {
        item.addEventListener('click', function() {
            // Quitar selección anterior
            if (selectedItem) {
                selectedItem.classList.remove('selected');
            }
            
            // Establecer nueva selección
            this.classList.add('selected');
            selectedItem = this;
        });
    });
    
    // Confirmar selección
    confirmSelection.addEventListener('click', function() {
        if (selectedItem) {
            selectedActivity = selectedItem.textContent;
            selectedActivityText.textContent = selectedActivity;
            closeActivityModal();
        } else {
            alert('Por favor, seleccione una actividad');
        }
    });
    
    // Búsqueda de actividades
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        activityItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = 'block';
                
                // Expandir categorías padre si coincide con la búsqueda
                let parent = item.closest('.subcategory');
                if (parent) {
                    parent.classList.add('open');
                    const parentTitle = parent.previousElementSibling;
                    if (parentTitle && parentTitle.classList.contains('subcategory-title')) {
                        parentTitle.querySelector('.toggle-icon').classList.add('open');
                    }
                }
                
                parent = item.closest('.category');
                if (parent) {
                    const categoryTitle = parent.querySelector('.category-title');
                    categoryTitle.querySelector('.toggle-icon').classList.add('open');
                    const subcategory = parent.querySelector('.subcategory');
                    if (subcategory) subcategory.classList.add('open');
                }
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Upload de archivo de coordenadas
    fileUploadBtn.addEventListener('click', function() {
        coordinateFile.click();
    });
    
    coordinateFile.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileName.textContent = this.files[0].name;
        } else {
            fileName.textContent = 'No se ha seleccionado ningún archivo';
        }
    });
    
    // Agregar coordenadas manualmente
    addCoordinateBtn.addEventListener('click', function() {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="text" placeholder="Coordenada X" class="coord-input"></td>
            <td><input type="text" placeholder="Coordenada Y" class="coord-input"></td>
            <td>
                <select class="coord-type">
                    <option value="punto">Punto</option>
                    <option value="linea">Línea</option>
                    <option value="poligono">Polígono</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn-remove">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        
        coordinatesBody.appendChild(newRow);
        
        // Agregar evento al botón de eliminar
        const removeBtn = newRow.querySelector('.btn-remove');
        removeBtn.addEventListener('click', function() {
            coordinatesBody.removeChild(newRow);
        });
    });
    
    // Validación del formulario
    projectForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validación básica
        const projectName = document.getElementById('projectName').value;
        const projectDescription = document.getElementById('projectDescription').value;
        const permitType = document.getElementById('permitType').value;
        const officeNumber = document.getElementById('officeNumber').value;
        const studyTime = document.querySelector('input[name="studyTime"]:checked');
        const address = document.getElementById('address').value;
        
        if (!projectName || !projectDescription || !permitType || !officeNumber || !studyTime || !address) {
            alert('Por favor, complete todos los campos obligatorios');
            return;
        }
        
        // Verificar que se hayan agregado coordenadas (SOLUCIÓN: validación optimizada)
        const coordInputs = document.querySelectorAll('.coord-input');
        let hasCoords = false;
        let hasValidCoords = false;
        
        // Verificar si hay al menos un par de coordenadas completas
        for (let i = 0; i < coordInputs.length; i += 2) {
            if (coordInputs[i].value.trim() !== '' && coordInputs[i+1].value.trim() !== '') {
                hasValidCoords = true;
                break;
            }
        }
        
        // Verificar si hay inputs con valores (pero posiblemente incompletos)
        coordInputs.forEach(input => {
            if (input.value.trim() !== '') {
                hasCoords = true;
            }
        });
        
        // Mostrar alerta solo si no hay coordenadas válidas
        if (!hasValidCoords) {
            if (hasCoords) {
                alert('Por favor, complete ambos campos de coordenadas para al menos un punto');
            } else {
                alert('Por favor, agregue al menos una coordenada completa (X e Y)');
            }
            return;
        }
        
        // Mostrar resumen del proyecto
        projectForm.style.display = 'none';
        projectSummary.style.display = 'block';
    });
    
    // Volver al formulario desde el resumen
    backToFormBtn.addEventListener('click', function() {
        projectSummary.style.display = 'none';
        projectForm.style.display = 'block';
    });
    
    // Función para actualizar el resumen con los datos del formulario
    function updateProjectSummary() {
        // Obtener valores del formulario
        const projectName = document.getElementById('projectName').value;
        const projectDescription = document.getElementById('projectDescription').value;
        const permitType = document.getElementById('permitType');
        const permitText = permitType.options[permitType.selectedIndex].text;
        const officeNumber = document.getElementById('officeNumber').value;
        const studyTime = document.querySelector('input[name="studyTime"]:checked');
        const studyText = studyTime ? 
            (studyTime.value === 'ex-ante' ? 'Antes del proyecto (Ex-ante)' : 'Después del proyecto (Ex-post)') : 
            'No seleccionado';
        const address = document.getElementById('address').value;
        const province = document.getElementById('province').value;
        const canton = document.getElementById('canton').value;
        const parish = document.getElementById('parish').value;
        
        // Obtener coordenadas
        const coordInputs = document.querySelectorAll('.coord-input');
        let coordinates = [];
        for (let i = 0; i < coordInputs.length; i += 2) {
            if (coordInputs[i].value.trim() !== '' && coordInputs[i+1].value.trim() !== '') {
                coordinates.push(`(${coordInputs[i].value}, ${coordInputs[i+1].value})`);
            }
        }
        
        // Actualizar el resumen
        document.getElementById('summary-name').textContent = projectName;
        document.getElementById('summary-description').textContent = projectDescription;
        document.getElementById('summary-activity').textContent = selectedActivity;
        document.getElementById('summary-permit').textContent = permitText;
        document.getElementById('summary-study-type').textContent = studyText;
        document.getElementById('summary-office-number').textContent = officeNumber;
        document.getElementById('summary-address').textContent = address;
        document.getElementById('summary-location').textContent = `${province} - ${canton} - ${parish}`;
        
        // Actualizar coordenadas si existen
        if (coordinates.length > 0) {
            document.getElementById('summary-coordinates').textContent = coordinates.join(', ');
        } else {
            document.getElementById('summary-coordinates').textContent = 'No se han ingresado coordenadas';
        }
        
        // Generar código y fecha actual
        const now = new Date();
        const randomCode = Math.floor(1000 + Math.random() * 9000); // Código de 4 dígitos
        const formattedDate = now.toLocaleString('es-ES');
        
        document.getElementById('summary-code').textContent = randomCode;
        document.getElementById('summary-date').textContent = formattedDate;
    }

    // Modificar el evento submit del formulario para que actualice el resumen
    projectForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validación básica
        const projectName = document.getElementById('projectName').value;
        const projectDescription = document.getElementById('projectDescription').value;
        const permitType = document.getElementById('permitType').value;
        const officeNumber = document.getElementById('officeNumber').value;
        const studyTime = document.querySelector('input[name="studyTime"]:checked');
        const address = document.getElementById('address').value;
        
        if (!projectName || !projectDescription || !permitType || !officeNumber || !studyTime || !address) {
            alert('Por favor, complete todos los campos obligatorios');
            return;
        }
        
        // Verificar que se hayan agregado coordenadas (validación optimizada)
        const coordInputs = document.querySelectorAll('.coord-input');
        let hasValidCoords = false;
        
        // Verificar si hay al menos un par de coordenadas completas
        for (let i = 0; i < coordInputs.length; i += 2) {
            if (coordInputs[i].value.trim() !== '' && coordInputs[i+1].value.trim() !== '') {
                hasValidCoords = true;
                break;
            }
        }
        
        // Mostrar alerta solo si no hay coordenadas válidas
        if (!hasValidCoords) {
            alert('Por favor, agregue al menos una coordenada completa (X e Y)');
            return;
        }
        
        // Actualizar y mostrar resumen
        updateProjectSummary();
        projectForm.style.display = 'none';
        projectSummary.style.display = 'block';
    });

    // Confirmar proyecto - CORRECCIÓN: Duración de 3 segundos exactos
    confirmProjectBtn.addEventListener('click', function() {
        // Mostrar modal de confirmación
        const confirmationModal = document.getElementById('confirmationModal');
        confirmationModal.style.display = 'flex';
        
        // Obtener elementos de la barra de progreso
        const progressFill = document.querySelector('.progress-fill');
        
        // Iniciar animación de la barra de progreso - CORRECCIÓN: 3 segundos exactos
        let progress = 0;
        const totalTime = 3000; // 3 segundos
        const intervalTime = 30; // Actualizar cada 30ms
        const increment = (intervalTime / totalTime) * 100;
        
        const progressInterval = setInterval(() => {
            progress += increment;
            if (progress > 100) progress = 100;
            progressFill.style.width = progress + '%';
            
            if (progress >= 100) {
                clearInterval(progressInterval);
                
                // Aquí iría la lógica para enviar los datos al servidor
                const projectCode = document.getElementById('summary-code').textContent;
                
                // Simular envío exitoso
                setTimeout(() => {
                    // Redirigir a la página principal
                    window.location.href = '/usuario_externo'; // Cambia por la URL de tu página principal
                }, 500);
            }
        }, intervalTime);
    });

    // Función para redireccionar
    function redirectToHomepage() {
        // Mostrar mensaje de confirmación
        alert('Proyecto creado exitosamente. Redirigiendo a la página principal...');
        
        // Redirigir después de un breve delay
        setTimeout(() => {
            window.location.href = '/usuario_externo'; // Cambia por la URL de tu página principal
        }, 1500);
    }
    
    // Abrir modal automáticamente al cargar la página
    openActivityModal();
});