<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\ObligacionController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\CatalogoObligacionesController;
use App\Http\Controllers\TecnicoController;
use App\Http\Controllers\NacionalidadController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\PlantillaFormatoController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;

Route::get('/', function () {
    return redirect()->route('login');
});


// Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Registro
Route::get('/registro', [PersonaController::class, 'create'])->name('registro');
Route::get('/condiciones', function () {
    return view('persona.condiciones');
})->name('condiciones');

// Dashboards según rol
Route::middleware(['auth', 'role:Administrador'])->get('/admin/dashboard', function () {
    return view('dashboards.admin');
})->name('admin.dashboard');

Route::middleware(['auth', 'role:Técnico'])->get('/tecnico/dashboard', function () {
    return view('dashboards.tecnico');
})->name('tecnico.dashboard');

Route::middleware(['auth', 'role:Coordinador'])->get('/coordinador/dashboard', function () {
    return view('dashboards.coordinador');
})->name('coordinador.dashboard');

Route::middleware(['auth', 'role:Director'])->get('/director/dashboard', function () {
    return view('dashboards.director');
})->name('director.dashboard');

Route::middleware(['auth', 'role:Usuario externo'])->get('/usuario/dashboard', function () {
    return view('dashboards.usuario');
})->name('usuario.dashboard');




// Rutas Admin protegidas
Route::middleware(['auth', 'role:Administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // --- LO QUE YA TENÍAS ---
        Route::get('/personas/{id}/edit', [PersonaController::class, 'editAdmin'])
            ->name('personas.edit');
        Route::put('/personas/{id}', [PersonaController::class, 'updateAdmin'])
            ->name('personas.update');

        Route::get('/administrador/proyectos', [ProyectoController::class, 'indexTodos'])
            ->name('proyectos.index.todos');
            
        // --- NUEVO: PROYECTOS ---
        Route::get('/proyectos', [ProyectoController::class, 'adminIndex'])
            ->name('proyectos.index');        // admin.proyectos.index

        Route::get('/proyectos/{proyecto}/editar', [ProyectoController::class, 'editAdmin'])
            ->whereNumber('proyecto')
            ->name('proyectos.edit');         // admin.proyectos.edit

        Route::put('/proyectos/{proyecto}', [ProyectoController::class, 'updateAdmin'])
            ->whereNumber('proyecto')
            ->name('proyectos.update');       // admin.proyectos.update

        Route::resource('tipos-obligacion', \App\Http\Controllers\TipoObligacionController::class)
            ->parameters(['tipos-obligacion' => 'id'])   // para que los métodos reciban $id
            ->names('tipos-obligacion');                 // genera admin.tipos-obligacion.*

        // Activar/Desactivar (opcional, tu controlador lo trae)
        Route::post('tipos-obligacion/{id}/toggle', [\App\Http\Controllers\TipoObligacionController::class, 'toggle'])
            ->whereNumber('id')->name('tipos-obligacion.toggle');

        // === Ítems de obligación ===
        Route::resource('items-obligacion', \App\Http\Controllers\ItemObligacionController::class)
            ->parameters(['items-obligacion' => 'id'])
            ->names('items-obligacion');                 // genera admin.items-obligacion.*

        Route::post('items-obligacion/{id}/toggle', [\App\Http\Controllers\ItemObligacionController::class, 'toggle'])
            ->whereNumber('id')->name('items-obligacion.toggle');
    });

Route::middleware(['auth', 'role:Director|Administrador'])
    ->prefix('director')
    ->name('director.')
    ->group(function () {
        // Obligaciones: listado (sin mapas) y resumen
        Route::get('/obligaciones/listado-general', [\App\Http\Controllers\ObligacionController::class, 'listadoResumenGeneralDirector'])
            ->name('obligaciones.listadoGeneral');
        Route::get('/obligaciones/listado-asignado', [\App\Http\Controllers\ObligacionController::class, 'listadoResumenAsignadoDirector'])
            ->name('obligaciones.listadoAsignado');
        Route::get('/obligaciones/listado-pendiente', [\App\Http\Controllers\ObligacionController::class, 'listadoResumenPendienteDirector'])
            ->name('obligaciones.listadoPendiente');
        Route::get('/obligaciones/{obligacion}/resumen', [ObligacionController::class, 'resumenDirector'])
            ->name('obligaciones.resumen');

        // Nueva ruta para asignar proyecto
        Route::post('/proyectos/{proyecto}/asignar', [\App\Http\Controllers\ProyectoController::class, 'asignarCoordinador'])
            ->name('proyectos.asignar');

        // Actualizar dictamen del director
        Route::put('/obligaciones/{obligacion}/dictamen', [ObligacionController::class, 'dictamenDirectorUpdate'])
            ->name('obligaciones.dictamen.update');

        // Validación de firma electrónica (.p12) del director
        Route::post('/obligaciones/{obligacion}/firma/validar', [\App\Http\Controllers\ObligacionController::class, 'validarFirmaP12'])
            ->name('obligaciones.firma.validar');


        // NUEVO: Listado de obligaciones que el coordinador marcó como "Pendiente evaluación del director"
        Route::get(
            '/obligaciones/aprobados',
            [\App\Http\Controllers\ObligacionController::class, 'listadoAprobadosDirector']
        )->name('obligaciones.aprobados');

    
    });

Route::middleware(['auth', 'role:Coordinador|Administrador'])
    ->prefix('coordinador')
    ->name('coordinador.')
    ->group(function () {
        Route::get('/obligaciones/listado-general', [\App\Http\Controllers\ObligacionController::class, 'listadoResumenGeneralCoordinador'])
            ->name('obligaciones.listadoGeneral');
        Route::get('/obligaciones/listado-asignaciones', [\App\Http\Controllers\ObligacionController::class, 'listadoResumenAsignadoCoordinador'])
            ->name('obligaciones.listadoAsignado');
        Route::get('/obligaciones/listado-pendientes', [\App\Http\Controllers\ObligacionController::class, 'listadoResumenPendienteCoordinador'])
            ->name('obligaciones.listadoPendiente');
        Route::post('/obligaciones/asignar-obligacion', [ObligacionController::class, 'asignarObligacion'])->name('obligacion.asignar');

        Route::get('/obligaciones/{obligacion}/resumen', [ObligacionController::class, 'resumenCoordinador'])
            ->name('obligaciones.resumen');
        Route::put('/obligaciones/{obligacion}/dictamen', [ObligacionController::class, 'dictamenCoordinadorUpdate'])
            ->name('obligaciones.dictamen.update');
        Route::post('/obligaciones/{obligacion}/firma/validar', [\App\Http\Controllers\ObligacionController::class, 'validarFirmaP12'])
            ->name('obligaciones.firma.validar');
        

        
        // NUEVO: Aprobados por técnico (para Coordinador)
        Route::get(
            '/obligaciones/aprobados',
            [\App\Http\Controllers\ObligacionController::class, 'listadoAprobadosCoordinador']
        )->name('obligaciones.aprobados');

    
    
    });
    Route::middleware(['auth']) // puedes poner ['auth','role:Administrador'] si quieres
        ->prefix('admin/api')
        ->name('admin.api.')
        ->group(function () {
            Route::get('/provincias',               [ProyectoController::class, 'apiProvincias'])->name('provincias');
            Route::get('/cantones/{provincia}',     [ProyectoController::class, 'apiCantones'])->name('cantones');
            Route::get('/parroquias/{canton}',      [ProyectoController::class, 'apiParroquias'])->name('parroquias');
        });

// Mostrar perfil del usuario autenticado
Route::middleware(['auth'])->get('/perfil', [PersonaController::class, 'perfil'])->name('persona.perfil');

// Actualizar perfil del usuario autenticado
Route::middleware(['auth'])->post('/perfil', [PersonaController::class, 'updatePerfil'])->name('persona.perfil.update');

// Público: SOLO el POST de registro
Route::post('personas', [PersonaController::class, 'store'])
    ->name('personas.store'); // sin middleware (ni auth ni guest)
    // ->middleware('throttle:10,1'); // opcional, para evitar spam

// Privado: todo lo demás del resource
Route::resource('personas', PersonaController::class)
    ->except(['store'])
    ->middleware('auth');

Route::post('/perfil/password', [PersonaController::class, 'updatePassword'])
    ->name('persona.password.update')
    ->middleware('auth');

Route::middleware(['auth', 'role:Usuario externo|Administrador'])
    ->prefix('usuario')
    ->name('usuario.')
    ->group(function () {
        
        Route::get('/inbox', [ObligacionController::class, 'inbox'])->name('inbox');

        Route::get('/proyectos', [\App\Http\Controllers\ProyectoController::class, 'index'])
        ->name('proyectos.index');

        // -> usa el controlador que arma la pantalla (selector + form)
        Route::get('/proyectos/crear', [ActividadController::class, 'index'])->name('proyectos.create');

        Route::post('/proyectos', [ProyectoController::class, 'store'])->name('proyectos.store');

        Route::get('/proyectos/{id}', [ProyectoController::class, 'show'])->name('proyectos.show');
        // Si quieres proteger estos endpoints también:
        Route::get('/actividades/roots', [ActividadController::class, 'roots'])->name('actividades.roots');
        Route::get('/actividades/children/{codigo}', [ActividadController::class, 'children'])->name('actividades.children');
        Route::get('/actividades/is-leaf/{codigo}', [ActividadController::class, 'isLeaf'])->name('actividades.isLeaf');
        
        Route::post('/proyectos/{proyecto}/comentarios/marcar-leidos',
            [\App\Http\Controllers\ProyectoController::class, 'marcarComentariosLeidos']
        )->name('proyectos.marcarLeidos');
            
            
    
    });
    
/* ------------------ PERSONAS (público, sin auth) ------------------ */
Route::prefix('api')->name('api.personas.')->group(function () {
    Route::get('/provincias',               [PersonaController::class, 'getProvincias'])->name('provincias');
    Route::get('/cantones/{provincia}',     [PersonaController::class, 'getCantonesPorProvincia'])->name('cantones');
    Route::get('/parroquias/{canton}',      [PersonaController::class, 'getParroquiasPorCanton'])->name('parroquias');
});


Route::middleware(['auth'])
    ->prefix('usuario/api')->name('usuario.api.')->group(function () {
        Route::get('/provincias',               [ProyectoController::class, 'apiProvincias'])->name('provincias');
        Route::get('/cantones/{provincia}',     [ProyectoController::class, 'apiCantones'])->name('cantones');
        Route::get('/parroquias/{canton}',      [ProyectoController::class, 'apiParroquias'])->name('parroquias');
    });


Route::post(
    '/usuario/proyecto/{proyecto}/obligaciones',
    [ObligacionController::class, 'store']
)->name('obligaciones.store')->middleware('auth');

Route::middleware(['auth'])->prefix('catalogos')->group(function () {
    Route::get('/tipos-obligacion', [CatalogoObligacionesController::class, 'tipos'])
         ->name('catalogos.tipos');
    Route::get('/items-obligacion', [CatalogoObligacionesController::class, 'items'])
         ->name('catalogos.items'); // ?id_tipo=123
});

Route::middleware('auth')->group(function () {
  // Pantalla de adjuntar documentos
  Route::get('/usuario/obligaciones/{obligacion}/documentos', [ObligacionController::class, 'documentos'])
    ->name('obligaciones.documentos');

  // Subir PDF(s)
  Route::post('/usuario/obligaciones/{obligacion}/documentos', [ObligacionController::class, 'documentosStore'])
    ->name('obligaciones.documentos.store');

  // Descargar un archivo
  Route::get('/usuario/obligaciones/{obligacion}/documentos/descargar', [ObligacionController::class, 'documentosDownload'])
    ->name('obligaciones.documentos.download');

  // Eliminar un archivo
  Route::delete('/usuario/obligaciones/{obligacion}/documentos', [ObligacionController::class, 'documentosDestroy'])
    ->name('obligaciones.documentos.destroy');

  // Guardar comentarios (resumen) de la obligación
  Route::put('/usuario/obligaciones/{obligacion}/comentarios', [ObligacionController::class, 'guardarComentarios'])
    ->name('obligaciones.comentarios.update');
});

Route::middleware(['auth', 'role:Técnico|Administrador'])
    ->prefix('tecnico')
    ->name('tecnico.')
    ->group(function () {
        // Dashboard (puedes redirigir al listado)
        Route::get('/', [TecnicoController::class, 'home'])->name('home');

        // Obligaciones: listado + mapa
        Route::get('/obligaciones/listado', [\App\Http\Controllers\ObligacionController::class, 'listadoResumenTecnico'])
            ->name('obligaciones.listado');
        Route::get('/obligaciones/{obligacion}/resumen', [ObligacionController::class, 'resumenTecnico'])
            ->name('obligaciones.resumen');

        Route::get('/mapas/proyectos', [ProyectoController::class, 'indexProyectosMapa'])
            ->name('proyectos.index');

        Route::get('/mapas/proyectos/{id}/mapa', [ProyectoController::class, 'verMapa'])
            ->whereNumber('id')
            ->name('proyectos.mapa');

        Route::put('/obligaciones/{obligacion}/dictamen', [ObligacionController::class, 'dictamenTecnicoUpdate'])
            ->name('obligaciones.dictamen.update');

        // Proyectos (listado simple)
        Route::get('/proyectos', [TecnicoController::class, 'proyectos'])
            ->name('proyectos');

        // Validación de firma electrónica (.p12) del técnico
        Route::post('/obligaciones/{obligacion}/firma/validar',
            [\App\Http\Controllers\ObligacionController::class, 'validarFirmaP12']
        )->name('obligaciones.firma.validar');


        Route::get('/openssl/debug', function () {
            $ciphers = array_map('strtolower', openssl_get_cipher_methods(true) ?: []);
            $mds     = array_map('strtolower', openssl_get_md_methods(true) ?: []);
            return response()->json([
                'OPENSSL_VERSION_TEXT' => OPENSSL_VERSION_TEXT,
                'OPENSSL_CONF'         => getenv('OPENSSL_CONF'),
                'has_rc2'              => in_array('rc2-40-cbc', $ciphers) || in_array('rc2-cbc', $ciphers),
                'has_des3'             => in_array('des-ede3-cbc', $ciphers),
                'sample_ciphers'       => array_values(array_intersect($ciphers, ['rc2-40-cbc','rc2-cbc','des-ede3-cbc','aes-256-cbc'])),
                'has_sha1'             => in_array('sha1', $mds),
            ]);
        })->name('openssl.debug');


        // NUEVO: Listado de aprobados por técnico (solo esta sección)
        Route::get(
            '/obligaciones/aprobados',
            [\App\Http\Controllers\ObligacionController::class, 'listadoAprobadosTecnico']
        )->name('obligaciones.aprobados');

    });




Route::resource('nacionalidades', NacionalidadController::class);

//tipos de obligacion
Route::get('/tipos-obligacion', function () {
    return view('obligacion.index');
})->name('obligacion.index');

//catastro
Route::get('/catastro', function () {
    return view('catastro.index');
})->name('catastro.index');

// Ruta para la vista principal de reportes
Route::get('/reportes', function () {
    return view('reportes.documentos');
})->name('reportes.documentos');

// Ruta para generar el reporte de coordenadas
Route::get('/reportes/coordenadas', function () {
    return view('reportes.coordenadas');
})->name('reportes.coordenadas');

//ruta para formatos
Route::get('/formatos', function () {
    return view('formatos.index');
})->name('formatos.index');

// Ruta para el archivo de trámites
Route::get('/archivo', function () {
    return view('archivo.index');
})->name('archivo.index');

// Ruta para el formato de informe observado
Route::get('/formatos/observado', function () {
    return view('formatos.observado');
})->name('formatos.observado');

// Ruta para el mapa
Route::get('/mapa', function () {
    return view('mapa.proyectos');
})->name('mapa.proyectos');

Route::get('/mapa/proyectos-canton', function () {
    return view('mapa.proyectos_canton');
})->name('mapa.proyectos_canton');

// Ruta para el formato de informe aprobación
Route::get('/formatos/aprobacion', function () {
    return view('formatos.aprobacion');
})->name('formatos.aprobacion');

// Ruta para el actualizat proyecto
Route::get('/actualizar_proyecto', function () {
    return view('actualizar_proyecto.index');
})->name('actualizar_proyecto.index');

// Ruta para el formato de informe de pronunciamiento favorable
Route::get('/formatos/infpronunciamiento', function () {
    return view('formatos.infpronunciamiento');
})->name('formatos.infpronunciamiento');

// Ruta para el formato de memo de aprobación
Route::get('/formatos/memoaprobacion', function () {
    return view('formatos.memoaprobacion');
})->name('formatos.memoaprobacion');

// Ruta para el formato de memo observado
Route::get('/formatos/memoobservado', function () {
    return view('formatos.memoobservado');
})->name('formatos.memoobservado');

// Ruta para el formato de memo pronunciamiento
Route::get('/formatos/memopronunciamiento', function () {
    return view('formatos.memopronunciamiento');
})->name('formatos.memopronunciamiento');

// Ruta para el formato de oficio aprobación
Route::get('/formatos/ofiaprobacion', function () {
    return view('formatos.ofiaprobacion');
})->name('formatos.ofiaprobacion');

// Ruta para el formato de oficio observado
Route::get('/formatos/ofiobservado', function () {
    return view('formatos.ofiobservado');
})->name('formatos.ofiobservado');

// Ruta para el formato de oficio pronunciamiento
Route::get('/formatos/ofipronunciamiento', function () {
    return view('formatos.ofipronunciamiento');
})->name('formatos.ofipronunciamiento');

// Ruta para el formato de resolución
Route::get('/formatos/resolucion', function () {
    return view('formatos.resolucion');
})->name('formatos.resolucion');

// Ruta para el Obligaciones Director
Route::get('/obligacionesD/listado', function () {
    return view('obligacionesD.listado');
})->name('obligacionesD.listado');

Route::get('/obligacionesD/listadoG', function () {
    return view('obligacionesD.listadoG');
})->name('obligacionesD.listadoG');

// Ruta para el Obligaciones Coordinador
Route::get('/obligacionC/listado', function () {
    return view('obligacionC.listado');
})->name('obligacionC.listado');

Route::get('/obligacionC/listadoPendiente', function () {
    return view('obligacionC.listadoPendiente');
})->name('obligacionC.listadoPendiente');

Route::get('/obligacionC/listadoAsigandos', function () {
    return view('obligacionC.listadoAsignados');
})->name('obligacionC.listadoAsignados');

// Ruta para el Obligaciones Tecnico
Route::get('/obligacionT/listado', function () {
    return view('obligacionT.listado');
})->name('obligacionT.listado');

Route::get('/obligacionT/listadoG', function () {
    return view('obligacionT.listadoG');
})->name('obligacionT.listadoG');

//nuevas rutas de Formatos editados y guardados en la base de datos
// Rutas para Formatos/Plantillas

Route::middleware(['auth'])->prefix('formatos')->name('formatos.')->group(function () {
    
    // Listado principal de formatos
    Route::get('/', [PlantillaFormatoController::class, 'index'])->name('index');
    
    // INFORMES
    Route::get('/aprobacion', [PlantillaFormatoController::class, 'editInformeAprobacion'])->name('aprobacion');
    Route::get('/observado', [PlantillaFormatoController::class, 'editInformeObservado'])->name('observado');
    Route::get('/infpronunciamiento', [PlantillaFormatoController::class, 'editInformePronunciamiento'])->name('infpronunciamiento');
    
    // MEMOS
    Route::get('/memoaprobacion', [PlantillaFormatoController::class, 'editMemoAprobacion'])->name('memoaprobacion');
    Route::get('/memoobservado', [PlantillaFormatoController::class, 'editMemoObservado'])->name('memoobservado');
    Route::get('/memopronunciamiento', [PlantillaFormatoController::class, 'editMemoPronunciamiento'])->name('memopronunciamiento');
    
    // OFICIOS
    Route::get('/ofiaprobacion', [PlantillaFormatoController::class, 'editOficioAprobacion'])->name('ofiaprobacion');
    Route::get('/ofiobservado', [PlantillaFormatoController::class, 'editOficioObservado'])->name('ofiobservado');
    Route::get('/ofipronunciamiento', [PlantillaFormatoController::class, 'editOficioPronunciamiento'])->name('ofipronunciamiento');
    
    // RESOLUCIÓN
    Route::get('/resolucion', [PlantillaFormatoController::class, 'editResolucion'])->name('resolucion');
    
    // GUARDAR/ACTUALIZAR (POST/PUT)
    Route::put('/{id}', [PlantillaFormatoController::class, 'update'])->name('update');
});

//FIRMAR DOCUMENTO

Route::post('/documentos/{id}/firmar', [DocumentoController::class, 'firmar'])->name('documentos.firmar');

// Ruta temporal para migrar archivos
Route::get('/admin/migrar-archivos', [ObligacionController::class, 'migrarArchivosABD'])->middleware('auth');

// Recuperacion de contraseña
Route::middleware('guest')->group(function () {
    Route::get('/recuperar', [ForgotPasswordController::class,'showLinkRequestForm'])->name('password.request');
    Route::post('/recuperar', [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');

    Route::get('/recuperar/{token}', [ResetPasswordController::class,'showResetForm'])->name('password.reset');
    Route::post('/recuperar/{token}', [ResetPasswordController::class,'reset'])->name('password.update');
});
