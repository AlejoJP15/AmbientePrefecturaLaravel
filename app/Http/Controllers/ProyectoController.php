<?php

namespace App\Http\Controllers;

use App\Models\Obligacion;
use App\Models\Proyecto;
use App\Models\Provincia;
use App\Models\Canton;
use App\Models\Parroquia;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Coordenada;
use Illuminate\Support\Facades\Auth;
use App\Models\Actividad;
use App\Models\Comentario;


class ProyectoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id_persona ?? $user->id;

        $proyectos = \App\Models\Proyecto::select(
            'id_proyecto',
            'nombre',
            'sector',
            'fecha_creacion',
            'id_usuario',
            'id_actividad'  // ← ¡ESTO ES OBLIGATORIO!
        )
            ->with(['actividad:id_actividad,descripcion_actividad'])
            ->where('id_usuario', $userId)
            ->orderByDesc('id_proyecto')
            ->get();

        return view('usuario.proyectos.index', compact('proyectos'));
    }

    public function indexTodos()
    {
        $proyectos = \App\Models\Proyecto::query()
            ->with(['actividad:id_actividad,descripcion_actividad'])
            ->orderByDesc('id_proyecto')
            // ->paginate(15) // si quieres paginar, la vista ya soporta links()
            ->get();

        // Usa la vista “clon” que hiciste para admin (idéntica de estilos)
        return view('administrador.proyectos.index', compact('proyectos'));
    }

    public function store(Request $request)
    {

        $persona = Auth::user();
        $currentPersonaId = $persona->id_persona ?? $persona->id ?? null;

        if (!$currentPersonaId) {
            return back()
                ->withErrors(['auth' => 'No se pudo obtener el id_persona del usuario autenticado.'])
                ->withInput();
        }


        // TODO: Ajusta tu validación original aquí
        $validated = $request->validate([
            'id_actividad' => 'required|integer|exists:actividad,id_actividad',
            'nombre'           => ['required', 'string'],
            'tipo_estudio'     => ['required', 'string'],
            'codigo_suia'      => ['nullable', 'string'],
            'id_provincia'     => ['nullable', 'integer'],
            'id_canton'        => ['nullable', 'integer'],
            'id_parroquia'     => ['nullable', 'integer'],
            'direccion'        => ['nullable', 'string'],
            'sector'           => ['nullable', 'string'],
            'coordenadas_json' => ['required', 'string'],
            'resumen'      => 'nullable|string',
            'tipo_permiso' => 'required|string',
        ]);

        $coords = json_decode($validated['coordenadas_json'] ?? '[]', true);
        if (!is_array($coords) || count($coords) !== 5) {
            return back()
                ->withErrors(['coordenadas_json' => 'Debes seleccionar exactamente 5 puntos en el mapa.'])
                ->withInput();
        }

        $payload = [
            'id_actividad'   => (int) $validated['id_actividad'],
            'nombre'         => $validated['nombre'],
            'codigo_suia'    => $validated['codigo_suia'] ?? null,
            'tipo_estudio'   => $validated['tipo_estudio'],
            'id_provincia'   => $validated['id_provincia'] ?? null,
            'id_canton'      => $validated['id_canton'] ?? null,
            'id_parroquia'   => $validated['id_parroquia'] ?? null,
            'direccion'      => $validated['direccion'] ?? null,
            'sector'         => $validated['sector'] ?? null,
            'fecha_creacion' => \Illuminate\Support\Carbon::now(),
            'resumen'        => $validated['resumen'] ?? null,
            'tipo_permiso'   => $validated['tipo_permiso'],
            'tipo_estudio'   => $validated['tipo_estudio'],
            'id_estado_proyecto' => 1, // ← VALOR 1 AGREGADO,
            'id_usuario'     => $currentPersonaId,
        ];

        $proyecto = null;

        DB::transaction(function () use (&$proyecto, $payload, $coords) {
            // 1) Proyecto
            $proyecto = \App\Models\Proyecto::create($payload);

            // 2) Coordenadas (5 filas)
            foreach ($coords as $i => $pt) {
                $lat = (float) ($pt['lat'] ?? null);
                $lng = (float) ($pt['lng'] ?? null);

                if (!is_finite($lat) || !is_finite($lng)) {
                    throw new \RuntimeException('Coordenada inválida en el índice ' . $i);
                }

                \Illuminate\Support\Facades\DB::table('coordenadas')->insert([
                    'id_proyecto'   => $proyecto->id_proyecto,
                    'tipo'          => 'P' . ($i + 1),
                    'coordenada_x'  => $lng, // X = LONG
                    'coordenada_y'  => $lat, // Y = LAT
                    'geom'          => \Illuminate\Support\Facades\DB::raw("ST_SetSRID(ST_MakePoint($lng, $lat), 4326)"),
                    'coordenada_x1' => $lng,
                    'coordenada_y1' => $lat,
                ]);
            }
        });

        // Redirección / vista final (deja tu lógica tal como la tenías)
        return redirect()
            ->route('usuario.proyectos.show', $proyecto->id_proyecto)
            ->with('ok', 'Proyecto y coordenadas guardados correctamente.');
    }


    // ===== API ubicación con MODELOS + route model binding =====

    public function apiProvincias()
    {
        return Provincia::select('id_provincia', 'provincia')
            ->orderBy('provincia')
            ->get();
    }

    // /api/cantones/{provincia}
    public function apiCantones(Provincia $provincia)
    {
        return Canton::where('id_provincia', $provincia->id_provincia)
            ->select('id_canton', 'canton')
            ->orderBy('canton')
            ->get();
    }

    // /api/parroquias/{canton}
    public function apiParroquias(Canton $canton)
    {
        return Parroquia::where('id_canton', $canton->id_canton)
            ->select('id_parroquia', 'parroquia')
            ->orderBy('parroquia')
            ->get();
    }

    public function show($id)
    {
        // 1) Usuario autenticado (Persona)
        $persona = auth()->user();
        $currentPersonaId = $persona->id_persona ?? $persona->id ?? null;
        if (!$currentPersonaId) {
            abort(401); // no autenticado
        }

        // Detectar si es ADMIN (Spatie o perfil.descripcion)
        $isAdmin = false;
        if (method_exists($persona, 'hasRole')) {
            $isAdmin = $persona->hasRole('Administrador') || $persona->hasRole('Admin');
        } else {
            $rol = (string) optional($persona->perfil)->descripcion;
            $isAdmin = strcasecmp(trim($rol), 'Administrador') === 0 || strcasecmp(trim($rol), 'Admin') === 0;
        }

        // 2) Traer el proyecto (si NO es admin, restringir por dueño)
        $query = Proyecto::query()
            ->whereKey($id)
            ->select('proyecto.*')
            ->with([
                'provincia:id_provincia,provincia',
                'canton'     => fn($q) => $q->select('id_canton', 'nombre_canton as canton'),
                'parroquia'  => fn($q) => $q->select('id_parroquia', 'nombre_parroquia as parroquia'),
                'actividad:id_actividad,codigo_actividad,descripcion_actividad',
            ]);

        if (!$isAdmin) {
            $query->where('id_usuario', $currentPersonaId);
        }

        $proyecto = $query->firstOrFail();

        // 3) Limpieza visual de nombres de provincia/cantón/parroquia
        $clean = function (?string $raw): ?string {
            if ($raw === null) return null;
            $s = trim($raw);
            if (preg_match('/^\(\s*\d+\s*,\s*("?)(.+?)\1(?:\s*,\s*\d+)?\s*\)$/u', $s, $m)) {
                $s = $m[2];
            }
            return trim($s, " \t\n\r\0\x0B\"'“”‘’«»‹›„‚");
        };
        if ($proyecto->provincia) $proyecto->provincia->provincia = $clean($proyecto->provincia->provincia);
        if ($proyecto->canton)    $proyecto->canton->canton       = $clean($proyecto->canton->canton);
        if ($proyecto->parroquia) $proyecto->parroquia->parroquia = $clean($proyecto->parroquia->parroquia);

        // 4) Coordenadas para el mapa
        $puntos = DB::table('coordenadas')
            ->selectRaw('coordenada_y1 AS lat, coordenada_x1 AS lng, tipo')
            ->where('id_proyecto', $proyecto->id_proyecto)
            ->whereRaw("tipo ~ '^P[0-9]+$'")
            ->orderByRaw("COALESCE(NULLIF(regexp_replace(tipo, '\\D', '', 'g'), '')::int, 0)")
            ->get()
            ->map(fn($r) => ['lat' => (float) $r->lat, 'lng' => (float) $r->lng, 'label' => $r->tipo])
            ->values();

        $polyGeojson = DB::table('coordenadas')
            ->where('id_proyecto', $proyecto->id_proyecto)
            ->where('tipo', 'POLIGONO')
            ->selectRaw('ST_AsGeoJSON(geom) AS gj')
            ->value('gj');

        // 5) Obligaciones del proyecto
        $obligaciones = Obligacion::where('id_proyecto', $proyecto->id_proyecto)
            ->orderByDesc('id_obligacion')
            ->get();

        // 6) **Comentarios** (observaciones) y marcar como leídos
        $comentarios = Comentario::where('id_proyecto', $proyecto->id_proyecto)
            ->orderByDesc('fecha_comentario')
            ->get();

        Comentario::where('id_proyecto', $proyecto->id_proyecto)
            ->where('leido', false)
            ->update(['leido' => true, 'fecha_leido' => now()]);

        // 7) Render
        return view('usuario.proyectos.show', compact(
            'proyecto', 'puntos', 'polyGeojson', 'obligaciones', 'comentarios'
        ));
    }


    public function indexProyectosMapa()
    {
        // Obtener el ID del técnico logueado (asignado)
        $idTecnico = Auth::user()->id_persona;

        $proyectos = DB::table('proyecto')
            // Unir con asignaciones: solo proyectos asignados a este técnico
            ->join('asignaciones as asig', 'asig.id_tramite', '=', 'proyecto.id_proyecto')
            // Persona (dueño del proyecto)
            ->leftJoin('persona as pe', 'pe.id_persona', '=', 'proyecto.id_usuario')
            // Usuario externo (para persona jurídica)
            ->leftJoin('usuario_externo as ue', 'ue.id_persona', '=', 'pe.id_persona')
            // Catálogo de cantón
            ->leftJoin('canton as c', 'c.id_canton', '=', 'proyecto.id_canton')
            // Catálogo de actividad
            ->leftJoin('actividad as a', 'a.id_actividad', '=', 'proyecto.id_actividad')
            // Filtrar solo asignaciones donde el técnico es el asignado
            ->where('asig.id_asignado', $idTecnico)
            ->select([
                'proyecto.id_proyecto',
                'proyecto.nombre',
                // Actividad
                DB::raw("COALESCE(a.descripcion_actividad, proyecto.id_actividad::text) AS actividad"),
                // Cantón
                DB::raw("COALESCE(c.nombre_canton, 'ID: ' || proyecto.id_canton::text) AS canton"),
                // Proponente
                DB::raw("
                TRIM(
                    COALESCE(
                        NULLIF(TRIM(ue.nombre_representante), ''),
                        NULLIF(TRIM(ue.organizacion), ''),
                        NULLIF(TRIM((COALESCE(pe.nombres, '') || ' ' || COALESCE(pe.apellidos, ''))), ''),
                        '—'
                    )
                ) AS proponente
            "),
            ])
            ->orderByDesc('proyecto.id_proyecto')
            ->paginate(20);

        return view('tecnico.mapa.index', compact('proyectos'));
    }


    public function verMapa($id)
    {
        // Trae el proyecto (no hace falta cargar relaciones aquí)
        $proyecto = Proyecto::with(['provincia', 'canton', 'parroquia'])->findOrFail($id);

        // === MISMA consulta que en show(): P1..P5 con coordenada_y1 (LAT) y coordenada_x1 (LNG) ===
        $puntos = DB::table('coordenadas')
            ->selectRaw('coordenada_y1 AS lat, coordenada_x1 AS lng, tipo')
            ->where('id_proyecto', $proyecto->id_proyecto)
            ->whereRaw("tipo ~ '^P[0-9]+$'")
            ->orderByRaw("COALESCE(NULLIF(regexp_replace(tipo, '\\D', '', 'g'), '')::int, 0)")
            ->get()
            ->map(fn($r) => [
                'lat'   => (float) $r->lat,
                'lng'   => (float) $r->lng,
                'label' => $r->tipo,
            ])
            ->values();

        // Polígono guardado (si existe)
        $polyGeojson = DB::table('coordenadas')
            ->where('id_proyecto', $proyecto->id_proyecto)
            ->where('tipo', 'POLIGONO')
            ->selectRaw('ST_AsGeoJSON(geom) AS gj')
            ->value('gj');

        return view('tecnico.mapa.mapa', compact('proyecto', 'puntos', 'polyGeojson'));
    }

    public function editAdmin($id)
    {
        $proyecto = Proyecto::with(['provincia', 'canton', 'parroquia', 'actividad'])->findOrFail($id);

        // Lista estática de actividades (sin subdivisiones)
        $actividades = Actividad::orderBy('descripcion_actividad')
            ->get(['id_actividad', 'descripcion_actividad']);

        // Puntos existentes (misma lógica que show/create)
        $puntos = DB::table('coordenadas')
            ->selectRaw('coordenada_y1 AS lat, coordenada_x1 AS lng, tipo')
            ->where('id_proyecto', $proyecto->id_proyecto)
            ->whereRaw("tipo ~ '^P[0-9]+$'")
            ->orderByRaw("COALESCE(NULLIF(regexp_replace(tipo, '\\D', '', 'g'), '')::int, 0)")
            ->get()
            ->map(fn($r) => ['lat' => (float)$r->lat, 'lng' => (float)$r->lng, 'label' => $r->tipo])
            ->values();

        return view('actualizar_proyecto.edit', compact('proyecto', 'actividades', 'puntos'));
    }

    public function updateAdmin(Request $request, $id)
    {
        $proyecto = Proyecto::findOrFail($id);

        $data = $request->validate([
            'nombre'        => 'required|string|max:255',
            'resumen'       => 'nullable|string',
            'direccion'     => 'nullable|string|max:255',
            'sector'        => 'nullable|string|max:255',                 // ← nuevo
            'codigo_suia'   => 'nullable|string|max:100',                 // ← nuevo
            'tipo_permiso'  => 'required|string|in:Ficha Ambiental,Certificado Ambiental,Registro Ambiental,Licencia Ambiental', // ← nuevo
            'tipo_estudio'  => 'required|string|in:Antes del proyecto (Ex-ante),Después del proyecto (Ex-post)',                 // ← nuevo
            'id_actividad'  => 'required|integer|exists:actividad,id_actividad',
            'id_provincia'  => 'nullable|integer',
            'id_canton'     => 'nullable|integer',
            'id_parroquia'  => 'nullable|integer',
            'coordenadas_json' => 'nullable|string',
        ]);

        DB::transaction(function () use ($proyecto, $data) {
            $proyecto->fill([
                'nombre'        => $data['nombre'],
                'resumen'       => $data['resumen']      ?? $proyecto->resumen,
                'direccion'     => $data['direccion']    ?? $proyecto->direccion,
                'sector'        => $data['sector']       ?? $proyecto->sector,         // ← nuevo
                'codigo_suia'   => $data['codigo_suia']  ?? $proyecto->codigo_suia,    // ← nuevo
                'tipo_permiso'  => $data['tipo_permiso'] ?? $proyecto->tipo_permiso,   // ← nuevo
                'tipo_estudio'  => $data['tipo_estudio'] ?? $proyecto->tipo_estudio,   // ← nuevo
                'id_actividad'  => $data['id_actividad'],
                'id_provincia'  => $data['id_provincia'] ?? $proyecto->id_provincia,
                'id_canton'     => $data['id_canton']    ?? $proyecto->id_canton,
                'id_parroquia'  => $data['id_parroquia'] ?? $proyecto->id_parroquia,
            ])->save();

            // 2) Coordenadas (solo si el user envió 5 puntos)
            $nuevos = null;
            if (!empty($data['coordenadas_json'])) {
                try {
                    $nuevos = json_decode($data['coordenadas_json'], true, 512, JSON_THROW_ON_ERROR);
                } catch (\Throwable $e) {
                    $nuevos = null;
                }
            }

            if (is_array($nuevos) && count($nuevos) === 5) {
                // borrar P1..P5 y POLIGONO previos
                DB::table('coordenadas')
                    ->where('id_proyecto', $proyecto->id_proyecto)
                    ->where(function ($q) {
                        $q->whereRaw("tipo ~ '^P[0-9]+$'")->orWhere('tipo', 'POLIGONO');
                    })
                    ->delete();

                // insertar P1..P5 nuevos (en orden)
                foreach (array_values($nuevos) as $i => $pt) {
                    if (!isset($pt['lat'], $pt['lng'])) continue;
                    $lat = (float)$pt['lat'];
                    $lng = (float)$pt['lng'];
                    DB::table('coordenadas')->insert([
                        'id_proyecto'   => $proyecto->id_proyecto,
                        'tipo'          => 'P' . ($i + 1),
                        'coordenada_y1' => $lat, // LAT
                        'coordenada_x1' => $lng, // LNG
                    ]);
                }
                // (Opcional) Guardar polígono a partir de puntos si quieres:
                // $wkt = 'POLYGON((' . implode(',', array_map(fn($p)=>$p['lng'].' '.$p['lat'], array_merge($nuevos, [$nuevos[0]]))) . '))';
                // DB::table('coordenadas')->insert([
                //   'id_proyecto'=>$proyecto->id_proyecto,'tipo'=>'POLIGONO','geom'=>DB::raw("ST_SetSRID(ST_GeomFromText('{$wkt}'),4326)"),'created_at'=>now(),'updated_at'=>now(),
                // ]);
            }
            // Si no llegaron 5 puntos, se mantienen los existentes (no se toca coordenadas)
        });

        return redirect()->route('admin.proyectos.edit', $proyecto->id_proyecto)
            ->with('success', 'Proyecto actualizado correctamente.');
    }


    public function adminIndex(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        $perPage = (int)$request->get('per_page', 10);
        if (!in_array($perPage, [10, 20, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $proyectos = Proyecto::query()
            ->leftJoin('actividad as a', 'a.id_actividad', '=', 'proyecto.id_actividad')
            ->select([
                'proyecto.id_proyecto',
                'proyecto.nombre',
                'proyecto.fecha_creacion',
                DB::raw("a.descripcion_actividad as actividad"),
            ])
            ->when($q !== '', function ($query) use ($q) {
                // Búsqueda por nombre, actividad y número del código (ej. 'HGAD-PCH-PRO-123' o '123')
                return $query->where(function ($w) use ($q) {
                    $w->where('proyecto.nombre', 'ilike', '%' . $q . '%')
                        ->orWhere('a.descripcion_actividad', 'ilike', '%' . $q . '%');
                    if (is_numeric($q)) {
                        $w->orWhere('proyecto.id_proyecto', (int)$q);
                    }
                    // también detectar si viene "HGAD-PCH-PRO-123"
                    if (preg_match('/(\d+)/', $q, $m)) {
                        $w->orWhere('proyecto.id_proyecto', (int)$m[1]);
                    }
                });
            })
            ->orderByDesc('proyecto.id_proyecto')
            ->paginate($perPage)
            ->withQueryString();

        return view('actualizar_proyecto.index', compact('proyectos', 'q', 'perPage'));
    }

    public function asignarCoordinador(Request $request, $idProyecto)
    {
        try {
            DB::beginTransaction();

            // Buscar el proyecto
            $proyecto = Proyecto::findOrFail($idProyecto);

            // Actualizar el estado del proyecto a 2 (Asignado a Coordinador)
            $proyecto->update([
                'id_estado_proyecto' => 2
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Proyecto asignado correctamente al coordinador',
                'proyecto' => $proyecto
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al asignar el proyecto: ' . $e->getMessage()
            ], 500);
        }
    }


    public function marcarComentariosLeidos($id)
    {
        $user = auth()->user();
        $currentId = $user->id_persona ?? $user->id ?? null;
        if (!$currentId) abort(401);

        // ¿Admin?
        $isAdmin = method_exists($user, 'hasRole')
            ? ($user->hasRole('Administrador') || $user->hasRole('Admin'))
            : (strcasecmp(trim(optional($user->perfil)->descripcion), 'Administrador') === 0
            || strcasecmp(trim(optional($user->perfil)->descripcion), 'Admin') === 0);

        // El dueño (o admin) puede marcar
        $q = \App\Models\Proyecto::where('id_proyecto', $id);
        if (!$isAdmin) $q->where('id_usuario', $currentId);
        $proyecto = $q->firstOrFail();

        $n = \App\Models\Comentario::where('id_proyecto', $proyecto->id_proyecto)
            ->where('leido', false)
            ->update(['leido' => true, 'fecha_leido' => now()]);

        return response()->json(['ok' => true, 'updated' => $n]);
    }

}
