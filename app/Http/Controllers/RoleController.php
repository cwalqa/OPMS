<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    // List all roles
    public function index()
    {
        $roles = Role::with('permissions')->paginate(10);
        $permissions = Permission::all();
        return view('admin.roles', compact('roles', 'permissions'));
    }

    // Store a new role
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array',
        ]);

        $role = Role::create([
            'name' => $request->name,
        ]);

        // Attach permissions to the new role
        $role->permissions()->sync($request->permissions);

        return redirect()->route('admin.roles')->with('success', 'Role created successfully.');
    }

    // Show the form to edit a role
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    // Update an existing role
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'required|array',
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        // Update permissions
        $role->permissions()->sync($request->permissions);

        return redirect()->route('admin.roles')->with('success', 'Role updated successfully.');
    }

    // Delete a role
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('admin.roles')->with('success', 'Role deleted successfully.');
    }
}
