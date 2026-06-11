<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Proyecto;
use App\Models\Comentario;
use App\Models\Obligacion;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ItemObligacion;
use App\Models\TipoObligacion;
use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Models\Archivo;

class ObligacionController extends Controller
{
    use ValidatesRequests;

    public function store(Request $request, Proyecto $proyecto)
    {
        // 1) Validación básica (FKs + campos)
        $data = $request->validate([
            'id_tipo'   => ['required', 'integer', 'exists:tipos_obligacion,id_tipo'],
            'id_item'   => ['required', 'integer', 'exists:items_obligacion,id_item'],

            // Estos snapshots pueden venir o no desde el front; los sobreescribiremos desde BD
            'tipo_obligacion' => ['nullable', 'string', 'max:120'],
            'descripcion'     => ['nullable', 'string', 'max:255'],

            // Fechas crudas del picker (opcionales; obligatorias si el tipo requiere periodo)
            'periodo_desde'   => ['nullable', 'date'],
            'periodo_hasta'   => ['nullable', 'date'],

            // Periodo final (lo armamos nosotros; límite acorde a tu schema)
            'periodo'         => ['nullable', 'string', 'max:50'],

            'resumen'         => ['nullable', 'string'],
        ]);

        // 2) Cargar catálogo y validar consistencia
        $tipo  = TipoObligacion::where('id_tipo', $data['id_tipo'])->where('activo', true)->firstOrFail();
        $item  = ItemObligacion::where('id_item', $data['id_item'])->where('activo', true)->firstOrFail();

        if ((int)$item->id_tipo !== (int)$tipo->id_tipo) {
            return back()->withErrors(['id_item' => 'La opción seleccionada no pertenece al tipo elegido.'])->withInput();
        }

        // 3) Periodo: obligatorio solo si el tipo lo requiere
        $periodoTexto = null;
        if ($tipo->requiere_periodo) {
            if (!$request->filled(['periodo_desde', 'periodo_hasta'])) {
                return back()->withErrors([
                    'periodo_desde' => 'El campo Desde es obligatorio para este tipo.',
                    'periodo_hasta' => 'El campo Hasta es obligatorio para este tipo.',
                ])->withInput();
            }

            $desde = Carbon::parse($request->input('periodo_desde'));
            $hasta = Carbon::parse($request->input('periodo_hasta'));

            if ($desde->gt($hasta)) {
                return back()->withErrors([
                    'periodo_hasta' => 'La fecha Hasta debe ser mayor o igual a la fecha Desde.',
                ])->withInput();
            }

            // Formato final que guardas en tu columna periodo (varchar)
            $periodoTexto = $desde->format('d/m/Y') . ' - ' . $hasta->format('d/m/Y');
        } else {
            $periodoTexto = null; // no aplica
        }

        // 4) Id del proyecto desde la ruta
        $idProyecto = $proyecto->id_proyecto ?? $proyecto->id;

        // 5) Crear obligación (snapshots de texto desde BD, NO del request)
        Obligacion::create([
            'descripcion'     => $item->descripcion,            // snapshot
            'id_proyecto'     => $idProyecto,
            'tipo_obligacion' => strtoupper($tipo->nombre),     // snapshot
            'periodo'         => $periodoTexto,                 // 'dd/mm/YYYY - dd/mm/YYYY' o null
            'estado'          => 'En tramite',                  // por defecto
            'resumen'         => $data['resumen'] ?? null,
            'id_tipo'         => $tipo->id_tipo,
            'id_item'         => $item->id_item,
        ]);

        return back()->with('success', 'Obligación registrada correctamente.');
    }
    // Página "Adjuntar documento"
    // Página "Adjuntar documento"
    public function documentos(Obligacion $obligacion)
    {
        $persona   = Auth::user();
        $currentId = $persona->id_persona ?? $persona->id ?? null;
        if (!$currentId) abort(401);

        // Detectar admin
        $isAdmin = method_exists($persona, 'hasRole')
            ? ($persona->hasRole('Administrador') || $persona->hasRole('Admin'))
            : (strcasecmp(trim(optional($persona->perfil)->descripcion), 'Administrador') === 0
                || strcasecmp(trim(optional($persona->perfil)->descripcion), 'Admin') === 0);

        // Traer el proyecto
        $query = \App\Models\Proyecto::query()
            ->where('id_proyecto', $obligacion->id_proyecto)
            ->select('proyecto.*')
            ->with([
                'provincia:id_provincia,provincia',
                'canton'     => fn($q) => $q->select('id_canton', 'nombre_canton as canton'),
                'parroquia'  => fn($q) => $q->select('id_parroquia', 'nombre_parroquia as parroquia'),
                'actividad:id_actividad,codigo_actividad,descripcion_actividad',
            ]);

        if (!$isAdmin) {
            $query->where('id_usuario', $currentId);
        }

        $proyecto = $query->firstOrFail();

        $clean = function (?string $raw): ?string {
            if ($raw === null) return null;
            $s = trim($raw);
            if (preg_match('/^\(\s*\d+\s*,\s*("?)(.+?)\1(?:\s*,\s*\d+)?\s*\)$/u', $s, $m)) {
                $s = $m[2];
            }
            return trim($s, " \t\n\r\0\x0B\"'\"\"''«»‹›„‚");
        };

        if ($proyecto->provincia) $proyecto->provincia->provincia = $clean($proyecto->provincia->provincia);
        if ($proyecto->canton)    $proyecto->canton->canton       = $clean($proyecto->canton->canton);
        if ($proyecto->parroquia) $proyecto->parroquia->parroquia = $clean($proyecto->parroquia->parroquia);

        $dir = "obligaciones/{$obligacion->id_obligacion}";
        $bloqueado = Storage::disk('public')->exists($dir . '/.lock');

        // ============================================
        // CARGAR ARCHIVOS SOLO DESDE LA BASE DE DATOS
        // ============================================
        $files = \App\Models\Archivo::where('id_obligacion', $obligacion->id_obligacion)
            ->orderBy('fecha_creacion', 'desc')
            ->get()
            ->map(function ($archivo) use ($dir) {
                return [
                    'name'       => $archivo->nombre_archivo,
                    'path'       => $dir . '/' . $archivo->nombre_archivo, // Ruta para descargar
                    'url'        => $archivo->url,
                    'tipo'       => $archivo->tipo ?? 'PDF',
                    'created_at' => $archivo->fecha_creacion 
                        ? \Carbon\Carbon::parse($archivo->fecha_creacion)->format('d/m/Y H:i') 
                        : '—',
                ];
            });

        $ultimaSubida = $files->isNotEmpty() 
            ? $files->first()['created_at'] 
            : null;

        return view('obligaciones.adjuntar_documento', [
            'proyecto'     => $proyecto,
            'obligacion'   => $obligacion,
            'files'        => $files,
            'bloqueado'    => $bloqueado,
            'ultimaSubida' => $ultimaSubida,
        ]);
    }

    // Subir PDF y guardar en BD
    public function documentosStore(Request $request, Obligacion $obligacion)
    {
        $this->assertOwnership($obligacion);

        $dir = "obligaciones/{$obligacion->id_obligacion}";
        if (Storage::disk('public')->exists($dir . '/.lock')) {
            return back()->with('error', 'Esta obligación ya fue enviada. No se pueden subir archivos.');
        }

        $request->validate([
            'archivos'   => ['required', 'array'],
            'archivos.*' => ['file', 'mimes:pdf', 'max:10240'],
        ], [
            'archivos.required' => 'Selecciona al menos un archivo PDF.',
        ]);

        $tz = config('app.timezone');
        $archivosSubidos = 0;

        foreach ($request->file('archivos') as $file) {
            $orig = $file->getClientOriginalName();
            $safe = now()->setTimezone($tz)->format('Ymd_His') . '_' . preg_replace('/[^\w.\-]+/u', '_', $orig);

            // 1. Verificar si YA existe en la base de datos
            $existe = \App\Models\Archivo::where('id_obligacion', $obligacion->id_obligacion)
                ->where('nombre_archivo', $safe)
                ->exists();

            if ($existe) {
                Log::warning("Archivo duplicado ignorado: {$safe}");
                continue; // Saltar si ya existe
            }

            try {
                // 2. Guardar archivo físicamente
                Storage::disk('public')->putFileAs($dir, $file, $safe);

                // 3. Guardar metadata de fecha
                $metaPath = $dir . '/.' . $safe . '.uploaded_at';
                $stamp    = now()->setTimezone($tz)->format('Y-m-d H:i:s');
                Storage::disk('public')->put($metaPath, $stamp);

                // 4. GUARDAR EN LA BASE DE DATOS
                \App\Models\Archivo::create([
                    'id_proyecto'    => $obligacion->id_proyecto,
                    'id_obligacion'  => $obligacion->id_obligacion,
                    'nombre_archivo' => $safe,
                    'url'            => Storage::url($dir . '/' . $safe),
                    'tipo'           => 'PDF',
                    'fecha_creacion' => now()->setTimezone($tz),
                    'fecha_archivo'  => now()->setTimezone($tz),
                ]);

                $archivosSubidos++;

                Log::info("Archivo guardado exitosamente", [
                    'archivo' => $safe,
                    'obligacion' => $obligacion->id_obligacion,
                    'proyecto' => $obligacion->id_proyecto
                ]);

            } catch (\Exception $e) {
                Log::error("Error al subir archivo {$safe}: " . $e->getMessage());
                return back()->with('error', "Error al subir el archivo: {$e->getMessage()}");
            }
        }

        if ($archivosSubidos > 0) {
            return back()->with('success', "{$archivosSubidos} documento(s) subido(s) correctamente.");
        } else {
            return back()->with('error', 'No se subió ningún archivo nuevo. Puede que ya existan.');
        }
    }

    // Descargar PDF
    public function documentosDownload(Request $request, Obligacion $obligacion)
    {
        $this->validate($request, ['file' => ['required', 'string']]);
        
        // NUEVO: Verificar permisos ampliados (dueño, técnico asignado, admin)
        $this->assertDownloadPermission($obligacion);

        $path = $request->query('file');
        $dir  = "obligaciones/{$obligacion->id_obligacion}";
        if (!str_starts_with($path, $dir)) abort(403);

        $full = storage_path('app/public/' . $path);
        if (!file_exists($full)) abort(404);

        return response()->download($full);
    }

    // NUEVO: Método para verificar permisos de descarga
    protected function assertDownloadPermission(Obligacion $obligacion): void
    {
        $user = Auth::user();
        $currentId = $user->id_persona ?? $user->id ?? null;

        // 1. Verificar si es ADMIN (Administrador o Director o Coordinador)
        $isAdmin = method_exists($user, 'hasRole')
            ? ($user->hasRole('Administrador') || $user->hasRole('Admin') || $user->hasRole('Director') || $user->hasRole('Coordinador'))
            : (in_array(strtolower(trim(optional($user->perfil)->descripcion ?? '')), ['administrador', 'admin', 'director', 'coordinador']));

        if ($isAdmin) {
            return; // Admin puede descargar todo
        }

        // 2. Verificar si es el DUEÑO del proyecto
        $isOwner = Proyecto::where('id_proyecto', $obligacion->id_proyecto)
            ->where('id_usuario', $currentId)
            ->exists();

        if ($isOwner) {
            return; // Dueño puede descargar
        }

        // 3. Verificar si es el TÉCNICO ASIGNADO
        $isTecnicoAsignado = DB::table('asignaciones')
            ->where('id_tramite', $obligacion->id_proyecto)
            ->where('id_asignado', $currentId)
            ->exists();

        if ($isTecnicoAsignado) {
            return; // Técnico asignado puede descargar
        }

        // Si no cumple ninguna condición, denegar acceso
        abort(403, 'No tienes permisos para descargar este archivo');
    }

    // Eliminar PDF (del disco Y de la base de datos)
    public function documentosDestroy(Request $request, Obligacion $obligacion)
    {
        $this->assertOwnership($obligacion);

        $dir = "obligaciones/{$obligacion->id_obligacion}";
        if (Storage::disk('public')->exists($dir . '/.lock')) {
            return back()->with('error', 'Esta obligación ya fue enviada. No se pueden eliminar archivos.');
        }

        $data = $request->validate(['file' => ['required', 'string']]);
        $path = $data['file'];
        if (!str_starts_with($path, $dir)) abort(403);

        $nombreArchivo = basename($path);

        try {
            // 1. Eliminar de la base de datos
            $deleted = \App\Models\Archivo::where('id_obligacion', $obligacion->id_obligacion)
                ->where('nombre_archivo', $nombreArchivo)
                ->delete();

            // 2. Eliminar del disco
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            // 3. Eliminar metadata
            $metaPath = dirname($path) . '/.' . $nombreArchivo . '.uploaded_at';
            if (Storage::disk('public')->exists($metaPath)) {
                Storage::disk('public')->delete($metaPath);
            }

            if ($deleted) {
                Log::info("Archivo eliminado correctamente: {$nombreArchivo}");
                return back()->with('success', 'Documento eliminado correctamente.');
            } else {
                return back()->with('error', 'El archivo no se encontró en la base de datos.');
            }

        } catch (\Exception $e) {
            Log::error("Error al eliminar archivo: " . $e->getMessage());
            return back()->with('error', 'Error al eliminar el archivo.');
        }
    }

    // Helper para verificar que la obligación pertenece al usuario
    protected function assertOwnership(Obligacion $obligacion): void
    {
        $user       = Auth::user();
        $currentId  = $user->id_persona ?? $user->id ?? null;

        // Bypass admin (Spatie o perfil.descripcion)
        $isAdmin = method_exists($user, 'hasRole')
            ? ($user->hasRole('Administrador') || $user->hasRole('Admin'))
            : (strcasecmp(trim(optional($user->perfil)->descripcion), 'Administrador') === 0
                || strcasecmp(trim(optional($user->perfil)->descripcion), 'Admin') === 0);

        if ($isAdmin) {
            return; // Admin pasa sin verificar dueño
        }

        $owner = Proyecto::where('id_proyecto', $obligacion->id_proyecto)
            ->where('id_usuario', $currentId)
            ->exists();

        if (!$owner) abort(403);
    }

    /**
     * Mostrar la bandeja de entrada con las obligaciones del usuario autenticado
     */
    public function inbox()
    {
        $user = Auth::user();
        $userId = $user->id_persona ?? $user->id;

        // Traer proyectos del usuario + contar comentarios no leídos
        $proyectos = Proyecto::with([
                'actividad:id_actividad,descripcion_actividad',
                'estadoProyecto:id_estado_proyecto,descripcion',
            ])
            ->where('id_usuario', $userId)
            ->withCount([
                'comentarios as unread_count' => function ($q) {
                    $q->where('leido', false);
                }
            ])
            ->orderByDesc('fecha_creacion')
            ->get();

        return view('usuario.inbox', compact('proyectos'));
    }


    public function listadoResumenTecnico()
    {
        // Obtener el ID del técnico logueado
        $idTecnico = auth()->user()->id_persona;

        $obligaciones = DB::table('asignaciones as a')
            ->join('proyecto as p', 'a.id_tramite', '=', 'p.id_proyecto')
            ->join('obligaciones as o', 'o.id_proyecto', '=', 'p.id_proyecto')
            ->join('actividad as act', 'act.id_actividad', '=', 'p.id_actividad')
            ->join('estado_proyecto as e', 'e.id_estado_proyecto', '=', 'p.id_estado_proyecto')
            ->select([
                'o.id_obligacion',
                'o.id_proyecto',
                'o.fecha_registro',
                'o.descripcion as tipo_obligacion',
                'p.nombre as proyecto_nombre',
                'p.sector as proyecto_sector',
                'act.descripcion_actividad as actividad_descripcion',
                'e.descripcion as estado_descripcion'
            ])
            ->where('a.id_asignado', $idTecnico)
            
            ->orderBy('o.fecha_registro', 'desc')
            ->paginate(15);

        return view('tecnico.listado', compact('obligaciones'));
    }

    public function listadoAprobadosTecnico()
    {
        // Técnico logueado
        $idTecnico = auth()->user()->id_persona;

        $obligaciones = DB::table('asignaciones as a')
            ->join('proyecto as p', 'a.id_tramite', '=', 'p.id_proyecto')
            ->join('obligaciones as o', 'o.id_proyecto', '=', 'p.id_proyecto')
            ->join('actividad as act', 'act.id_actividad', '=', 'p.id_actividad')
            ->join('estado_proyecto as e', 'e.id_estado_proyecto', '=', 'p.id_estado_proyecto')
            ->select([
                'o.id_obligacion',
                'o.id_proyecto',
                'o.fecha_registro',
                'o.descripcion as tipo_obligacion',
                'p.nombre as proyecto_nombre',
                'p.sector as proyecto_sector',
                'act.descripcion_actividad as actividad_descripcion',
                'e.descripcion as estado_descripcion',
            ])
            ->where('a.id_asignado', $idTecnico)  // ← solo los asignados a mí
            ->where('p.id_estado_proyecto', 3)    // ← solo aprobados por técnico
            ->orderBy('o.fecha_registro', 'desc')
            ->paginate(15);

        // Puedes reutilizar la misma vista y pasar un título para diferenciar
        $titulo = 'Proyectos aprobados por el técnico';
        return view('tecnico.listado', compact('obligaciones', 'titulo'));
    }

    /**
     * Actualizar el dictamen técnico de una obligación
     * 
     * Maneja:
     * - Guardar decisión (Aprobado, Rechazado, Observaciones)
     * - Crear comentario en la tabla comentarios
     * - Actualizar estado del proyecto según la decisión
     * - Devolver al coordinador o al usuario externo
     */
    public function dictamenTecnicoUpdate(Request $request, $id_obligacion)
    {
        $obligacion = Obligacion::findOrFail($id_obligacion);
        $proyecto   = $obligacion->proyecto; // relación definida en el modelo

        if (!$proyecto) {
            return back()->with('error', 'Proyecto no encontrado.');
        }

        // Validación
        $validated = $request->validate([
            'decision'            => ['required', 'in:aprobado,rechazado,observaciones'],
            'comentario_decision' => ['nullable', 'string', 'max:2000'],
            'firma_verificada'    => ['required', 'boolean'],
        ]);

        $decision        = $validated['decision'];
        $comentarioInput = $validated['comentario_decision'] ?? null;
        $firmaVerificada = (bool) $validated['firma_verificada'];

        // Exigir firma solo si APRUEBA
        if ($decision === 'aprobado' && !$firmaVerificada) {
            return back()->with('error', 'Para aprobar un proyecto se requiere firma electrónica válida.');
        }

        // Map de estado del proyecto
        $estadoProyectoMap = [
            'aprobado'      => 3, // Aprobado
            'rechazado'     => 4, // Rechazado
            'observaciones' => 5, // Observaciones
        ];

        \DB::transaction(function () use (
            $proyecto, $obligacion, $decision, $estadoProyectoMap, $comentarioInput
        ) {
            // 1) Actualizar estado del PROYECTO
            $proyecto->id_estado_proyecto = $estadoProyectoMap[$decision];
            $proyecto->save();

            // 2) Actualizar estado de la OBLIGACIÓN (clave para el flujo del coordinador)
            $obligacion->estado = match ($decision) {
                'aprobado'      => 'Pendiente evaluación del coordinador',
                'rechazado'     => 'Rechazado (técnico)',
                'observaciones' => 'Observaciones (técnico)',
            };
            $obligacion->save();

            // 3) Dejar comentario para el inbox del usuario
            $autorId = optional(auth()->user())->id_persona ?? optional(auth()->user())->id;
            $prefijo = "[Técnico • {$decision}]";
            Comentario::create([
                'id_proyecto' => $proyecto->id_proyecto,
                'id_persona'  => $autorId,
                'descripcion' => $comentarioInput ? ($prefijo.' '.$comentarioInput) : $prefijo,
                // 'leido' => false, // si tu columna tiene default, no hace falta
                // 'fecha_comentario' => now(), // si la BD lo autogenera, omitir
            ]);
        });

        $msg = match ($decision) {
            'aprobado'      => 'Decisión registrada: aprobado. Pendiente evaluación del coordinador.',
            'rechazado'     => 'Decisión registrada: rechazado. El usuario fue notificado.',
            'observaciones' => 'Decisión registrada: observaciones. El usuario fue notificado.',
        };

        return back()->with('success', $msg);
    }


    // Guardar comentarios adicionales (se almacenan en obligaciones.resumen)
    public function guardarComentarios(Request $request, Obligacion $obligacion)
    {
        $this->assertOwnership($obligacion);

        $dir  = "obligaciones/{$obligacion->id_obligacion}";
        $disk = Storage::disk('public');

        // Si ya está bloqueada o ya tiene fecha_envio => no permitir
        $bloqueado = $disk->exists($dir . '/.lock') || !is_null($obligacion->fecha_envio);
        if ($bloqueado) {
            return back()->with('error', 'Esta obligación ya fue enviada. No se puede editar.');
        }

        // Validar el comentario (opcional)
        $data = $request->validate([
            'resumen' => ['nullable', 'string'],
        ]);

        // Debe existir al menos un PDF antes de "Enviar"
        $pdfs = collect($disk->files($dir))
            ->reject(fn($p) => Str::startsWith(basename($p), '.'))         // ignora archivos ocultos/metadata
            ->filter(fn($p) => Str::endsWith(Str::lower($p), '.pdf'))      // solo PDF
            ->values();

        if ($pdfs->isEmpty()) {
            return back()->with('error', 'Debes subir al menos un PDF antes de enviar.')->withInput();
        }

        // Guardar comentario y FECHA DE ENVÍO (justo al dar clic en "Enviar")
        $obligacion->resumen     = $data['resumen'] ?? null;
        $obligacion->fecha_envio = now();   // <- Aquí se marca la fecha y hora de envío
        $obligacion->save();

        // Crear lock para impedir nuevas cargas/eliminaciones
        $disk->put($dir . '/.lock', now()->toIso8601String());

        return back()->with('success', 'Enviado correctamente. A partir de ahora solo podrás descargar los archivos.');
    }

    /**
     * Procesar la acción (guardar, devolver al coordinador, devolver al usuario)
     */
    private function procesarAccion(Proyecto $proyecto, string $decision, string $action): string
    {
        switch ($action) {
            case 'guardar':
                return "Dictamen técnico guardado como '{$decision}'.";

            case 'devolver_coordinador':
                // Lógica para devolver al coordinador
                $proyecto->update([
                    'motivos_devolucion' => 'Revisión técnica completada',
                    'descripcion_devolucion' => 'Proyecto devuelto por el técnico al coordinador',
                ]);
                return "Proyecto devuelto al coordinador para revisión.";

            case 'devolver_usuario':
                // Lógica para devolver al usuario externo
                $proyecto->update([
                    'motivos_devolucion' => 'Se requieren correcciones o aclaraciones',
                    'descripcion_devolucion' => 'Proyecto devuelto al usuario externo para ajustes',
                ]);
                return "Proyecto devuelto al usuario externo.";

            default:
                return "Acción guardada.";
        }
    }

    public function listadoResumenGeneralDirector()
    {
        $obligaciones = \App\Models\Obligacion::query()
            ->leftJoin('proyecto as p', 'p.id_proyecto', '=', 'obligaciones.id_proyecto')
            ->leftJoin('actividad as a', 'a.id_actividad', '=', 'p.id_actividad')
            ->leftJoin('estado_proyecto as e', 'e.id_estado_proyecto', '=', 'p.id_estado_proyecto')
            ->select([
                'obligaciones.id_obligacion',
                'obligaciones.id_proyecto',
                'obligaciones.fecha_registro',
                'obligaciones.descripcion as tipo_obligacion',
                'obligaciones.periodo',
                'obligaciones.estado',
                'p.nombre as proyecto_nombre',
                'p.sector as proyecto_sector',
                'e.id_estado_proyecto',
                'e.descripcion as estado_proyecto',
                'a.descripcion_actividad as actividad_descripcion',
            ])
            ->orderByDesc('obligaciones.fecha_registro')
            ->paginate(15);

        return view('director.listadoGeneral', compact('obligaciones'));
    }

    public function listadoResumenAsignadoDirector()
    {
        $obligaciones = \App\Models\Obligacion::query()
            ->leftJoin('proyecto as p', 'p.id_proyecto', '=', 'obligaciones.id_proyecto')
            ->leftJoin('actividad as a', 'a.id_actividad', '=', 'p.id_actividad')
            ->leftJoin('estado_proyecto as e', 'e.id_estado_proyecto', '=', 'p.id_estado_proyecto')
            ->select([
                'obligaciones.id_obligacion',
                'obligaciones.id_proyecto',
                'obligaciones.fecha_registro',
                'obligaciones.descripcion as tipo_obligacion',
                'obligaciones.periodo',
                'obligaciones.estado',
                'p.nombre as proyecto_nombre',
                'p.sector as proyecto_sector',
                'e.id_estado_proyecto',
                'e.descripcion as estado_proyecto',
                'a.descripcion_actividad as actividad_descripcion',
            ])
            ->orderByDesc('obligaciones.fecha_registro')
            ->paginate(15);

        return view('director.listadoAsignado', compact('obligaciones'));
    }

    public function listadoResumenPendienteDirector()
    {
        $obligaciones = \App\Models\Obligacion::query()
            ->leftJoin('proyecto as p', 'p.id_proyecto', '=', 'obligaciones.id_proyecto')
            ->leftJoin('actividad as a', 'a.id_actividad', '=', 'p.id_actividad')
            ->leftJoin('estado_proyecto as e', 'e.id_estado_proyecto', '=', 'p.id_estado_proyecto')
            ->select([
                'obligaciones.id_obligacion',
                'obligaciones.id_proyecto',
                'obligaciones.fecha_registro',
                'obligaciones.descripcion as tipo_obligacion',
                'obligaciones.periodo',
                'obligaciones.estado',
                'p.nombre as proyecto_nombre',
                'p.sector as proyecto_sector',
                'e.id_estado_proyecto',
                'e.descripcion as estado_proyecto',
                'a.descripcion_actividad as actividad_descripcion',
            ])
            ->orderByDesc('obligaciones.fecha_registro')
            ->paginate(15);

        return view('director.listadoPendiente', compact('obligaciones'));
    }

    public function resumenTecnico(\App\Models\Obligacion $obligacion)
    {
        $proyecto = $obligacion->proyecto()
            ->with(['actividad:id_actividad,descripcion_actividad'])
            ->first();

        // NUEVO: Traer el técnico asignado desde la tabla asignaciones
        $tecnicoAsignado = DB::table('asignaciones as a')
            ->join('persona as p', 'p.id_persona', '=', 'a.id_asignado')
            ->where('a.id_tramite', $obligacion->id_proyecto)
            ->select(
                'p.id_persona',
                DB::raw("CONCAT(p.nombres, ' ', p.apellidos) as nombre_completo")
            )
            ->first();

        $dir  = "obligaciones/{$obligacion->id_obligacion}";
        $disk = Storage::disk('public');

        $files = collect($disk->exists($dir) ? $disk->files($dir) : [])
            ->reject(fn($p) => \Illuminate\Support\Str::startsWith(basename($p), '.'))
            ->map(function ($p) use ($disk) {
                return [
                    'path'       => $p,
                    'name'       => basename($p),
                    'tipo'       => 'PDF',
                    'created_at' => date('Y-m-d H:i', $disk->lastModified($p)),
                ];
            })
            ->values()
            ->all();

        return view('tecnico.resumen', compact('obligacion', 'proyecto', 'files', 'tecnicoAsignado'));
    }

    public function resumenDirector(\App\Models\Obligacion $obligacion)
    {
        $proyecto = $obligacion->proyecto()
            ->with(['actividad:id_actividad,descripcion_actividad'])
            ->first();

        $dir  = "obligaciones/{$obligacion->id_obligacion}";
        $disk = Storage::disk('public');

        $files = collect($disk->exists($dir) ? $disk->files($dir) : [])
            ->reject(fn($p) => \Illuminate\Support\Str::startsWith(basename($p), '.'))
            ->map(function ($p) use ($disk) {
                return [
                    'path'       => $p,
                    'name'       => basename($p),
                    'tipo'       => 'PDF',
                    'created_at' => date('Y-m-d H:i', $disk->lastModified($p)),
                ];
            })
            ->values()
            ->all();

        return view('director.resumen', compact('obligacion', 'proyecto', 'files'));
    }


    public function listadoAprobadosDirector()
    {
        // Tomamos como “aprobado por coordinador” aquellas obligaciones
        // cuyo campo obligaciones.estado = 'Pendiente evaluación del director'
        $query = DB::table('obligaciones as o')
            ->leftJoin('proyecto as p', 'p.id_proyecto', '=', 'o.id_proyecto')
            ->leftJoin('actividad as act', 'act.id_actividad', '=', 'p.id_actividad')
            ->leftJoin('estado_proyecto as e', 'e.id_estado_proyecto', '=', 'p.id_estado_proyecto')
            ->leftJoin('asignaciones as a', 'a.id_tramite', '=', 'p.id_proyecto')
            ->leftJoin('persona as asignador', 'asignador.id_persona', '=', 'a.id_asignador')
            ->leftJoin('persona as asignado',  'asignado.id_persona',  '=', 'a.id_asignado')
            ->select([
                'o.id_obligacion',
                'o.id_proyecto',
                'o.fecha_registro',
                'o.descripcion as tipo_obligacion',
                'o.estado as estado_obligacion',

                'p.nombre as proyecto_nombre',
                'p.sector as proyecto_sector',

                'act.descripcion_actividad as actividad_descripcion',
                'e.descripcion as estado_descripcion',

                DB::raw("CASE WHEN a.id_asignador IS NULL THEN 'NO ASIGNADO' ELSE CONCAT(asignador.nombres, ' ', asignador.apellidos) END as asignador_completo"),
                DB::raw("CASE WHEN a.id_asignado  IS NULL THEN 'NO ASIGNADO' ELSE CONCAT(asignado.nombres,  ' ', asignado.apellidos)  END as asignado_completo"),
            ])
            ->where('o.estado', 'Pendiente evaluación del director')
            ->orderByDesc('o.fecha_registro');

        $perPage = 15;
        $page    = request('page', 1);
        $total   = $query->count();
        $results = $query->forPage($page, $perPage)->get();

        $obligaciones = new \Illuminate\Pagination\LengthAwarePaginator(
            $results, $total, $perPage, $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('director.listadoAprobados', compact('obligaciones'));
    }





    public function listadoResumenGeneralCoordinador()
    {
        $query = DB::table('proyecto as p')
            ->leftJoin('asignaciones as a', 'p.id_proyecto', '=', 'a.id_tramite')
            ->leftJoin('obligaciones as o', 'o.id_proyecto', '=', 'p.id_proyecto')
            ->leftJoin('actividad as act', 'act.id_actividad', '=', 'p.id_actividad')
            ->leftJoin('estado_proyecto as e', 'e.id_estado_proyecto', '=', 'p.id_estado_proyecto')
            ->leftJoin('persona as asignador', 'asignador.id_persona', '=', 'a.id_asignador')
            ->leftJoin('persona as asignado', 'asignado.id_persona', '=', 'a.id_asignado')
            ->where(function ($q) {
                $q->where('p.id_estado_proyecto', 2)
                    ->orWhere('p.id_estado_proyecto', 3);
            })
            ->select([
                'p.id_proyecto',
                'p.nombre as proyecto_nombre',
                'p.sector as proyecto_sector',
                'e.descripcion as estado_proyecto',
                'act.descripcion_actividad as actividad_descripcion',
                DB::raw("CASE WHEN a.id_tramite IS NULL THEN 'NO ASIGNADO' ELSE 'ASIGNADO' END as estado_asignacion"),
                'o.id_obligacion',
                'o.descripcion as tipo_obligacion',
                DB::raw("CASE WHEN a.id_asignador IS NULL THEN 'NO ASIGNADO' ELSE CONCAT(asignador.nombres, ' ', asignador.apellidos) END as asignador_completo"),
                DB::raw("CASE WHEN a.id_asignado IS NULL THEN 'NO ASIGNADO' ELSE CONCAT(asignado.nombres, ' ', asignado.apellidos) END as asignado_completo")
            ])
            ->orderBy('estado_asignacion')
            ->orderBy('p.id_proyecto');

        // Paginación manual porque DB::table no tiene paginate() directo en todas las versiones antiguas
        $perPage = 15;
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;

        $results = $query->skip($offset)->take($perPage)->get();
        $total = $query->count(); // Esto puede ser ineficiente; mejor usar una subconsulta si hay muchos registros

        $obligaciones = new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        return view('coordinador.listadoGeneral', compact('obligaciones'));
    }

    public function listadoResumenAsignadoCoordinador()
    {
        $query = DB::table('asignaciones as a')
            ->leftJoin('proyecto as p', 'a.id_tramite', '=', 'p.id_proyecto')
            ->leftJoin('obligaciones as o', 'o.id_proyecto', '=', 'p.id_proyecto')
            ->leftJoin('actividad as act', 'act.id_actividad', '=', 'p.id_actividad')
            ->leftJoin('persona as asignador', 'asignador.id_persona', '=', 'a.id_asignador')
            ->leftJoin('persona as asignado', 'asignado.id_persona', '=', 'a.id_asignado')
            ->select([
                'o.id_obligacion',
                'o.id_proyecto',
                'o.fecha_registro',
                'o.descripcion as tipo_obligacion',
                'p.nombre as proyecto_nombre',
                'p.sector as proyecto_sector',
                'act.descripcion_actividad as actividad_descripcion',
                DB::raw("CONCAT(asignador.nombres, ' ', asignador.apellidos) as asignador_completo"),
                DB::raw("CONCAT(asignado.nombres, ' ', asignado.apellidos) as asignado_completo")
            ])
            ->whereNotNull('o.id_obligacion') // Evita filas sin obligación
            ->orderBy('o.fecha_registro', 'desc');

        // Paginación manual
        $perPage = 15;
        $page = request()->get('page', 1);
        $total = $query->count();
        $results = $query->forPage($page, $perPage)->get();

        $obligaciones = new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        return view('coordinador.listadoAsignado', compact('obligaciones'));
    }

    public function listadoAprobadosCoordinador()
    {
        $query = DB::table('proyecto as p')
            ->leftJoin('asignaciones as a', 'p.id_proyecto', '=', 'a.id_tramite')
            ->leftJoin('obligaciones as o', 'o.id_proyecto', '=', 'p.id_proyecto')
            ->leftJoin('actividad as act', 'act.id_actividad', '=', 'p.id_actividad')
            ->leftJoin('estado_proyecto as e', 'e.id_estado_proyecto', '=', 'p.id_estado_proyecto')
            ->leftJoin('persona as asignador', 'asignador.id_persona', '=', 'a.id_asignador')
            ->leftJoin('persona as asignado',  'asignado.id_persona',  '=', 'a.id_asignado')
            ->select([
                'o.id_obligacion',
                'o.id_proyecto',
                'o.fecha_registro',
                'o.descripcion as tipo_obligacion',
                'p.nombre as proyecto_nombre',
                'p.sector as proyecto_sector',
                'act.descripcion_actividad as actividad_descripcion',
                'e.descripcion as estado_descripcion',
                DB::raw("CASE WHEN a.id_asignador IS NULL THEN 'NO ASIGNADO' ELSE CONCAT(asignador.nombres, ' ', asignador.apellidos) END as asignador_completo"),
                DB::raw("CASE WHEN a.id_asignado  IS NULL THEN 'NO ASIGNADO' ELSE CONCAT(asignado.nombres,  ' ', asignado.apellidos)  END as asignado_completo"),
            ])
            // ✅ Mostrar SOLO lo pendiente para el coordinador
            ->where('o.estado', 'Pendiente evaluación del coordinador')
            // (opcional) además exigir que el proyecto siga “Aprobado” por el técnico
            ->where('p.id_estado_proyecto', 3)
            ->orderByDesc('o.fecha_registro');

        $perPage = 15;
        $page    = request('page', 1);
        $total   = $query->count();
        $results = $query->forPage($page, $perPage)->get();

        $obligaciones = new \Illuminate\Pagination\LengthAwarePaginator(
            $results, $total, $perPage, $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('coordinador.listadoAprobados', compact('obligaciones'));
    }




    public function listadoResumenPendienteCoordinador()
    {
        $obligaciones = DB::table('proyecto as p')
            ->leftJoin('asignaciones as a', 'p.id_proyecto', '=', 'a.id_tramite')
            ->leftJoin('obligaciones as o', 'o.id_proyecto', '=', 'p.id_proyecto')
            ->leftJoin('actividad as act', 'act.id_actividad', '=', 'p.id_actividad')
            ->leftJoin('estado_proyecto as e', 'e.id_estado_proyecto', '=', 'p.id_estado_proyecto')
            ->leftJoin('persona as asignador', 'asignador.id_persona', '=', 'a.id_asignador')
            ->leftJoin('persona as asignado', 'asignado.id_persona', '=', 'a.id_asignado')
            ->select([
                'p.id_proyecto',
                'p.nombre as proyecto_nombre',
                'p.sector as proyecto_sector',
                'e.id_estado_proyecto',
                'e.descripcion as estado_proyecto',
                'act.descripcion_actividad as actividad_descripcion',
                'o.id_obligacion',
                'o.fecha_registro',
                'o.descripcion as tipo_obligacion',
                'o.periodo',
                'o.estado',
                'a.id_asignacion',
                DB::raw("CONCAT(COALESCE(asignador.nombres, ''), ' ', COALESCE(asignador.apellidos, '')) as asignador_completo"),
                DB::raw("CONCAT(COALESCE(asignado.nombres, ''), ' ', COALESCE(asignado.apellidos, '')) as asignado_completo")
            ])
            ->whereNull('a.id_tramite') // No asignado
            ->whereIn('p.id_estado_proyecto', [2, 3])
            ->whereNotNull('o.id_obligacion') // Solo proyectos con obligación
            ->orderBy('o.fecha_registro', 'desc')
            ->paginate(15);

        // 2. Técnicos (perfil = 5)
        $tecnicos = DB::table('persona')
            ->select('id_persona', DB::raw("CONCAT(nombres, ' ', apellidos) as nombres"))
            ->where('id_perfil', 5)
            ->orderBy('nombres')
            ->get();

        return view('coordinador.listadoPendiente', compact('obligaciones', 'tecnicos'));
    }

    public function resumenCoordinador(\App\Models\Obligacion $obligacion)
    {
        $proyecto = $obligacion->proyecto()
            ->with(['actividad:id_actividad,descripcion_actividad'])
            ->first();

        $dir  = "obligaciones/{$obligacion->id_obligacion}";
        $disk = Storage::disk('public');

        $files = collect($disk->exists($dir) ? $disk->files($dir) : [])
            ->reject(fn($p) => \Illuminate\Support\Str::startsWith(basename($p), '.'))
            ->map(function ($p) use ($disk) {
                return [
                    'path'       => $p,
                    'name'       => basename($p),
                    'tipo'       => 'PDF',
                    'created_at' => date('Y-m-d H:i', $disk->lastModified($p)),
                ];
            })
            ->values()
            ->all();

        return view('coordinador.resumen', compact('obligacion', 'proyecto', 'files'));
    }

    public function asignarObligacion(Request $request)
    {
        $request->validate([
            'id_proyecto' => 'required|exists:proyecto,id_proyecto',
            'id_asignador' => 'required|exists:persona,id_persona',
            'id_asignado' => 'required|exists:persona,id_persona',
            'comentario' => 'nullable|string|max:500',
        ]);

        DB::table('asignaciones')->insert([
            'id_tramite' => $request->id_proyecto,
            'id_asignador' => $request->id_asignador,
            'id_asignado' => $request->id_asignado,
            'fecha_asignacion' => now(),
            'comentario' => $request->comentario,
        ]);

        return response()->json(['message' => 'Asignación creada exitosamente.']);
    }

    public function dictamenCoordinadorUpdate(Request $request, Obligacion $obligacion)
{
    // Relación a proyecto (requerido para estados y comentarios)
    $proyecto = $obligacion->proyecto;
    if (!$proyecto) {
        return back()->with('error', 'Proyecto no encontrado para esta obligación.');
    }

    // Validación (nuevo: usamos "decision" y "comentario_decision"; firma solo obligatoria si es aprobado)
    $data = $request->validate([
        'decision'          => ['required', 'in:aprobado,rechazado,observaciones'],
        'comentario_decision' => ['nullable', 'string', 'max:2000'],
        'firma_verificada'  => ['required', 'in:0,1'],
    ]);

    $decision  = $data['decision'];
    $comentario = $data['comentario_decision'] ?? null;
    $firmaOK   = $data['firma_verificada'] === '1';

    // Si APRUEBA → requiere firma
    if ($decision === 'aprobado' && !$firmaOK) {
        return back()->with('error', 'Para aprobar un proyecto se requiere firma electrónica válida.');
    }

    // Mapear decisiones a estados de proyecto (tu tabla estado_proyecto)
    // 1 Ingresado | 2 En Trámite | 3 Aprobado | 4 Rechazado | 5 Observaciones
    $estadoProyectoPorDecision = [
        'aprobado'      => 3, // Aprobado (se mantiene como "aprobado por técnico/coordinador" hasta que el director decida)
        'rechazado'     => 4,
        'observaciones' => 5,
    ];

    // Actualizar estado del PROYECTO:
    // - Si el coord. aprueba: dejamos 3 (Aprobado) como “listo para director”.
    // - Si rechaza / observaciones: 4 / 5 respectivamente.
    $proyecto->id_estado_proyecto = $estadoProyectoPorDecision[$decision];
    $proyecto->save();

    // Actualizar estado de la OBLIGACIÓN (texto claro para cada caso)
    $obligacion->estado = match ($decision) {
        'aprobado'      => 'Pendiente evaluación del director',
        'rechazado'     => 'Rechazado por coordinador',
        'observaciones' => 'Observaciones por coordinador',
    };
    $obligacion->save();

    // Insertar comentario para “inbox” del usuario (tu modelo Comentario ya existe)
    if (!empty($comentario)) {
        Comentario::create([
            'id_proyecto' => $proyecto->id_proyecto,
            'id_persona'  => auth()->user()->id_persona ?? auth()->user()->id, // autor: coordinador actual
            'descripcion' => "[Coordinador • {$decision}] " . $comentario,
            // campos de fecha/leído se completan según tu esquema (si tienes defaults)
        ]);
    } else {
        // Aun sin texto, dejamos rastro mínimo de la decisión
        Comentario::create([
            'id_proyecto' => $proyecto->id_proyecto,
            'id_persona'  => auth()->user()->id_persona ?? auth()->user()->id,
            'descripcion' => "[Coordinador • {$decision}]",
        ]);
    }

    // Mensaje final
    $msg = match ($decision) {
        'aprobado'      => 'Decisión registrada: aprobado. Se envió a pendiente de evaluación del director.',
        'rechazado'     => 'Decisión registrada: rechazado. El usuario ha sido notificado en su bandeja.',
        'observaciones' => 'Decisión registrada: observaciones. El usuario ha sido notificado en su bandeja.',
    };

    return back()->with('success', $msg);
}


    public function validarFirmaP12(Request $request, $obligacionId)
    {
        // (Opcional) Cargar el modelo por si quieres validar ownership/estado
        // $obligacion = Obligacion::findOrFail($obligacionId);

        // Requisitos previos
        if (!extension_loaded('openssl')) {
            return response()->json([
                'ok' => false,
                'message' => 'La extensión OpenSSL no está habilitada en el servidor.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $validator = Validator::make($request->all(), [
            'archivo_p12' => [
                'required',
                'file',
                'max:5120', // 5MB
                // Aceptar los MIME típicos de PKCS#12 y fallback genérico
                'mimetypes:application/x-pkcs12,application/pkcs12,application/octet-stream',
                // Verificar extensión por si el MIME viene genérico
                function ($attribute, $value, $fail) {
                    /** @var UploadedFile $value */
                    $ext = strtolower($value->getClientOriginalExtension());
                    if (!in_array($ext, ['p12', 'pfx'])) {
                        $fail('El archivo debe tener extensión .p12 o .pfx.');
                    }
                },
            ],
            'pass_p12' => ['required', 'string', 'max:255'],
        ], [
            'archivo_p12.required'   => 'Adjunta tu certificado (.p12/.pfx).',
            'archivo_p12.file'       => 'El archivo de certificado no es válido.',
            'archivo_p12.max'        => 'El certificado no debe superar 5 MB.',
            'archivo_p12.mimetypes'  => 'El archivo debe ser un certificado PKCS#12 (.p12 / .pfx).',
            'pass_p12.required'      => 'Ingresa la contraseña del certificado.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok'     => false,
                'message' => $validator->errors()->first(), // <- mensaje claro para el front
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        /** @var UploadedFile $file */
        $file = $request->file('archivo_p12');
        $password = $request->input('pass_p12');

        try {
            $contents = file_get_contents($file->getRealPath());
            if ($contents === false) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No fue posible leer el archivo .p12.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $certs = [];
            $ok = @openssl_pkcs12_read($contents, $certs, $password);


            $certs = [];
            // Vacía la cola de errores de OpenSSL antes de llamar (por si viene “sucia”)
            while (function_exists('openssl_error_string') && openssl_error_string()) {
            }

            $ok = @openssl_pkcs12_read($contents, $certs, $password);

            if (!$ok || empty($certs['cert']) || empty($certs['pkey'])) {
                // Lee lo que dice OpenSSL realmente (solo en dev)
                $osslErrors = [];
                while (function_exists('openssl_error_string') && ($e = openssl_error_string())) {
                    $osslErrors[] = $e;
                }

                // Mensaje humano según el error
                $humanMsg = 'Certificado o contraseña inválidos.';
                $flat = strtolower(implode(' | ', $osslErrors));
                if (str_contains($flat, 'mac verify failure')) {
                    $humanMsg = 'La contraseña del .p12 es incorrecta o el archivo está corrupto (MAC verify failure).';
                } elseif (str_contains($flat, 'unsupported') || str_contains($flat, 'unknown cipher') || str_contains($flat, 'legacy')) {
                    $humanMsg = 'Tu PKCS#12 usa algoritmos “legacy” (ej. 3DES/RC2 con SHA1). Activa el proveedor legacy de OpenSSL 3.';
                }

                return response()->json([
                    'ok'      => false,
                    'message' => $humanMsg,
                    'debug'   => app()->hasDebugModeEnabled() ? $osslErrors : null,
                ], \Symfony\Component\HttpFoundation\Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if (!$ok || empty($certs['cert']) || empty($certs['pkey'])) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Certificado o contraseña inválidos.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $parsed = @openssl_x509_parse($certs['cert']) ?: [];
            $validFrom = isset($parsed['validFrom_time_t']) ? date('Y-m-d H:i:s', $parsed['validFrom_time_t']) : null;
            $validTo   = isset($parsed['validTo_time_t'])   ? date('Y-m-d H:i:s', $parsed['validTo_time_t'])   : null;

            $now = time();
            $vigente = true;
            if (isset($parsed['validFrom_time_t'], $parsed['validTo_time_t'])) {
                $vigente = ($now >= $parsed['validFrom_time_t']) && ($now <= $parsed['validTo_time_t']);
            }

            return response()->json([
                'ok'         => true,
                'message'    => $vigente ? 'Certificado válido.' : 'Certificado fuera de vigencia.',
                'vigente'    => $vigente,
                'subject'    => $parsed['subject'] ?? null,
                'issuer'     => $parsed['issuer'] ?? null,
                'valid_from' => $validFrom,
                'valid_to'   => $validTo,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Ocurrió un error al validar el certificado.',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Registra la actividad de devolución del documento firmado al coordinador
     */
    private function registrarActividadDevolucion(Obligacion $obligacion, ?string $comentarios = null)
    {
        try {
            // Aquí puedes agregar lógica adicional para registrar la actividad
            // Por ejemplo, crear un registro en una tabla de actividades o historial

            // Opción 1: Usar el sistema de logs de Laravel
            Log::info('Devolución firmada al coordinador', [
                'obligacion_id' => $obligacion->id_obligacion,
                'proyecto_id' => $obligacion->id_proyecto ?? null,
                'tecnico_id' => (optional(Auth::user())->id_persona ?? optional(Auth::user())->id ?? null),
                'fecha' => now()->toDateTimeString(),
                'comentarios' => $comentarios,
                'estado_anterior' => $obligacion->getOriginal('estado'),
                'estado_nuevo' => 'Devuelto firmado al coordinador'
            ]);

            // Opción 2: Si tienes una tabla de actividades, puedes crear un registro aquí
            // Activity::create([
            //     'obligacion_id' => $obligacion->id_obligacion,
            //     'usuario_id' => auth()->id(),
            //     'tipo_actividad' => 'devolucion_firmada',
            //     'descripcion' => 'Documento firmado devuelto al coordinador',
            //     'comentarios' => $comentarios,
            //     'fecha_actividad' => now(),
            // ]);

        } catch (\Exception $e) {
            // No fallar la operación principal si hay error en el registro de actividad
            Log::warning('Error al registrar actividad de devolución', [
                'obligacion_id' => $obligacion->id_obligacion,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function dictamenDirectorUpdate(\Illuminate\Http\Request $request, \App\Models\Obligacion $obligacion)
    {
        $proyecto = $obligacion->proyecto; // relación obligación->proyecto

        if (!$proyecto) {
            return back()->with('error', 'Proyecto no encontrado.');
        }

        $data = $request->validate([
            'decision'          => ['required', 'in:aprobado,rechazado,observaciones'],
            'comentario_decision' => ['nullable', 'string', 'max:2000'],
            'firma_verificada'  => ['required', 'boolean'],
        ]);

        $decision      = $data['decision'];
        $comentario    = $data['comentario_decision'] ?? null;
        $firmaVerifica = (bool)$data['firma_verificada'];

        // Firma obligatoria solo para "aprobado"
        if ($decision === 'aprobado' && !$firmaVerifica) {
            return back()->with('error', 'Para aprobar un proyecto se requiere firma electrónica válida.');
        }

        // Map de estados del proyecto (ya definidos en tu tabla)
        $estadoProyectoMap = [
            'aprobado'      => 3, // Aprobado
            'rechazado'     => 4, // Rechazado
            'observaciones' => 5, // Observaciones
        ];

        // Actualiza estado del proyecto
        $proyecto->id_estado_proyecto = $estadoProyectoMap[$decision];
        $proyecto->save();

        // Estado de la obligación: algo corto y legible
        $obligacion->estado = match ($decision) {
            'aprobado'      => 'Aprobado (director)',
            'rechazado'     => 'Rechazado (director)',
            'observaciones' => 'Observaciones (director)',
        };
        $obligacion->save();

        // Comentario (para Inbox del usuario externo)
        if (!empty($comentario)) {
            \App\Models\Comentario::create([
                'id_proyecto' => $proyecto->id_proyecto,
                'id_persona'  => optional(\Auth::user())->id_persona ?? null,
                'descripcion' => "[Director: {$decision}] {$comentario}",
                // fecha_comentario se genera en BD si tienes default NOW()
            ]);
        } else {
            // Incluso sin comentario, puedes dejar constancia mínima
            \App\Models\Comentario::create([
                'id_proyecto' => $proyecto->id_proyecto,
                'id_persona'  => optional(\Auth::user())->id_persona ?? null,
                'descripcion' => "[Director: {$decision}]",
            ]);
        }

        // Listo
        return back()->with('success', 'Decisión del director registrada correctamente.');
    }
// MÉTODO TEMPORAL para migrar archivos del disco a la BD
public function migrarArchivosABD()
{
    $obligaciones = \App\Models\Obligacion::all();
    $migrados = 0;

    foreach ($obligaciones as $obligacion) {
        $dir = "obligaciones/{$obligacion->id_obligacion}";
        
        if (!Storage::disk('public')->exists($dir)) {
            continue;
        }

        $archivos = collect(Storage::disk('public')->files($dir))
            ->filter(fn($p) => str_ends_with(strtolower($p), '.pdf'));

        foreach ($archivos as $path) {
            $nombreArchivo = basename($path);

            // Verificar si ya existe en BD
            $existe = \App\Models\Archivo::where('id_obligacion', $obligacion->id_obligacion)
                ->where('nombre_archivo', $nombreArchivo)
                ->exists();

            if ($existe) {
                continue; // Ya está en BD
            }

            // Crear registro en BD
            try {
                \App\Models\Archivo::create([
                    'id_proyecto'    => $obligacion->id_proyecto,
                    'id_obligacion'  => $obligacion->id_obligacion,
                    'nombre_archivo' => $nombreArchivo,
                    'url'            => Storage::url($path),
                    'tipo'           => 'PDF',
                    'fecha_creacion' => now(),
                    'fecha_archivo'  => now(),
                ]);
                $migrados++;
            } catch (\Exception $e) {
                Log::error("Error migrando archivo {$nombreArchivo}: " . $e->getMessage());
            }
        }
    }

    return response()->json([
        'success' => true,
        'mensaje' => "Se migraron {$migrados} archivos a la base de datos",
        'migrados' => $migrados
    ]);
    }
}