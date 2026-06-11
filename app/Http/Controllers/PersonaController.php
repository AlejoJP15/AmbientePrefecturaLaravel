<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Provincia;
use App\Models\Canton;
use App\Models\Parroquia;
use App\Models\Perfil;
use App\Models\EstadoPersona;
use App\Models\UsuarioExterno;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ValidacionCedula;
use App\Helpers\ValidacionRuc;
use Illuminate\Validation\Rules\Password;

class PersonaController extends Controller
{
    // Listar todas las personas
    public function index()
    {
        $personas = Persona::with(['perfil', 'estado', 'provincia', 'canton', 'parroquia', 'usuarioExterno'])->get();
        return view('persona.index', compact('personas'));
    }


    public function create()
    {
        $provincia = Provincia::orderBy('nombre_provincia')->get();
        $canton = Canton::all();
        $parroquia = Parroquia::all();
        $perfiles = Perfil::all();
        $estado = EstadoPersona::all();

        return view('persona.create', compact(
            'provincia', 'canton', 'parroquia', 'perfiles', 'estado'
        ));
    }

    public function editAdmin(string $id)
{
    $persona = \App\Models\Persona::findOrFail($id);

    $externo = \App\Models\UsuarioExterno::firstWhere('id_persona', $persona->id_persona);

    $provincias = \App\Models\Provincia::all();
    $cantones   = \App\Models\Canton::all();
    $parroquias = \App\Models\Parroquia::all();
    $perfiles   = \App\Models\Perfil::all();
    $estados    = \App\Models\EstadoPersona::all();

    return view('persona.edit_admin', compact(
        'persona','externo','provincias','cantones','parroquias','perfiles','estados'
    ));
}

public function store(Request $request)
{
    // Reglas generales
    $rules = [
        'tipo_persona' => 'required|in:natural,juridica',
        'contraseña' => [
            'required',
            'string',
            Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised()
        ],
        'email' => 'required|email|max:100|unique:persona,email',
        'usuario' => 'required|string|max:13|unique:persona,usuario',
        'correo_alternativo' => 'nullable|email|max:100'
    ];

    // Validación condicional dependiendo del tipo de persona
    if ($request->tipo_persona === 'natural') {
        $rules = array_merge($rules, [
            'tipo_documento' => 'required|in:cedula,ruc',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'genero' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'nivel_instruccion' => 'nullable|string|max:50',
            'consentimiento' => 'nullable|string|max:10',
            'id_provincia' => 'nullable|exists:provincia,id_provincia',
            'id_canton' => 'nullable|exists:canton,id_canton',
            'id_parroquia' => 'nullable|exists:parroquia,id_parroquia',
        ]);
    } elseif ($request->tipo_persona === 'juridica') {
        $rules = array_merge($rules, [
            'organizacion' => 'required|string|max:255',
            'comercial' => 'nullable|string|max:255',
            'representante' => 'required|string|max:13',
            'nombre_representante' => 'required|string|max:255',
            'cargo_representante' => 'nullable|string|max:255',
            'sector' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'id_provincia' => 'nullable|exists:provincia,id_provincia',
            'id_canton' => 'nullable|exists:canton,id_canton',
            'id_parroquia' => 'nullable|exists:parroquia,id_parroquia',
        ]);
    }

    // Validar los campos
    $validator = Validator::make($request->all(), $rules);

    // Agrega validaciones personalizadas para cédula o RUC después de las reglas estándar
    $validator->after(function ($validator) use ($request) {
        $tipoPersona = $request->input('tipo_persona');
        $tipoDocumento = $request->input('tipo_documento');
        $usuario = $request->input('usuario');
        $representante = $request->input('representante');

        // Validar usuario (RUC o cédula)
        if ($tipoPersona === 'natural' && $tipoDocumento === 'cedula' && !ValidacionCedula::validar($usuario)) {
            $validator->errors()->add('usuario', 'La cédula ingresada no es válida.');
        }

        if ($tipoPersona === 'natural' && $tipoDocumento === 'ruc' && !ValidacionRuc::validar($usuario)) {
            $validator->errors()->add('usuario', 'El RUC ingresado no es válido.');
        }

        if ($tipoPersona === 'juridica') {
            // Validar RUC de empresa
            if (!ValidacionRuc::validar($usuario)) {
                $validator->errors()->add('usuario', 'El RUC de la empresa no es válido.');
            }

            // Validar RUC o cédula del representante
            if (strlen($representante) === 10) {
                if (!ValidacionCedula::validar($representante)) {
                    $validator->errors()->add('representante', 'La cédula del representante no es válida.');
                }
            } elseif (strlen($representante) === 13) {
                if (!ValidacionRuc::validar($representante)) {
                    $validator->errors()->add('representante', 'El RUC del representante no es válido.');
                }
            } else {
                $validator->errors()->add('representante', 'El documento del representante debe tener 10 o 13 dígitos.');
            }
        }
    });


    // Si hay errores en la validación, devolver con errores
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Guardar datos en la tabla 'persona'
    $data = $validator->validated();
    $data['fecha_creacion'] = now();
    $data['tipo_persona'] = $request->tipo_persona;
    $data['tipo_documento'] = $request->tipo_documento;
    $data['id_estado_persona'] = 3;
    $data['id_perfil'] = 6;
    $persona = Persona::create($data);

    // Si es jurídica, guardar los datos en 'usuario_externo'
    if ($request->tipo_persona === 'juridica') {
        \App\Models\UsuarioExterno::create([
            'organizacion' => $data['organizacion'],
            'comercial' => $data['comercial'],
            'representante' => $data['representante'],
            'nombre_representante' => $data['nombre_representante'],
            'cargo_representante' => $data['cargo_representante'],
            'sector' => $data['sector'],
            'estado' => 'activo',
            'id_persona' => $persona->id_persona
        ]);
    }

    return redirect()->back()->with('success', 'Registro exitoso');
}


    // Mostrar una persona específica
    public function show($id)
    {
        $persona = Persona::with(['perfil', 'estado', 'provincia', 'canton', 'parroquia'])->find($id);

        if (!$persona) {
            return redirect()->back()->with('error', 'Persona no encontrada');
        }

        return response()->json($persona);
    }


    public function edit(string $id)
    {
        //
    }

    // Actualizar una persona
    public function update(Request $request, string $id)
    {
        $persona = Persona::find($id);

        if (!$persona) {
            return redirect()->back()->with('error', 'Persona no encontrada');
        }

        $validator = Validator::make($request->all(), [
            'usuario' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('persona')->ignore($persona->id_persona, 'id_persona')
            ],
            'nombres' => 'sometimes|string|max:100',
            'apellidos' => 'sometimes|string|max:100',
            'contraseña' => 'sometimes|string|min:8',
            'email' => [
                'sometimes',
                'email',
                'max:100',
                Rule::unique('persona')->ignore($persona->id_persona, 'id_persona')
            ],
            'id_perfil' => 'sometimes|exists:perfil,id_perfil',
            'id_estado_persona' => 'sometimes|exists:estado_persona,id_estado_persona',
            'cedula' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('persona')->ignore($persona->id_persona, 'id_persona')
            ],
            'genero' => 'nullable|string|max:1',
            'telefono' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'id_provincia' => 'nullable|exists:provincia,id_provincia',
            'id_canton' => 'nullable|exists:canton,id_canton',
            'id_parroquia' => 'nullable|exists:parroquia,id_parroquia',
            'correo_alternativo' => 'nullable|email|max:100',
            'nivel_instruccion' => 'nullable|string|max:50',
            'consentimiento' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator) // esto carga los mensajes en $errors
            ->withInput(); // para que se mantengan los campos llenos
        }

        $persona->update($validator->validated());

        return redirect()->back()->with('success', 'Persona actualizada correctamente');
    }

    public function updatePerfil(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();

        
        $tipoRaw = trim((string) ($user->tipo_persona ?? ''));
        $sinAcentos = strtr($tipoRaw, [
            'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U',
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u',
        ]);
        $tipoNorm   = mb_strtolower($sinAcentos, 'UTF-8');
        $esJuridica = (strpos($tipoNorm, 'juridic') !== false);

        /* -------- 1) PERSONA -------- */
        $dataPersona = $request->only([
            'nombres','apellidos','email','correo_alternativo','telefono','celular',
            'direccion','nivel_instruccion','id_provincia','genero'
        ]);

        foreach ($dataPersona as $k => $v) {
            if (is_string($v)) $dataPersona[$k] = trim($v);
            if ($dataPersona[$k] === '') unset($dataPersona[$k]);
        }

        // UPPER a texto (no emails/num)
        foreach (['nombres','apellidos','direccion','nivel_instruccion','genero'] as $k) {
            if (isset($dataPersona[$k]) && is_string($dataPersona[$k])) {
                $dataPersona[$k] = mb_strtoupper($dataPersona[$k], 'UTF-8');
            }
        }

        $rulesPersona = [
            'nombres'            => 'sometimes|string|max:100',
            'apellidos'          => 'sometimes|string|max:100',
            'email'              => 'sometimes|email|max:50|unique:persona,email,' . $user->id_persona . ',id_persona',
            'correo_alternativo' => 'sometimes|email|max:50',
            'telefono'           => 'sometimes|string|max:10',
            'celular'            => 'sometimes|string|max:10',
            'direccion'          => 'sometimes|string|max:200',
            'nivel_instruccion'  => 'sometimes|in:PRIMARIA,SECUNDARIA,UNIVERSITARIA,POSGRADO',
            'genero'             => 'sometimes|in:MASCULINO,FEMENINO,OTRO',
            'id_provincia'       => 'sometimes|nullable|exists:provincia,id_provincia',
        ];
        $validatedPersona = \Validator::make($dataPersona, $rulesPersona)->validate();

        /* -------- 2) USUARIO_EXTERNO (solo si es jurídica) -------- */
        $externo = null;
        if ($esJuridica) {
            $dataExterno = $request->only([
                'organizacion','comercial','representante',
                'nombre_representante','cargo_representante','sector'
            ]);

            foreach ($dataExterno as $k => $v) {
                if (is_string($v)) $dataExterno[$k] = trim($v);
                if (($dataExterno[$k] ?? null) === '') unset($dataExterno[$k]);
            }

            // UPPER texto
            foreach (['organizacion','comercial','nombre_representante','cargo_representante','sector'] as $k) {
                if (isset($dataExterno[$k]) && is_string($dataExterno[$k])) {
                    $dataExterno[$k] = mb_strtoupper($dataExterno[$k], 'UTF-8');
                }
            }

            $rulesExterno = [
                'organizacion'         => 'required|string|max:100',
                'comercial'            => 'sometimes|nullable|string|max:100',
                'representante'        => 'sometimes|nullable|string|max:13',
                'nombre_representante' => 'required|string|max:100',
                'cargo_representante'  => 'sometimes|nullable|string|max:100',
                'sector'               => 'required|string',
            ];
            $validatedExterno = \Validator::make($dataExterno, $rulesExterno)->validate();

            $externo = \App\Models\UsuarioExterno::firstOrNew(['id_persona' => $user->id_persona]);
            $externo->fill($validatedExterno);
            $externo->id_persona = $user->id_persona;
        }

        /* -------- 3) Guardado -------- */
        $user->fill($validatedPersona);
        $dirtyPersona = $user->isDirty();
        $dirtyExterno = $esJuridica ? $externo->isDirty() : false;

        if (!$dirtyPersona && !$dirtyExterno) {
            return redirect()->route('persona.perfil')->with('success', 'No se detectaron cambios.');
        }

        if ($dirtyPersona) $user->save();
        if ($esJuridica && $dirtyExterno) $externo->save();

        return redirect()->route('persona.perfil')->with('success', 'Perfil actualizado correctamente.');
    }



    public function perfil()
    {
        $user = auth()->user();

        // Si usas además datos geográficos / catálogos, déjalos como ya los cargas
        $provincias = \App\Models\Provincia::all();
        $cantones   = \App\Models\Canton::all();
        $parroquias = \App\Models\Parroquia::all();
        $perfiles   = \App\Models\Perfil::all();
        $estados    = \App\Models\EstadoPersona::all();

        // (Opcional) Si necesitas los datos del usuario_externo para autopoblar campos
        $externo = \App\Models\UsuarioExterno::firstWhere('id_persona', $user->id_persona);

        // 🔒 SOLO por tipo_persona (ignora rol)
        $tipoRaw = trim((string) ($user->tipo_persona ?? ''));
        $sinAcentos = strtr($tipoRaw, [
            'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U',
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u',
        ]);
        $tipoNorm = mb_strtolower($sinAcentos, 'UTF-8');
        $esJuridicaVista = (strpos($tipoNorm, 'juridic') !== false); // capta "juridica"/"juridico"

        return view('persona.perfil', compact(
            'user','externo','provincias','cantones','parroquias','perfiles','estados','esJuridicaVista'
        ));
    }

    
        // Actualizar una persona (admin)
    public function updateAdmin(Request $request, string $id)
    {
        $persona = \App\Models\Persona::find($id);
        if (!$persona) {
            return response()->json(['message' => 'Persona no encontrada'], 404);
        }

        // ¿JURÍDICA por tipo o porque ya tiene UE?
        $tipoRaw = trim((string) ($persona->tipo_persona ?? ''));
        $sinAcentos = strtr($tipoRaw, ['Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ü'=>'U','á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u']);
        $tipoNorm = mb_strtolower($sinAcentos, 'UTF-8');
        $esJuridicaTipo = (strpos($tipoNorm, 'juridic') !== false);
        $tieneUE = \App\Models\UsuarioExterno::where('id_persona', $persona->id_persona)->exists();

        $esJuridica = $esJuridicaTipo || $tieneUE;

        // Si vienen separados los nombres del representante, combínalos
        if ($esJuridica && ($request->filled('rep_nombres') || $request->filled('rep_apellidos'))) {
            $request->merge([
                'nombre_representante' => trim(($request->input('rep_nombres','')).' '.$request->input('rep_apellidos',''))
            ]);
        }

        // Reglas Persona
        $rulesPersona = [
            'usuario' => ['sometimes','string','max:50', Rule::unique('persona','usuario')->ignore($persona->id_persona, 'id_persona')],
            'nombres'   => 'sometimes|string|max:100',
            'apellidos' => 'sometimes|string|max:100',
            'email'     => ['sometimes','email','max:100', Rule::unique('persona','email')->ignore($persona->id_persona, 'id_persona')],
            'id_perfil' => 'sometimes|exists:perfil,id_perfil',             // se puede cambiar, pero no afecta el tipo
            'id_estado_persona' => 'sometimes|exists:estado_persona,id_estado_persona',
            'cedula'    => ['sometimes','string','max:20', Rule::unique('persona','cedula')->ignore($persona->id_persona, 'id_persona')],
            'genero'    => 'nullable|string|max:10',
            'telefono'  => 'nullable|string|max:20',
            'celular'   => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'id_provincia' => 'nullable|exists:provincia,id_provincia',
            'id_canton'    => 'nullable|exists:canton,id_canton',
            'id_parroquia' => 'nullable|exists:parroquia,id_parroquia',
            'correo_alternativo' => 'nullable|email|max:100',
            'nivel_instruccion'  => 'nullable|string|max:50',
            'consentimiento'     => 'nullable|string|max:10',
        ];

        // Reglas UE (solo si el tipo es jurídica o ya tiene UE)
        $rulesExterno = [];
        if ($esJuridica) {
            $rulesExterno = [
                'organizacion'         => 'required|string|max:100',
                'comercial'            => 'sometimes|nullable|string|max:100',
                'representante'        => 'sometimes|nullable|string|max:13',
                'nombre_representante' => 'required|string|max:100',
                'cargo_representante'  => 'sometimes|nullable|string|max:100',
                
            ];
        }

        $validator = \Validator::make($request->all(), array_merge($rulesPersona, $rulesExterno));
        if ($validator->fails()) return response()->json($validator->errors(), 400);

        $data = $validator->validated();

        // Upper a texto (no emails/números)
        foreach (['nombres','apellidos','direccion','nivel_instruccion','genero'] as $k) {
            if (isset($data[$k]) && is_string($data[$k])) $data[$k] = mb_strtoupper(trim($data[$k]), 'UTF-8');
        }

        $dataExterno = [];
        if ($esJuridica) {
            foreach (['organizacion','comercial','nombre_representante','cargo_representante','sector'] as $k) {
                if (array_key_exists($k,$data) && is_string($data[$k])) $dataExterno[$k] = mb_strtoupper(trim($data[$k]), 'UTF-8');
            }
            if (array_key_exists('representante',$data)) $dataExterno['representante'] = trim($data['representante']);
        }

        // Guardar Persona
        $persona->fill(array_intersect_key($data, array_flip([
            'usuario','nombres','apellidos','email','id_perfil','id_estado_persona',
            'cedula','genero','telefono','celular','direccion','id_provincia','id_canton',
            'id_parroquia','correo_alternativo','nivel_instruccion','consentimiento'
        ])));
        $dirtyPersona = $persona->isDirty();

        // Guardar / crear UE
        $dirtyExterno = false;
        if ($esJuridica) {
            $externo = \App\Models\UsuarioExterno::firstOrNew(['id_persona' => $persona->id_persona]);
            $externo->fill($dataExterno);
            $externo->id_persona = $persona->id_persona;
            $dirtyExterno = $externo->isDirty();
        }

        if (!$dirtyPersona && !$dirtyExterno) return back()->with('success', 'No se detectaron cambios.');
        if ($dirtyPersona) $persona->save();
        if ($esJuridica && $dirtyExterno) $externo->save();

        return back()->with('success', 'Persona actualizada correctamente');
    }


    public function updatePassword(Request $request)
    {
        $user = auth()->user(); // instancia de Persona autenticada

        $request->validate([
            // Valida que la actual coincida con el hash del usuario autenticado.
            // Esta regla usa getAuthPassword(), que en tu modelo retorna $this->contraseña.
            'current_password' => ['required', 'current_password'],

            // Nueva contraseña + confirmación y política de complejidad
            'password' => [
                'required', 'string', 'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'current_password.current_password' => 'La contraseña actual no es correcta.',
            'password.confirmed' => 'La confirmación no coincide.',
        ]);

        // Asignar (activará tu mutator setContraseñaAttribute -> bcrypt)
        $user->contraseña = $request->password;
        $user->save();

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    // // Eliminar una persona
    // public function destroy($id)
    // {
    //     $persona = Persona::findOrFail($id);

    //     if (!$persona) {
    //         return redirect()->back()->with('error', 'Persona no encontrada');
    //     }

    //     // Cambiar estado a inactivo
    //     $persona->id_estado_persona = 2;
    //     $persona->save();

    //     // Aplicar soft delete
    //     $persona->delete();

    //     return back()->with('success', 'Usuario marcado como inactivo');
    // }

    public function getCantonesPorProvincia(Provincia $provincia)
    {
        return response()->json($provincia->canton()->orderBy('nombre_canton')->get());
    }
    public function getParroquiasPorCanton(Canton $canton)
    {
        return response()->json($canton->parroquia()->orderBy('nombre_parroquia')->get());
    }

    // public function restore($id)
    // { 
    //     $persona = Persona::withTrashed()->findOrFail($id);

    //     $persona->restore();
    //     $persona->id_estado_persona = 1; // Volver a activo
    //     $persona->save();

    //     return back()->with('success', 'Usuario reactivado correctamente');
    // }
}
