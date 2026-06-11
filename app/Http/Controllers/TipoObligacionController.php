<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TipoObligacion;
use Illuminate\Http\Request;

class TipoObligacionController extends Controller
{
    public function index() {
        $tipos = TipoObligacion::orderBy('nombre')->paginate(20);
        return view('admin.tipos.index', compact('tipos'));
    }

    public function create() {
        return view('admin.tipos.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'nombre'           => ['required','string','max:120','unique:tipos_obligacion,nombre'],
            'requiere_periodo' => ['required','boolean'],
            'activo'           => ['sometimes','boolean'],
        ]);

        $data['activo'] = $request->boolean('activo', true);
        $tipo = TipoObligacion::create($data);

        // ← AJAX / fetch
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'id' => $tipo->id_tipo,
            ], 201);
        }

        return redirect()
            ->route('admin.tipos-obligacion.index')
            ->with('success','Tipo creado.');
    }

    public function edit($id) {
        $tipo = TipoObligacion::findOrFail($id);
        return view('admin.tipos.edit', compact('tipo'));
    }

    public function update(Request $request, $id) {
        $tipo = TipoObligacion::findOrFail($id);

        $data = $request->validate([
            'nombre'           => ['required','string','max:120','unique:tipos_obligacion,nombre,'.$tipo->id_tipo.',id_tipo'],
            'requiere_periodo' => ['required','boolean'],
            'activo'           => ['sometimes','boolean'],
        ]);

        $data['activo'] = $request->boolean('activo', true);
        $tipo->update($data);

        // ← AJAX / fetch
        if ($request->ajax() || $request->wantsJson()) {
            return response()->noContent(); // 204
        }

        return redirect()
            ->route('admin.tipos-obligacion.index')
            ->with('success','Tipo actualizado.');
    }

    public function destroy(Request $request, $id) {
        $tipo = TipoObligacion::findOrFail($id);
        $tipo->delete();

        // ← AJAX / fetch
        if ($request->ajax() || $request->wantsJson()) {
            return response()->noContent(); // 204
        }

        return back()->with('success','Tipo eliminado.');
    }

    public function toggle(Request $request, $id) {
        $tipo = TipoObligacion::findOrFail($id);
        $tipo->activo = !$tipo->activo;
        $tipo->save();

        // ← AJAX / fetch
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => true, 'activo' => (bool)$tipo->activo], 200);
        }

        return back()->with('success','Estado actualizado.');
    }
}