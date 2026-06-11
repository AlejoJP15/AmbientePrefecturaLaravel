document.addEventListener('DOMContentLoaded', function() {
    // Inicialización de TinyMCE
    tinymce.init({
    selector: '.tiny-editor',
    language: 'es',
    plugins: 'advlist autolink lists link charmap preview anchor table',
    toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table',
    //menubar: false,
    skin: 'oxide',
    height: 300,
    statusbar: false,
    
    setup: function(editor) {
        editor.on('init', function() {
            this.getContainer().style.transition = 'border 0.3s ease';
            this.getContainer().style.borderRadius = '4px';
            this.getContainer().style.border = '1px solid #ced4da';
        });
        editor.on('focus', function() {
            this.getContainer().style.borderColor = '#80bdff';
            this.getContainer().style.boxShadow = '0 0 0 0.2rem rgba(0,123,255,.25)';
        });
        editor.on('blur', function() {
            this.getContainer().style.borderColor = '#ced4da';
            this.getContainer().style.boxShadow = 'none';
        });
    },
    formats: {
        alignleft: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign: 'left'}},
        aligncenter: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign: 'center'}},
        alignright: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign: 'right'}},
        alignjustify: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li', styles: {textAlign: 'justify'}},
        bold: {inline: 'strong', 'remove': 'all'},
        italic: {inline: 'em', 'remove': 'all'},
        underline: {inline: 'u', 'remove': 'all'}
    },
    table_default_styles: {
        width: '100%',
        borderCollapse: 'collapse'
    },
    table_default_attributes: {
        border: '1'
    },
    style_formats: [
        {title: 'Variable', inline: 'span', classes: 'variable'},
        {title: 'Encabezado', block: 'h2', classes: 'template-header'},
        {title: 'Texto Legal', block: 'p', classes: 'legal-text'}
    ]
    });

    // Controladores para los botones de la barra de herramientas
    document.querySelectorAll('.tool-btn').forEach(button => {
        button.addEventListener('click', function() {
            const command = this.getAttribute('data-command');
            const editorId = this.closest('.editor-section').querySelector('.tiny-editor').id;
            tinymce.get(editorId).execCommand(command);
        });
    });

    // Controladores para los botones de acción
    document.querySelector('.btn-save').addEventListener('click', function() {
        // Aquí iría la lógica para guardar
        console.log('Contenido guardado');
        alert('Los cambios han sido guardados');
        window.location.href = "/formatos";
    });

    document.querySelector('.btn-cancel').addEventListener('click', function() {
        if(confirm('¿Está seguro que desea cancelar? Se perderán los cambios no guardados.')) {
            window.location.href = "/formatos"; // Cambia esto por tu ruta de cancelación
        }
    });
});