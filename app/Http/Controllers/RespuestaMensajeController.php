<?php

namespace App\Http\Controllers;

use App\Models\RespuestaMensaje;
use App\Models\Mensaje;
use App\Models\Usuario;
use Illuminate\Http\Request;

class RespuestaMensajeController extends Controller
{
    public function index()
    {
        $respuestas = RespuestaMensaje::with(['mensaje','usuario'])
            ->orderByDesc('respuestaid')
            ->paginate(15);

        return view('respuestasmensajes.index', compact('respuestas'));
    }

    public function create()
    {
        $mensajes = Mensaje::orderByDesc('mensajeid')->get();
        $usuarios = Usuario::orderBy('nombre')->get();

        return view('respuestasmensajes.create', compact('mensajes','usuarios'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mensajeid'      => 'required|integer|exists:mensajes,mensajeid',
            'usuarioid'      => 'required|integer|exists:usuarios,usuarioid',
            'contenido'      => 'required|string',
            'fecharespuesta' => 'nullable|date',
        ]);

        RespuestaMensaje::create($data);

        return redirect()->route('respuestasmensajes.index')
                         ->with('success','Respuesta creada.');
    }

    public function edit($id)
    {
        $respuesta = RespuestaMensaje::findOrFail($id);
        $mensajes  = Mensaje::orderByDesc('mensajeid')->get();
        $usuarios  = Usuario::orderBy('nombre')->get();

        return view('respuestasmensajes.edit', compact('respuesta','mensajes','usuarios'));
    }

    public function update(Request $request, $id)
    {
        $respuesta = RespuestaMensaje::findOrFail($id);

        $data = $request->validate([
            'mensajeid'      => 'required|integer|exists:mensajes,mensajeid',
            'usuarioid'      => 'required|integer|exists:usuarios,usuarioid',
            'contenido'      => 'required|string',
            'fecharespuesta' => 'nullable|date',
        ]);

        $respuesta->update($data);

        return redirect()->route('respuestasmensajes.index')
                         ->with('success','Respuesta actualizada.');
    }

    public function destroy($id)
    {
        RespuestaMensaje::findOrFail($id)->delete();

        return redirect()->route('respuestasmensajes.index')
                         ->with('success','Respuesta eliminada.');
    }
}
