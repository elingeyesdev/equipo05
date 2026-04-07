<?php

namespace App\Http\Controllers;

use App\Models\Incendio;
use Illuminate\View\View;

class HistorialController extends Controller
{
    public function index(): View
    {
        $incendios = Incendio::query()
            ->orderByDesc('created_at')
            ->get();

        return view('historial.index', compact('incendios'));
    }
}
