<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\Comentario;
use App\Models\Proyecto;

class MenuComposerServiceProvider extends ServiceProvider
{
    public function boot()
{
    View::composer('*', function ($view) {
        $unreadCount = 0;

        if (Auth::check()) {
            $user = Auth::user();
            $userId = $user->id_persona ?? $user->id;

            $proyectoIds = Proyecto::where('id_usuario', $userId)->pluck('id_proyecto');

            if ($proyectoIds->isNotEmpty()) {
                $unreadCount = Comentario::whereIn('id_proyecto', $proyectoIds)
                    ->where('leido', false)
                    ->count();
            }
        }

        $view->with('unreadInboxCount', $unreadCount);
    });
}
}
