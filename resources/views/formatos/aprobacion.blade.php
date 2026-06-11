<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editor de Informe Aprobación</title>
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
            <h1>Edición De La Plantilla Para Informe Aprobación</h1>
            <p class="subtitle">Por favor ingrese los datos solicitados</p>

            <form id="formPlantilla" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="plantilla_id" value="{{ $plantilla->id_pformato }}">

                <!-- Sección Asunto -->
                <div class="editor-section">
                    <h2>Asunto</h2>
                    <textarea id="asunto-editor" name="asunto" class="tiny-editor">
                        {!! $plantilla->asunto ?? '<div class="template-variable"><p>APROBACIÓN AL <span class="variable"><strong>[tipoObligacion]</span></strong> DEL PROYECTO <span class="variable"><strong>"[nombreProyecto]",</strong></span> PERIODO <span><strong>[periodo]</strong></span></p></div>' !!}
                    </textarea>
                </div>

                <!-- Sección Antecedentes -->
                <div class="editor-section">
                    <h2>Antecedentes</h2>
                    <textarea id="antecedentes-editor" name="antecedentes" class="tiny-editor">
                        {!! $plantilla->antecedentes ?? '<div class="template-text"><p>Mediante Oficio Nro. <span class="variable"><strong>[numeroOficio]</strong></span> de fecha <span class="variable"><strong>[fechaOficio]</strong></span>, ingresado a esta dependencia administrativa el <span class="variable"><strong>[fechaIngreso]</strong></span> con trámite <span class="variable"><strong>[numeroTramite]</strong></span>, remite el <strong><span class="variable">[nombreSolicitante]</span></strong>, las facturas de pago por concepto pronunciamiento respecto a <span class="variable"><strong>[tipoObligacion]</strong></span> N° <span class="variable"><strong>[numeroFactura]</strong></span> de fecha <span class="variable"><strong>[fechaFactura]</strong></span> por un valor de USD <span class="variable"><strong>[valorFactura]</strong></span> y pago por control y seguimiento de Pcs N° <span class="variable"><strong>[numeroFacturaControl]</strong></span> de fecha <span class="variable"><strong>[fechaFacturaControl]</strong></span> por un valor de USD <span class="variable"><strong>[valorFacturaControl]</strong></span>, del <span class="variable"><strong>[tipoObligacion]</strong></span> del proyecto "<span class="variable"><strong>[nombreProyecto]"</strong></span>, ubicado en el cantón <span class="variable"><strong>[nombreCanton]</strong></span> correspondiente al periodo <span class="variable"><strong>[periodo]</strong></span> para su análisis y pronunciamiento.</p></div>' !!}
                    </textarea>
                </div>

                <!-- Sección Análisis -->
                <div class="editor-section">
                    <h2>Análisis</h2>
                    <textarea id="analisis-editor" name="analisis" class="tiny-editor">
                        {!! $plantilla->analisis ?? '<div class="legal-text"><p class="confirmacion-texto">Una vez realizada la inspección técnica se confirma la información ingresada a esta dependencia administrativa y cumple con:<br><br>&nbsp;&nbsp;• Normativa Ambiental Vigente<br>&nbsp;&nbsp;• Planes de Manejo Ambiental<br>&nbsp;&nbsp;• Pago de tasas estipulado en el Acuerdo Ministerial 083-B.</p></div>' !!}
                    </textarea>
                </div>

                <!-- Sección Conclusiones -->
                <div class="editor-section">
                    <h2>Conclusiones</h2>
                    <textarea id="conclusiones-editor" name="conclusiones" class="tiny-editor">
                        {!! $plantilla->conclusiones ?? '<p>Una vez analizado la documentación ingresada, se puede constatar las siguientes facturas: <br><br>• N° <span class="variable"><strong>[numeroFacturaControl]</strong></span> de valor <span class="variable"><strong>[valorFacturaControl]</strong></span> por concepto de pago por control y seguimiento pcs. <br><br>• N° <span class="variable"><strong>[numeroFactura]</strong></span> de valor <span class="variable"><strong>[valorFactura]</strong></span> por pronunciamiento respecto a <span class="variable"><strong>[tipoObligacion]</strong></span>. <br><br>• Se concluye que la información Cumple con lo establecido en el Reglamento del Código Orgánico del Ambiente, publicado mediante Registro Oficial N° 507 del 17 de Junio del 2019, Art. 488 de los informes ambientales de cumplimiento. <br></p>' !!}
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