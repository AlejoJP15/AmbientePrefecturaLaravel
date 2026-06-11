document.addEventListener('DOMContentLoaded', function() {
    // Cambiar entre opciones de documentos
    const docOptions = document.querySelectorAll('.doc-option');
    docOptions.forEach(option => {
        option.addEventListener('click', function() {
            docOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Generar reporte
    document.querySelector('.btn-generar').addEventListener('click', function() {
        const tipo = document.querySelector('input[name="tipo"]:checked').value;
        const formato = document.querySelector('input[name="formato"]:checked').value;
        
        console.log('Generando reporte:', { tipo, formato });
        alert(`Generando reporte ${tipo} en formato ${formato.toUpperCase()}`);
    });
});