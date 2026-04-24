<?php
namespace App\Http\Controllers;

use App\Models\{SaldosDonacion, Donacion};
use Illuminate\Http\Request;

class SaldosDonacionController extends Controller
{
    public function index() {
        $saldos = SaldosDonacion::with('donacion')->get();
        return view('saldosdonaciones.index', compact('saldos'));
    }

    public function create() {
        $donaciones = Donacion::all();
        return view('saldosdonaciones.create', compact('donaciones'));
    }

    public function store(Request $request) {
        $request->validate([
            'donacionid'=>'required|integer|unique:saldosdonaciones,donacionid|exists:donaciones,donacionid',
            'montooriginal'=>'required|numeric|min:0',
            'montoutilizado'=>'nullable|numeric|min:0',
            'saldodisponible'=>'required|numeric|min:0',
            'ultimaactualizacion'=>'nullable|date',
        ]);
        SaldosDonacion::create($request->only([
            'donacionid','montooriginal','montoutilizado','saldodisponible','ultimaactualizacion'
        ]));
        return redirect()->route('saldosdonaciones.index')->with('success','Saldo creado.');
    }

    public function edit($id) {
        $saldo = SaldosDonacion::findOrFail($id);
        $donaciones = Donacion::all();
        return view('saldosdonaciones.edit', compact('saldo','donaciones'));
    }

    public function update(Request $request, $id) {
        $saldo = SaldosDonacion::findOrFail($id);
        $request->validate([
            'donacionid'=>'required|integer|exists:donaciones,donacionid|unique:saldosdonaciones,donacionid,' . $id . ',saldoid',
            'montooriginal'=>'required|numeric|min:0',
            'montoutilizado'=>'nullable|numeric|min:0',
            'saldodisponible'=>'required|numeric|min:0',
            'ultimaactualizacion'=>'nullable|date',
        ]);
        $saldo->update($request->only([
            'donacionid','montooriginal','montoutilizado','saldodisponible','ultimaactualizacion'
        ]));
        return redirect()->route('saldosdonaciones.index')->with('success','Saldo actualizado.');
    }

    public function destroy($id) {
        SaldosDonacion::findOrFail($id)->delete();
        return redirect()->route('saldosdonaciones.index')->with('success','Saldo eliminado.');
    }
}
