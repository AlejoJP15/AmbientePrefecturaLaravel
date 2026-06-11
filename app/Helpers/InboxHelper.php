<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\Comentario;
use App\Models\Proyecto;

function getUnreadInboxCount()
{
    if (!Auth::check()) return 0;

    $user = Auth::user();
    $userId = $user->id_persona ?? $user->id;

    $proyectoIds = Proyecto::where('id_usuario', $userId)->pluck('id_proyecto');

    if ($proyectoIds->isEmpty()) return 0;

    return Comentario::whereIn('id_proyecto', $proyectoIds)
        ->where('leido', false)
        ->count();
}
