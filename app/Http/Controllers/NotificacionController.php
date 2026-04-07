<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    public function index(): View
    {
        $notificaciones = Notificacion::query()
            ->with('incendio')
            ->orderBy('leido')
            ->orderByDesc('created_at')
            ->get();

        return view('notificaciones.index', compact('notificaciones'));
    }

    public function marcarComoLeida(Notificacion $notificacion): RedirectResponse
    {
        $notificacion->update(['leido' => true]);

        return redirect()->route('notificaciones.index');
    }

    public function marcarTodasComoLeidas(): RedirectResponse
    {
        Notificacion::query()
            ->where('leido', false)
            ->update(['leido' => true]);

        return redirect()->route('notificaciones.index');
    }
}
