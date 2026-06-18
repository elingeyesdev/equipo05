<?php

namespace Modules\Inventario\Http\Controllers;

use Modules\Inventario\Models\Donante;
use App\Support\OwnershipScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Inventario\Http\Requests\DonanteRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Modules\Inventario\Mail\DonanteBienvenida;
use Illuminate\View\View;

class DonanteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $donantes = Donante::orderBy('nombre')->get();

        return view('inventario::donante.index', compact('donantes'))
            ->with('i', 0);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $donante = new Donante();

        return view('inventario::donante.create', compact('donante'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(DonanteRequest $request)
    {
        $data = $request->validated();
        
        // Guardar la contraseña sin encriptar para enviarla por correo
        $plainPassword = $data['password'] ?? null;

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        // Manejar el checkbox de cambiar_password
        $data['cambiar_password'] = $request->has('cambiar_password');

        $donante = Donante::create($data);
        
        // Enviar correo de bienvenida si tiene email y password
        if ($donante->email && $plainPassword) {
            try {
                Mail::to($donante->email)->send(new DonanteBienvenida($donante, $plainPassword));
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Error enviando correo de bienvenida: ' . $e->getMessage());
            }
        }

        // If AJAX request, return JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Donante creado exitosamente',
                'donante' => $donante
            ]);
        }

        return Redirect::route('inventario.donante.index')
            ->with('success', 'Donante creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $donante = Donante::find($id);

        return view('inventario::donante.show', compact('donante'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $donante = Donante::find($id);

        return view('inventario::donante.edit', compact('donante'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(DonanteRequest $request, $id): RedirectResponse
    {
        $donante = Donante::findOrFail($id);
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        
        // Manejar el checkbox de cambiar_password
        $data['cambiar_password'] = $request->has('cambiar_password');

        $donante->update($data);

        return Redirect::route('inventario.donante.index')
            ->with('success', 'Donante actualizado exitosamente.');
    }

    public function destroy($id): RedirectResponse
    {
        $this->assertNotDonanteOnly();

        Donante::find($id)->delete();

        return Redirect::route('inventario.donante.index')
            ->with('success', 'Donante eliminado exitosamente.');
    }

    public function miPerfil(): View
    {
        $donante = OwnershipScope::ensureInventarioDonanteProfile(auth()->user());

        return view('inventario::donante.edit', compact('donante'))
            ->with('esPerfilPropio', true);
    }

    public function updateMiPerfil(DonanteRequest $request): RedirectResponse
    {
        $donante = OwnershipScope::ensureInventarioDonanteProfile(auth()->user());
        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['cambiar_password'] = $request->has('cambiar_password');
        $donante->update($data);

        return Redirect::route('inventario.donante.mi-perfil')
            ->with('success', 'Perfil actualizado correctamente.');
    }
}







