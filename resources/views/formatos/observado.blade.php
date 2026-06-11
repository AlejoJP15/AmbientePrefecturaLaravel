<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editor de Informe Observado</title>
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
            <h1>Edición De La Plantilla Para Informe Observado</h1>
            <p class="subtitle">Por favor ingrese los datos solicitados</p>

            <form id="formPlantilla" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="plantilla_id" value="{{ $plantilla->id_pformato }}">

                <!-- Sección Asunto -->
                <div class="editor-section">
                    <h2>Asunto</h2>
                    <textarea id="asunto-editor" name="asunto" class="tiny-editor">
                        {!! $plantilla->asunto ?? '<div class="template-variable"><p>OBSERVACIONES <span class="variable"><strong>[tipoObligacion]</span></strong> DEL PROYECTO <span class="variable"><strong>"[nombreProyecto]"</strong></span> PERIODO <span><strong>[periodo]</strong></span></p></div>' !!}
                    </textarea>
                </div>

                <!-- Sección Antecedentes -->
                <div class="editor-section">
                    <h2>Antecedentes</h2>
                    <textarea id="antecedentes-editor" name="antecedentes" class="tiny-editor">
                        {!! $plantilla->antecedentes ?? '<div class="template-text"><p>Mediante Oficio Nro. <span class="variable"><strong>[numeroOficio]</strong></span> de fecha <span class="variable"><strong>[fechaOficio]</strong></span>, ingresado a esta dependencia administrativa el <span class="variable"><strong>[fechaIngreso]</strong></span> con código <span class="variable"><strong>[numeroTramite]</strong></span>, remite el <strong><span class="variable">[nombreSolicitante]</span></strong>, el <span class="variable"><strong>[tipoObligacion]</strong></span> del Proyecto.</p></div>' !!}
                    </textarea>
                </div>

                <!-- Sección Objetivos -->
                <div class="editor-section">
                    <h2>Objetivos</h2>
                    <textarea id="objetivos-editor" name="objetivos" class="tiny-editor">
                        {!! $plantilla->objetivos ?? '<div class="legal-text"><p>Verificar el cumplimiento del Art. 488 de los informes ambientales de cumplimiento, establecido en el Reglamento al Código Orgánico del Ambiente publicado mediante Registro Oficial N° 507 del 17 de Junio del 2019.</p></div>' !!}
                    </textarea>
                </div>

                <!-- Sección Detalle -->
                <div class="editor-section">
                    <h2>Detalle</h2>
                    <textarea id="detalle-editor" name="detalle" class="tiny-editor">
                        {!! $plantilla->detalle ?? '<p>1. Mejorar la Introducción, Antecedentes, Ficha técnica, Objetivos, Alcance, Normativa legal, Metodología y Descripción general de proyecto para lo cual se sugiere descargar el formato establecido en la página web <a href="https://www.ambiente.chimborazo.gob.ec" target="_blank">www.ambiente.chimborazo.gob.ec</a>, menú biblioteca, para la guía en la elaboración y presentación del documento en mención.</p>' !!}
                    </textarea>
                </div>

                <!-- Sección Conclusiones -->
                <div class="editor-section">
                    <h2>Conclusiones</h2>
                    <textarea id="conclusiones-editor" name="conclusiones" class="tiny-editor">
                        {!! $plantilla->conclusiones ?? '<p>La documentación que consta en el <span class="variable"><strong>[tipoObligacion]</strong></span> del Proyecto "<span class="variable"><strong> [nombreProyecto] </strong></span>", <span class="variable"><strong>[valorCumple]</strong></span> con los requerimientos establecidos en la Normativa Ambiental Vigente por lo que esta Dependencia Administrativa <span class="variable"><strong>[valorAcepta]</strong></span> el <span class="variable"><strong>[tipoObligacion]</strong></span> correspondiente al proyecto "<span class="variable"><strong> [nombreProyecto] </strong></span>", ubicado en el Cantón <span class="variable"><strong>[nombreCanton]</strong></span>, Provincia de <span class="variable"><strong>[nombreProvincia]</strong></span> debiendo el Representante legal atender los siguientes requerimientos, el cual no podrá ser superior a <strong>20 DÍAS</strong> a partir de su notificación conforme a lo establecido en el Reglamento al Código Orgánico del Ambiente, Art. 516 Respuesta a las notificaciones de la Autoridad Ambiental; en tal virtud se recomienda a la Autoridad Ambiental, solicitar información ampliatoria a la documentación presentada.</p>' !!}
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