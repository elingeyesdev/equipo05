<?php

namespace App\Http\Controllers;

use App\Models\Incendio;
use App\Models\Voluntario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoluntarioController extends Controller
{
    public function index(Request $request): View
    {
        $estado = $request->string('estado')->toString();
        $incendioId = $request->integer('incendio_id');
        $busqueda = trim($request->string('q')->toString());

        $estadoFiltro = $estado !== '' ? $estado : 'activo';

        $voluntarios = Voluntario::query()
            ->where('estado', $estadoFiltro)
            ->when($incendioId > 0, function (Builder $query) use ($incendioId) {
                $query->whereHas('incendios', function (Builder $incendiosQuery) use ($incendioId) {
                    $incendiosQuery->where('incendios.id', $incendioId);
                });
            })
            ->when($busqueda !== '', function (Builder $query) use ($busqueda) {
                $query->where(function (Builder $nestedQuery) use ($busqueda) {
                    $nestedQuery
                        ->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('apellido', 'like', "%{$busqueda}%");
                });
            })
            ->with([
                'incendios' => function ($query) {
                    $query->orderByDesc('fecha_inicio');
                },
            ])
            ->orderBy('nombre')
            ->orderBy('apellido')
            ->get();

        $totalActivos = Voluntario::query()
            ->where('estado', 'activo')
            ->count();

        $incendios = Incendio::query()
            ->orderByDesc('fecha_inicio')
            ->get(['id', 'titulo', 'estado']);

        return view('voluntarios.index', [
            'voluntarios' => $voluntarios,
            'totalActivos' => $totalActivos,
            'estadoFiltro' => $estadoFiltro,
            'incendioIdFiltro' => $incendioId > 0 ? $incendioId : null,
            'busqueda' => $busqueda,
            'incendios' => $incendios,
        ]);
    }

    public function show(int $id): View
    {
        $voluntario = Voluntario::query()
            ->with([
                'incendios' => function ($query) {
                    $query->orderByDesc('fecha_inicio');
                },
            ])
            ->findOrFail($id);

        return view('voluntarios.show', compact('voluntario'));
    }
}
