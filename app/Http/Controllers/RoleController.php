<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();
        return view('roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role berhasil ditambahkan!');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if ($role->name === 'admin' && $request->name !== 'admin') {
            return redirect()->route('roles.index')->with('error', 'Nama role admin utama tidak boleh diubah!');
        }

        $role->update(['name' => $request->name]);

        $permissions = $request->permissions ?? [];
        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')->with('success', 'Role berhasil diperbarui!');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            return redirect()->route('roles.index')->with('error', 'Role admin utama tidak dapat dihapus!');
        }

        if ($role->users()->exists()) {
            return redirect()->route('roles.index')->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh staff!');
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus!');
    }
}
