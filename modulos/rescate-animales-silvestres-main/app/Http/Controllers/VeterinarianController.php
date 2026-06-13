<?php

namespace Modules\Rescate\Http\Controllers;

use App\Support\AccessControl;
use App\Support\RescateAccess;
use Modules\Rescate\Models\Veterinarian;
use Modules\Rescate\Models\Person;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Rescate\Http\Requests\VeterinarianRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Modules\Rescate\Mail\VeterinarianApplicationResponse;
use Modules\Rescate\Services\User\UserTrackingService;

class VeterinarianController extends Controller
{
    public function __construct()
    {
        // Solo administradores o encargados pueden ver veterinarios
        // Administradores y encargados pueden crear veterinarios, solo admin puede eliminar
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        RescateAccess::assertCanManagePeople();
        $veterinarians = Veterinarian::with(['person.user'])->orderByDesc('id')->get();

        return view('veterinarian.index', compact('veterinarians'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $veterinarian = new Veterinarian();
        // Preseleccionar persona si viene desde el listado de personas
        $veterinarian->persona_id = $request->query('persona_id');
        // Excluir personas que ya son veterinarios
        $people = Person::whereDoesntHave('veterinarians')
            ->orderBy('nombre')
            ->get(['id','nombre']);
        return view('veterinarian.create', compact('veterinarian','people'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VeterinarianRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('cv')) {
            $data['cv_documentado'] = $request->file('cv')->store('cv', 'public');
        }
        $veterinarian = Veterinarian::create($data);

        // Si ya se crea aprobado, asignar rol al usuario vinculado
        if ($veterinarian->aprobado === true && $veterinarian->person?->user) {
            AccessControl::assignCanonicalRole($veterinarian->person->user, 'Veterinario');
        }

        // Registrar tracking de solicitud
        try {
            $user = $veterinarian->person?->user;
            app(UserTrackingService::class)->logApplication(
                'veterinarian',
                $veterinarian,
                $user?->id
            );
        } catch (\Exception $e) {
            \Log::warning('Error registrando tracking de solicitud de veterinario: ' . $e->getMessage());
        }

        return Redirect::route('rescate.veterinarians.index')
            ->with('success', 'Veterinario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $veterinarian = Veterinarian::findOrFail($id);

        return view('veterinarian.show', compact('veterinarian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $veterinarian = Veterinarian::findOrFail($id);
        $people = Person::orderBy('nombre')->get(['id','nombre']);
        return view('veterinarian.edit', compact('veterinarian','people'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VeterinarianRequest $request, Veterinarian $veterinarian): RedirectResponse
    {
        $data = $request->validated();

        // Si es un encargado (y no admin), solo puede cambiar aprobación y motivo de revisión
        $user = Auth::user();
        if ($user && AccessControl::userHasRole($user, 'Operador de Incendios') && ! AccessControl::userHasRole($user, 'Administrador')) {
            $data = Arr::only($data, ['aprobado', 'motivo_revision']);
        }

        if ($request->hasFile('cv')) {
            $data['cv_documentado'] = $request->file('cv')->store('cv', 'public');
        }
        $oldApproved = $veterinarian->aprobado;
        $veterinarian->update($data);
        $veterinarian->refresh();
        $veterinarian->load('person.user');

        // Enganchar aprobación con roles de Spatie
        $userModel = $veterinarian->person?->user;
        if ($userModel) {
            if ($veterinarian->aprobado === true) {
                AccessControl::assignCanonicalRole($userModel, 'Veterinario');
            } elseif ($veterinarian->aprobado === false || $veterinarian->aprobado === null) {
                AccessControl::removeCanonicalRole($userModel, 'Veterinario');
            }
        }

        // Registrar tracking si cambió el estado de aprobación
        if ($oldApproved !== $veterinarian->aprobado) {
            try {
                app(UserTrackingService::class)->logVeterinarianApproval(
                    $veterinarian,
                    $veterinarian->aprobado === true,
                    $oldApproved,
                    $veterinarian->motivo_revision
                );
            } catch (\Exception $e) {
                \Log::warning('Error registrando tracking de aprobación de veterinario: ' . $e->getMessage());
            }
        }

        // Enviar correo al ciudadano si cambió el estado de aprobación y hay motivo de revisión
        if ($oldApproved !== $veterinarian->aprobado && !empty($veterinarian->motivo_revision) && $userModel && $userModel->email) {
            try {
                $approved = $veterinarian->aprobado === true;
                Mail::to($userModel->email)->send(new VeterinarianApplicationResponse($veterinarian, $approved));
            } catch (\Exception $e) {
                \Log::error('Error enviando correo de respuesta de solicitud de veterinario: ' . $e->getMessage());
            }
        }

        return Redirect::route('rescate.veterinarians.index')
            ->with('success', 'Veterinario actualizado correctamente');
    }

    /**
     * Approve or reject a veterinarian application.
     */
    public function approve(Request $request, Veterinarian $veterinarian): RedirectResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'motivo_revision' => 'required|string|min:3',
        ]);

        $oldApproved = $veterinarian->aprobado;
        $veterinarian->aprobado = $validated['action'] === 'approve' ? true : false;
        $veterinarian->motivo_revision = $validated['motivo_revision'];
        $veterinarian->save();
        $veterinarian->refresh();
        $veterinarian->load('person.user');

        // Enganchar aprobación con roles de Spatie
        $userModel = $veterinarian->person?->user;
        if ($userModel) {
            if ($veterinarian->aprobado === true) {
                AccessControl::assignCanonicalRole($userModel, 'Veterinario');
            } elseif ($veterinarian->aprobado === false || $veterinarian->aprobado === null) {
                AccessControl::removeCanonicalRole($userModel, 'Veterinario');
            }
        }

        // Registrar tracking de aprobación/rechazo
        if ($oldApproved !== $veterinarian->aprobado) {
            try {
                app(UserTrackingService::class)->logVeterinarianApproval(
                    $veterinarian,
                    $veterinarian->aprobado === true,
                    $oldApproved,
                    $veterinarian->motivo_revision
                );
            } catch (\Exception $e) {
                \Log::warning('Error registrando tracking de aprobación de veterinario: ' . $e->getMessage());
            }
        }

        // Enviar correo al ciudadano si cambió el estado de aprobación
        if ($oldApproved !== $veterinarian->aprobado && $userModel && $userModel->email) {
            try {
                $approved = $veterinarian->aprobado === true;
                Mail::to($userModel->email)->send(new VeterinarianApplicationResponse($veterinarian, $approved));
            } catch (\Exception $e) {
                \Log::error('Error enviando correo de respuesta de solicitud de veterinario: ' . $e->getMessage());
            }
        }

        $message = $validated['action'] === 'approve' 
            ? 'La solicitud de veterinario ha sido aprobada correctamente.' 
            : 'La solicitud de veterinario ha sido rechazada correctamente.';

        return Redirect::route('rescate.veterinarians.index')
            ->with('success', $message);
    }

    public function destroy($id): RedirectResponse
    {
        RescateAccess::assertCanManagePeople();
        Veterinarian::findOrFail($id)->delete();

        return Redirect::route('rescate.veterinarians.index')
            ->with('success', 'Veterinario eliminado correctamente');
    }
}
