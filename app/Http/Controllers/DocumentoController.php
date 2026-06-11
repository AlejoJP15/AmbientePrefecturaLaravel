<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DocumentoController extends Controller
{
    public function firmar(Request $request, int $id): Response
    {
        return response('OK', 200);
    }
}


