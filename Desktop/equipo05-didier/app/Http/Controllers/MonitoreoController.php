<?php

namespace App\Http\Controllers;

use App\Models\Incendio;
use Illuminate\View\View;

class MonitoreoController extends Controller
{
    public function index(): View
    {
        $incendios = Incendio::query()
            ->enMonitoreo()
            ->orderByDesc('updated_at')
            ->orderByDesc('fecha_inicio')
            ->get();

        return view('monitoreo.index', compact('incendios'));
    }
}
