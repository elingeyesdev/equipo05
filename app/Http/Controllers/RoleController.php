<?php

namespace App\Http\Controllers;

use App\Support\AccessControl;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::query()
            ->whereIn('name', AccessControl::FINAL_ROLES)
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        abort(403, 'Los roles operativos están definidos por el sistema.');
    }

    public function store(Request $request)
    {
        abort(403, 'Los roles operativos están definidos por el sistema.');
    }

    public function edit($id)
    {
        $role = Role::findById($id);

        abort_unless(in_array($role->name, AccessControl::FINAL_ROLES, true), 404);

        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findById($id);

        abort_unless(in_array($role->name, AccessControl::FINAL_ROLES, true), 404);

        $request->validate([
            'descripcion' => 'nullable|string|max:255',
        ]);

        $role->update([
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Descripción del rol actualizada.');
    }

    public function destroy($id)
    {
        abort(403, 'No se pueden eliminar roles operativos del sistema.');
    }
}
