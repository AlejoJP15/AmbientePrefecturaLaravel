<?php
// app/Http/Controllers/CatalogoObligacionesController.php
namespace App\Http\Controllers;

use App\Models\TipoObligacion;
use App\Models\ItemObligacion;
use Illuminate\Http\Request;

class CatalogoObligacionesController extends Controller
{
    public function tipos()
    {
        return TipoObligacion::where('activo',true)
            ->orderBy('nombre')
            ->get(['id_tipo','nombre','requiere_periodo']);
    }

    public function items(Request $request)
    {
        $idTipo = $request->query('id_tipo');
        abort_unless($idTipo, 400, 'id_tipo requerido');

        return ItemObligacion::where('activo',true)
            ->where('id_tipo',$idTipo)
            ->orderBy('descripcion')
            ->get(['id_item','descripcion']);
    }
}
