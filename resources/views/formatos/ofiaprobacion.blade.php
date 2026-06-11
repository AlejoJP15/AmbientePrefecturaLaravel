<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editor de Oficio Aprobacion</title>
    <link rel="stylesheet" href="{{ asset('styles/infobservado.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tiny.cloud/1/nyuio3arpf9o0ihwut9yxxpf7a4xoiz6iyh525sv0m26z7q2/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>

<div class="main-container">
    {{-- Menú lateral --}}
    @include('menu_general.menu_general')
    
    <div class="page-wrapper">
        <div class="editor-container">
            <h1>Edición de la Plantilla para Oficio Aprobación</h1>
            <p class="subtitle">Por favor ingrese los datos solicitados</p>

            <form id="formPlantilla" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="plantilla_id" value="{{ $plantilla->id_pformato }}">

                <!-- Sección Asunto -->
                <div class="editor-section">
                    <h2>Asunto</h2>
                    <textarea id="asunto-editor" name="asunto" class="tiny-editor">
                        {!! $plantilla->asunto ?? '<div class="template-variable"><p>APROBACIÓN AL <span class="variable"><strong>[tipoObligacion]</strong></span> DEL PROYECTO "<span class="variable"><strong>[nombreProyecto]</strong></span>" PERIODO <span class="variable"><strong>[periodo]</strong></span></p></div>' !!}
                    </textarea>
                </div>

                <!-- Sección Para -->
                <div class="editor-section">
                    <h2>Para</h2>
                    <textarea id="para-editor" name="para" class="tiny-editor">
                        {!! $plantilla->para ?? '<div class="template-variable"><p><span class="variable"><strong>[nivelInstruccion]</strong></span><br><span class="variable"><strong>[nombresApellidos]</strong></span><br><br><strong>REPRESENTANTE LEGAL</strong></p></div>' !!}
                    </textarea>
                </div>

                <!-- Sección Cuerpo -->
                <div class="editor-section">
                    <h2>Cuerpo</h2>
                    <textarea id="cuerpo-editor" name="cuerpo" class="tiny-editor">
                        {!! $plantilla->cuerpo ?? '<div class="template-text"><p>Mediante Oficio Nro. <span class="variable"><strong>[numeroOficio]</strong></span> de fecha <span class="variable"><strong>[fechaOficio]</strong></span>, ingresado a esta dependencia administrativa el <span class="variable"><strong>[fechaIngreso]</strong></span> con Trámite <span class="variable"><strong>[numeroTramite]</strong></span>, remite el <span class="variable"><strong>[nombreSolicitante]</strong></span>, las facturas de pago por concepto pronunciamiento respecto a <span class="variable"><strong>[tipoObligacion]</strong></span> Nº <span class="variable"><strong>[numeroFactura]</strong></span> de fecha <span class="variable"><strong>[fechaFactura]</strong></span> por un valor de USD <span class="variable"><strong>[valorFactura]</strong></span> y pago por control y seguimiento de Pcs Nº <span class="variable"><strong>[numeroFacturaControl]</strong></span> de fecha <span class="variable"><strong>[fechaFacturaControl]</strong></span> por un valor de USD <span class="variable"><strong>[valorFacturaControl]</strong></span>, del <span class="variable"><strong>[tipoObligacion]</strong></span> del Proyecto "<span class="variable"><strong>[nombreProyecto]</strong></span>", ubicado en el cantón <span class="variable"><strong>[nombreCanton]</strong></span> correspondiente al periodo <span class="variable"><strong>[periodo]</strong></span> para su análisis y pronunciamiento.</p><p>Al respecto, y en base al Informe técnico remitido mediante Memorando, se determina que las facturas ingresadas corresponden al pago por concepto pronunciamiento respecto a informes ambientales de cumplimiento Nº <span class="variable"><strong>[numeroFactura]</strong></span> de fecha <span class="variable"><strong>[fechaFactura]</strong></span> por un valor de USD <span class="variable"><strong>[valorFactura]</strong></span> y el pago por control y seguimiento Pcs Nº <span class="variable"><strong>[numeroFacturaControl]</strong></span> de fecha <span class="variable"><strong>[fechafacturaControl]</strong></span> por un valor de USD <span class="variable"><strong>[valorFacturaControl]</strong></span>, Aprueba, el <span class="variable"><strong>[tipoObligacion]</strong></span> proyecto "<span class="variable"><strong>[nombreProyecto]</strong></span>", ubicado en el Cantón <span class="variable"><strong>[nombreCanton]</strong></span>, Provincia de <span class="variable"><strong>[nombreProvincia]</strong></span>, correspondiente al periodo <span class="variable"><strong>[periodo]</strong></span>, cabe recalcar que el representante legal deberá cumplir estrictamente con el Plan de Manejo Ambiental para lo cual la Autoridad Ambiental Competente se encargará del control y seguimiento.</p></div>' !!}
                    </textarea>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                    <a href="{{ route('formatos.index') }}" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuración de TinyMCE
    tinymce.init({
        selector: '.tiny-editor',
        height: 300,
        language: 'es',
        menubar: true,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
        content_style: '.variable { background-color: #fff3cd; padding: 2px 4px; border-radius: 3px; color: #856404; }'
    });

    // Manejo del formulario con AJAX y redirección automática
    const form = document.getElementById('formPlantilla');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const plantillaId = document.querySelector('input[name="plantilla_id"]').value;
            
            // Recopilar datos de TODOS los editores que existan en el formulario
            const formData = {
                _token: document.querySelector('input[name="_token"]').value
            };

            // Agregar contenido de cada editor si existe
            const editores = ['asunto', 'para', 'cuerpo', 'antecedentes', 'analisis', 'objetivos', 'observaciones', 'detalle', 'conclusiones'];
            
            editores.forEach(function(nombre) {
                const editorId = nombre + '-editor';
                const editor = tinymce.get(editorId);
                if (editor) {
                    formData[nombre] = editor.getContent();
                }
            });

            // Deshabilitar botón de guardar para evitar múltiples clics
            const btnGuardar = form.querySelector('.btn-save');
            if (btnGuardar) {
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            }

            // Enviar con AJAX
            fetch(`/formatos/${plantillaId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    alert('✅ Plantilla guardada correctamente');
                    
                    // Redirigir a la lista de formatos después de 500ms
                    setTimeout(function() {
                        window.location.href = '/formatos';
                    }, 500);
                } else {
                    alert('❌ Error: ' + data.message);
                    // Rehabilitar botón en caso de error
                    if (btnGuardar) {
                        btnGuardar.disabled = false;
                        btnGuardar.innerHTML = '<i class="fas fa-save"></i> Guardar';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('❌ Error al guardar la plantilla');
                // Rehabilitar botón en caso de error
                if (btnGuardar) {
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = '<i class="fas fa-save"></i> Guardar';
                }
            });
        });
    }
});
</script>

</body>
</html>