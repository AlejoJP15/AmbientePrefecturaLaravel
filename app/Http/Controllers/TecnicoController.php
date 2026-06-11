<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class TecnicoController extends Controller
{
    public function home(): RedirectResponse
    {
        return redirect()->route('tecnico.obligaciones.listado');
    }

    public function proyectos(): RedirectResponse
    {
        return redirect()->route('tecnico.obligaciones.listado');
    }
}


