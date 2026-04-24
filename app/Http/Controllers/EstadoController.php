<?php
namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    public function index() {
        $estados = Estado::withCount('donaciones')->get();
        return view('estados.index', compact('estados'));
    }

    public function create() { return view('estados.create'); }

    public function store(Request $request) {
        $request->validate([
            'nombre'=>'required|string|max:50|unique:estados,nombre',
            'descripcion'=>'nullable|string|max:255',
        ]);
        Estado::create($request->only(['nombre','descripcion']));
        return redirect()->route('estados.index')->with('success','Estado creado.');
    }

    public function edit($id) {
        $estado = Estado::findOrFail($id);
        return view('estados.edit', compact('estado'));
    }

    public function update(Request $request, $id) {
        $estado = Estado::findOrFail($id);
        $request->validate([
            'nombre'=>'required|string|max:50|unique:estados,nombre,' . $id . ',estadoid',
            'descripcion'=>'nullable|string|max:255',
        ]);
        $estado->update($request->only(['nombre','descripcion']));
        return redirect()->route('estados.index')->with('success','Estado actualizado.');
    }

    public function destroy($id) {
        Estado::findOrFail($id)->delete();
        return redirect()->route('estados.index')->with('success','Estado eliminado.');
    }
}
