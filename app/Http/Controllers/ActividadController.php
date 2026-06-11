<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use Illuminate\Support\Facades\DB;

class ActividadController extends Controller
{
    public function index()
    {
        return view('usuario.proyectos.create');
    }

    public function roots()
    {
        $primerNivel = Actividad::NIVEL_ORDER[0]; // SECCION
        $roots = DB::table('actividad')
            ->select('id_actividad','codigo_actividad','descripcion_actividad','nivel')
            ->whereRaw('upper(nivel) = ?', [mb_strtoupper($primerNivel,'UTF-8')])
            ->orderBy('codigo_actividad')
            ->get();

        if ($roots->count() === 0) {
            $minLen = DB::table('actividad')->selectRaw('MIN(char_length(codigo_actividad)) as min')->value('min');
            $roots = DB::table('actividad')
                ->select('id_actividad','codigo_actividad','descripcion_actividad','nivel')
                ->whereRaw('char_length(codigo_actividad) = ?', [$minLen])
                ->orderBy('codigo_actividad')
                ->get();
        }

        // De-duplicar por descripcion a nivel raíz (por si acaso)
        $roots = $this->uniqueByDescripcion($roots);

        return response()->json($roots);
    }

    /**
     * Devuelve "hijos visibles" aplicando colapso de niveles repetidos
     * y de-duplicado por descripcion entre hermanos.
     */
    public function children($codigo)
    {
        // 1) Colapsar niveles consecutivos con misma descripcion (si aplica)
        [$visibleParentCodigo, $visibleChildren] = $this->collapsedChildrenFor($codigo);

        // 2) De-duplicar por descripcion entre hermanos
        $visibleChildren = $this->uniqueByDescripcion($visibleChildren);

        return response()->json($visibleChildren);
    }

    /**
     * Indica si (tras colapsar repeticiones) el nodo es hoja.
     */
    public function isLeaf($codigo)
    {
        // Aplica el mismo colapso que en children()
        [$visibleParentCodigo, $visibleChildren] = $this->collapsedChildrenFor($codigo);

        return response()->json(['leaf' => count($visibleChildren) === 0]);
    }

    /* =========================
     * Helpers privados
     * ========================= */

    /**
     * Obtiene hijos puros por "siguiente nivel" o por longitud (fallback),
     * tal como ya hacías.
     */
    private function rawChildrenOf(string $codigo): \Illuminate\Support\Collection
    {
        $parent = DB::table('actividad')
            ->select('codigo_actividad','nivel','descripcion_actividad')
            ->where('codigo_actividad', $codigo)
            ->first();

        if (!$parent) return collect([]);

        $nextLevel = Actividad::nextLevel($parent->nivel);

        if ($nextLevel) {
            $children = DB::table('actividad')
                ->select('id_actividad','codigo_actividad','descripcion_actividad','nivel')
                ->whereRaw('upper(nivel) = ?', [mb_strtoupper($nextLevel,'UTF-8')])
                ->where('codigo_actividad','like',$codigo.'%')
                ->orderBy('codigo_actividad')
                ->get();

            if ($children->count() > 0) {
                return $children;
            }
        }

        // Fallback por longitud
        $minLenHijo = DB::table('actividad')
            ->where('codigo_actividad','like',$codigo.'%')
            ->whereRaw('char_length(codigo_actividad) > char_length(?)', [$codigo])
            ->selectRaw('MIN(char_length(codigo_actividad)) as minlen')
            ->value('minlen');

        if (!$minLenHijo) return collect([]);

        return DB::table('actividad')
            ->select('id_actividad','codigo_actividad','descripcion_actividad','nivel')
            ->where('codigo_actividad','like',$codigo.'%')
            ->whereRaw('char_length(codigo_actividad) = ?', [$minLenHijo])
            ->orderBy('codigo_actividad')
            ->get();
    }

    /**
     * Colapsa cadenas de un solo hijo con misma descripcion que el padre:
     * Mientras haya 1 hijo y comparta descripcion con el padre, "salta" al hijo,
     * y vuelve a consultar sus hijos, hasta romper la condición o llegar a hoja.
     *
     * Devuelve: [codigo_del_padre_visible, hijos_visibles]
     */
    private function collapsedChildrenFor(string $codigo): array
    {
        $guard = 10; // evita loops raros

        // descripcion del punto de partida
        $parent = DB::table('actividad')
            ->select('codigo_actividad','descripcion_actividad','nivel')
            ->where('codigo_actividad', $codigo)
            ->first();

        if (!$parent) return [$codigo, collect([])];

        $currentCodigo = $codigo;
        $currentDesc   = $parent->descripcion_actividad;

        while ($guard-- > 0) {
            $children = $this->rawChildrenOf($currentCodigo);

            if ($children->count() !== 1) {
                // 0 o >1: ya es un punto visible
                return [$currentCodigo, $children];
            }

            $only = $children->first();
            // Si el único hijo tiene la MISMA descripcion, saltamos hacia abajo
            if (mb_strtoupper($only->descripcion_actividad, 'UTF-8') === mb_strtoupper($currentDesc, 'UTF-8')) {
                $currentCodigo = $only->codigo_actividad;
                $currentDesc   = $only->descripcion_actividad;
                // y seguimos el bucle para ver sus hijos
                continue;
            }

            // Si la descripción ya cambió, estos hijos ya son visibles
            return [$currentCodigo, $children];
        }

        // Por seguridad: devolver lo que haya en este punto
        return [$currentCodigo, $this->rawChildrenOf($currentCodigo)];
    }

    /**
     * De-duplica una colección de hijos por descripcion_actividad.
     * Mantiene el registro con código más corto (más genérico).
     */
    private function uniqueByDescripcion(\Illuminate\Support\Collection $rows): \Illuminate\Support\Collection
    {
        if ($rows->isEmpty()) return $rows;

        return $rows
            ->groupBy(function ($r) {
                return mb_strtoupper($r->descripcion_actividad ?? '', 'UTF-8');
            })
            ->map(function ($group) {
                // elegir el de código más corto (o el primero si empatan)
                return $group->sortBy(function ($r) {
                    return strlen($r->codigo_actividad ?? '');
                })->first();
            })
            ->values();
    }
}
