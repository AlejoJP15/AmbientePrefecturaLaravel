document.addEventListener('DOMContentLoaded', function() {
    // Manejar la selección de formatos
    const formatOptions = document.querySelectorAll('.format-option input');
    formatOptions.forEach(option => {
        option.addEventListener('change', function() {
            // Remover todas las clases activas primero
            document.querySelectorAll('.format-content').forEach(content => {
                content.style.boxShadow = 'none';
                content.style.borderColor = '#DDD';
                content.style.backgroundColor = 'transparent';
            });
            
            // Aplicar estilos al seleccionado
            if(this.checked) {
                const content = this.nextElementSibling;
                content.style.boxShadow = '0 0 0 1px currentColor';
                content.style.borderColor = 'currentColor';
                content.style.backgroundColor = 'rgba(0,0,0,0.03)';
            }
        });
    });
});