<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::with('permissions', 'users')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('roles.index', compact('roles', 'permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
        ]);

        Role::create(['name' => $request->input('name'), 'guard_name' => 'web']);

        return redirect()
            ->route('roles.index')
            ->with('success', "Role '{$request->input('name')}' berhasil dibuat.");
    }

    /**
     * Update an existing role name.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name,' . $role->id],
        ]);

        $role->update(['name' => $request->input('name')]);

        return redirect()
            ->route('roles.index')
            ->with('success', "Role berhasil diperbarui menjadi '{$role->name}'.");
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        // Protect default system roles (5 role inti sesuai konsep awal)
        $protected = ['Owner', 'Kepala Produksi', 'Mandor', 'Kepala Lapangan', 'Driver'];
        if (in_array($role->name, $protected)) {
            return redirect()
                ->route('roles.index')
                ->with('error', "Role sistem '{$role->name}' tidak dapat dihapus.");
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', "Role '{$role->name}' berhasil dihapus.");
    }
}
