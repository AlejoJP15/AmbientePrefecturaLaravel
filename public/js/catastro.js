document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    const fileName = document.getElementById('fileName');
    const uploadForm = document.getElementById('uploadForm');
    
    fileInput.addEventListener('change', function() {
        fileName.textContent = this.files.length 
            ? this.files[0].name 
            : 'Ningún archivo seleccionado';
    });
    
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!fileInput.files.length) {
            alert('Por favor seleccione un archivo');
            return;
        }
        
        // Aquí puedes agregar la lógica AJAX para subir el archivo
        alert('Archivo listo para subir: ' + fileInput.files[0].name);
        
        // Ejemplo de AJAX:
        /*
        const formData = new FormData();
        formData.append('archivo', fileInput.files[0]);
        
        fetch('/ruta-para-subir', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            alert('Archivo subido con éxito');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al subir archivo');
        });
        */
    });
});