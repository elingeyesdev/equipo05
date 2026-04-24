<?php
namespace App\Http\Controllers;

use App\Models\{DetallesAsignacion, Asignacion};
use Illuminate\Http\Request;

class DetallesAsignacionController extends Controller
{
    public function index() {
        $detalles = DetallesAsignacion::with('asignacion')->get();
        return view('detallesasignacion.index', compact('detalles'));
    }

    public function create() {
        $asignaciones = Asignacion::all();
        return view('detallesasignacion.create', compact('asignaciones'));
    }

    public function store(Request $request) {
        $request->validate([
            'asignacionid'=>'required|integer|exists:asignaciones,asignacionid',
            'concepto'=>'required|string|max:100',
            'cantidad'=>'required|integer|min:1',
            'preciounitario'=>'required|numeric|min:0',
            'imagenurl'=>'nullable|string|max:255',
        ]);
        DetallesAsignacion::create($request->only(['asignacionid','concepto','cantidad','preciounitario','imagenurl']));
        return redirect()->route('detallesasignacion.index')->with('success','Detalle agregado.');
    }

    public function edit($id) {
        $detalle = DetallesAsignacion::findOrFail($id);
        $asignaciones = Asignacion::all();
        return view('detallesasignacion.edit', compact('detalle','asignaciones'));
    }

    public function update(Request $request, $id) {
        $detalle = DetallesAsignacion::findOrFail($id);
        $request->validate([
            'asignacionid'=>'required|integer|exists:asignaciones,asignacionid',
            'concepto'=>'required|string|max:100',
            'cantidad'=>'required|integer|min:1',
            'preciounitario'=>'required|numeric|min:0',
            'imagenurl'=>'nullable|string|max:255',
        ]);
        $detalle->update($request->only(['asignacionid','concepto','cantidad','preciounitario','imagenurl']));
        return redirect()->route('detallesasignacion.index')->with('success','Detalle actualizado.');
    }

    public function destroy($id) {
        DetallesAsignacion::findOrFail($id)->delete();
        return redirect()->route('detallesasignacion.index')->with('success','Detalle eliminado.');
    }
}
