<?php
namespace App\Http\Controllers;

use App\Models\{DonacionesAsignacion, Donacion, Asignacion};
use Illuminate\Http\Request;

class DonacionesAsignacionController extends Controller
{
    public function index() {
        $items = DonacionesAsignacion::with(['donacion','asignacion'])->get();
        return view('donacionesasignaciones.index', compact('items'));
    }

    public function create() {
        $donaciones = Donacion::all();
        $asignaciones = Asignacion::all();
        return view('donacionesasignaciones.create', compact('donaciones','asignaciones'));
    }

    public function store(Request $request) {
        $request->validate([
            'donacionid'=>'required|integer|exists:donaciones,donacionid',
            'asignacionid'=>'required|integer|exists:asignaciones,asignacionid',
            'montoasignado'=>'required|numeric|min:0',
            'fechaasignacion'=>'nullable|date',
        ]);
        DonacionesAsignacion::create($request->only(['donacionid','asignacionid','montoasignado','fechaasignacion']));
        return redirect()->route('donacionesasignaciones.index')->with('success','Registro creado.');
    }

    public function edit($id) {
        $item = DonacionesAsignacion::findOrFail($id);
        $donaciones = Donacion::all();
        $asignaciones = Asignacion::all();
        return view('donacionesasignaciones.edit', compact('item','donaciones','asignaciones'));
    }

    public function update(Request $request, $id) {
        $item = DonacionesAsignacion::findOrFail($id);
        $request->validate([
            'donacionid'=>'required|integer|exists:donaciones,donacionid',
            'asignacionid'=>'required|integer|exists:asignaciones,asignacionid',
            'montoasignado'=>'required|numeric|min:0',
            'fechaasignacion'=>'nullable|date',
        ]);
        $item->update($request->only(['donacionid','asignacionid','montoasignado','fechaasignacion']));
        return redirect()->route('donacionesasignaciones.index')->with('success','Registro actualizado.');
    }

    public function destroy($id) {
        DonacionesAsignacion::findOrFail($id)->delete();
        return redirect()->route('donacionesasignaciones.index')->with('success','Registro eliminado.');
    }
}
