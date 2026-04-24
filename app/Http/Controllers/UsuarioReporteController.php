<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Donacion;
use Illuminate\Http\Request;

class UsuarioReporteController extends Controller
{
    // ➊ NUEVO: pantalla para seleccionar usuario
    public function seleccionarUsuario()
    {
        // Puedes paginar si hay muchos
        $usuarios = Usuario::orderBy('nombre')->orderBy('apellido')->get();

        return view('usuarios.seleccionar_estado_cuenta', compact('usuarios'));
    }

    // ➋ NUEVO: recibe el usuario seleccionado y redirige al estado de cuenta
    public function mostrarDesdeSeleccion(Request $request)
    {
        $request->validate([
            'usuarioid' => 'required|integer|exists:usuarios,usuarioid',
        ]);

        return redirect()->route('usuarios.estadoCuenta', $request->usuarioid);
    }

    // ➌ YA TENÍAMOS: muestra el estado de cuenta de un usuario concreto
    public function estadoCuenta($usuarioid)
    {
        $usuario = Usuario::findOrFail($usuarioid);

        $donaciones = Donacion::with([
                'campania',
                'estado',
                'saldo',
                'asignacionesPivot.asignacion.detalles',
            ])
            ->withSum('asignacionesPivot as total_asignado', 'montoasignado')
            ->where('usuarioid', $usuarioid)
            ->get();

        $totales = [
            'total_donado'   => $donaciones->sum('monto'),
            'total_asignado' => $donaciones->sum('total_asignado'),
            'total_saldo'    => $donaciones->sum(function ($d) {
                return optional($d->saldo)->saldodisponible ?? 0;
            }),
        ];

        return view('usuarios.estado_cuenta', compact('usuario', 'donaciones', 'totales'));
    }
}
