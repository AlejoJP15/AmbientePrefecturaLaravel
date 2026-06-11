<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ItemObligacion;
use App\Models\TipoObligacion;
use Illuminate\Http\Request;

class ItemObligacionController extends Controller
{
    public function index() {
        $items = ItemObligacion::with('tipo:id_tipo,nombre')
            ->orderByDesc('id_item')->paginate(25);
        $tipos = TipoObligacion::where('activo',true)
            ->orderBy('nombre')->get(['id_tipo','nombre']);

        return view('admin.items.index', compact('items','tipos'));
    }

    public function create() {
        $tipos = TipoObligacion::where('activo',true)
            ->orderBy('nombre')->get(['id_tipo','nombre']);

        return view('admin.items.create', compact('tipos'));
    }

    public function store(Request $request) {
        $data = $request->validate([
            'id_tipo'     => ['required','integer','exists:tipos_obligacion,id_tipo'],
            'descripcion' => ['required','string','max:255'],
            'activo'      => ['sometimes','boolean'],
        ]);

        // Evitar duplicado (id_tipo, descripcion)
        $exists = ItemObligacion::where('id_tipo', $data['id_tipo'])
            ->where('descripcion', $data['descripcion'])
            ->exists();

        if ($exists) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Ya existe un ítem con esa descripción en el tipo seleccionado.'
                ], 422);
            }
            return back()->withErrors([
                'descripcion' => 'Ya existe un ítem con esa descripción en el tipo seleccionado.'
            ])->withInput();
        }

        $data['activo'] = $request->boolean('activo', true);
        $item = ItemObligacion::create($data);

        // ← AJAX / fetch
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => true, 'id' => $item->id_item], 201);
        }

        return redirect()
            ->route('admin.items-obligacion.index')
            ->with('success','Ítem creado.');
    }

    public function edit($id) {
        $item = ItemObligacion::findOrFail($id);
        $tipos = TipoObligacion::orderBy('nombre')->get(['id_tipo','nombre']);
        return view('admin.items.edit', compact('item','tipos'));
    }

    public function update(Request $request, $id) {
        $item = ItemObligacion::findOrFail($id);

        $data = $request->validate([
            'id_tipo'     => ['required','integer','exists:tipos_obligacion,id_tipo'],
            'descripcion' => ['required','string','max:255'],
            'activo'      => ['sometimes','boolean'],
        ]);

        // Evitar duplicado por (id_tipo, descripcion) excepto el mismo
        $exists = ItemObligacion::where('id_tipo',$data['id_tipo'])
            ->where('descripcion',$data['descripcion'])
            ->where('id_item','<>',$item->id_item)
            ->exists();

        if ($exists) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Ya existe un ítem con esa descripción en el tipo seleccionado.'
                ], 422);
            }
            return back()->withErrors([
                'descripcion'=>'Ya existe un ítem con esa descripción en el tipo seleccionado.'
            ])->withInput();
        }

        $data['activo'] = $request->boolean('activo', true);
        $item->update($data);

        // ← AJAX / fetch
        if ($request->ajax() || $request->wantsJson()) {
            return response()->noContent(); // 204
        }

        return redirect()
            ->route('admin.items-obligacion.index')
            ->with('success','Ítem actualizado.');
    }

    public function destroy(Request $request, $id) {
        $item = ItemObligacion::findOrFail($id);
        $item->delete();

        // ← AJAX / fetch
        if ($request->ajax() || $request->wantsJson()) {
            return response()->noContent(); // 204
        }

        return back()->with('success','Ítem eliminado.');
    }

    public function toggle(Request $request, $id) {
        $item = ItemObligacion::findOrFail($id);
        $item->activo = !$item->activo;
        $item->save();

        // ← AJAX / fetch
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => true, 'activo' => (bool)$item->activo], 200);
        }

        return back()->with('success','Estado actualizado.');
    }
}