<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editor de Oficio Pronunciamiento</title>
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
            <h1>Edición de la Plantilla para Oficio Pronunciamiento Favorable</h1>
            <p class="subtitle">Por favor ingrese los datos solicitados</p>

            <form id="formPlantilla" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="plantilla_id" value="{{ $plantilla->id_pformato }}">

                <!-- Sección Asunto -->
                <div class="editor-section">
                    <h2>Asunto</h2>
                    <textarea id="asunto-editor" name="asunto" class="tiny-editor">
                        {!! $plantilla->asunto ?? '<div class="template-variable"><p>PRONUNCIAMIENTO FAVORABLE AL <span class="variable"><strong>[tipoObligacion]</strong></span> DEL PROYECTO "<span class="variable"><strong>[nombreProyecto]</strong></span>" PERIODO <span class="variable"><strong>[periodo]</strong></span></p></div>' !!}
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
                        {!! $plantilla->cuerpo ?? '<div class="template-text"><div class="documento"><p><span class="variable"><strong>[antecedentes]</strong></span></p><p>Al respecto, una vez realizado el análisis correspondiente y sobre la base del <span class="variable"><strong>[numeroInforme]</strong></span> del <span class="variable"><strong>[fechaInforme]</strong></span> remitido con MEMORANDO <span class="variable"><strong>[numeroMemo]</strong></span> del <span class="variable"><strong>[fechaMemo]</strong></span>, <span class="variable"><strong>[valorCumple]</strong></span> con las disposiciones y legales establecido dentro del Reglamento al Código Orgánico del Ambiente publicado mediante Registro Oficial N° 507 del 17 de Junio del 2019, Art. 488 de los informes ambientales de cumplimiento. Sin embargo previo al pronunciamiento final se dispone la cancelación de tasas por servicios ambientales que presta el Gobierno Autónomo Descentralizado de la Provincia de Chimborazo al estar acreditada como Autoridad Ambiental de Aplicación Responsable.</p><p>Esta cancelación se debe realizar a la cuenta corriente N° 7534655 del Banco del Pacífico, reguladas dentro del Acuerdo Ministerial N°083-B, Registro Oficial Nº 387, publicado el 04 de Noviembre del 2015 mediante el cual reforma al libro IX del Texto Unificado de Legislación Secundaria del Ministerio del Ambiente y se detalla en la siguiente tabla:</p><table class="tabla-pagos"><thead><tr><th>PAGOS POR SERVICIOS ADMINISTRATIVOS DE REGULARIZACIÓN, CONTROL Y SEGUIMIENTO</th><th>DERECHO ASIGNADO</th><th>USD</th></tr></thead><tbody><tr><td>Pronunciamiento respecto a informes ambientales de cumplimiento</td><td>10% del costo total de la elaboración del informe</td><td>Mínimo USD 50.00</td></tr><tr><td>Pago por control y seguimiento (PCS)</td><td>PCS=PID *Nt*Nd</td><td>PCS=80*1*1=80.00</td></tr></tbody></table><p>El oficio de petición y la presentación de las facturas electrónicas lo realizará a través de la siguiente página web <a href="https://www.ambiente.chimborazo.gob.ec" target="_blank">www.ambiente.chimborazo.gob.ec</a>, en el link del sistema de control y calidad ambiental, de la Prefectura de Chimborazo.</p></div><style>.documento {font-family: Arial, sans-serif;line-height: 1.6;color: #333;}.variable {background-color: #fff8e1;padding: 2px 5px;border-radius: 3px;font-family: monospace;color: #d35400;font-weight: bold;}.tabla-pagos {width: 100%;border-collapse: collapse;margin: 20px 0;font-size: 0.9em;}.tabla-pagos th {background-color: #2c3e50;color: white;padding: 12px 15px;text-align: left;font-weight: bold;}.tabla-pagos td {padding: 12px 15px;border-bottom: 1px solid #e0e0e0;}.tabla-pagos tr:nth-child(even) {background-color: #f5f5f5;}.tabla-pagos tr:hover {background-color: #e9e9e9;}</style></div>' !!}
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