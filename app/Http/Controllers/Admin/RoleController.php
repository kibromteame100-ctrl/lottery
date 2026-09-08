<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    // ── Roles list ────────────────────────────────────────────────────────────
    public function index()
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $roles = Role::with('permissions')
            ->withCount('users')
            ->orderBy('name')
            ->get();

        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode(' ', $p->name, 2)[1] ?? 'general';
        });

        return view('admin.settings.roles.index', compact('roles', 'permissions'));
    }

    // ── Create role form ──────────────────────────────────────────────────────
    public function create()
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode(' ', $p->name, 2)[1] ?? 'general';
        });

        return view('admin.settings.roles.create', compact('permissions'));
    }

    // ── Store new role ────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:80',
                                'unique:roles,name',
                                'regex:/^[a-z0-9\-]+$/'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);

        if (!empty($data['permissions'])) {
            $perms = Permission::whereIn('id', $data['permissions'])->get();
            $role->syncPermissions($perms);
        }

        AuditLog::record('role_created', Role::class, $role->id, [],
            ['name' => $role->name, 'permissions' => $data['permissions'] ?? []]);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->name}\" created successfully.");
    }

    // ── Edit role form ────────────────────────────────────────────────────────
    public function edit(Role $role)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $permissions = Permission::orderBy('name')->get()->groupBy(function ($p) {
            return explode(' ', $p->name, 2)[1] ?? 'general';
        });

        $role->load('permissions');

        return view('admin.settings.roles.edit', compact('role', 'permissions'));
    }

    // ── Update role ───────────────────────────────────────────────────────────
    public function update(Request $request, Role $role)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:80',
                                "unique:roles,name,{$role->id}",
                                'regex:/^[a-z0-9\-]+$/'],
            'permissions'   => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $old = ['name' => $role->name, 'permissions' => $role->permissions->pluck('name')->toArray()];

        $role->update(['name' => $data['name']]);

        $perms = !empty($data['permissions'])
            ? Permission::whereIn('id', $data['permissions'])->get()
            : collect();

        $role->syncPermissions($perms);

        AuditLog::record('role_updated', Role::class, $role->id, $old,
            ['name' => $role->name, 'permissions' => $data['permissions'] ?? []]);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->name}\" updated successfully.");
    }

    // ── Delete role ───────────────────────────────────────────────────────────
    public function destroy(Role $role)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        // Protect built-in roles
        if (in_array($role->name, ['super-admin', 'payment-reviewer', 'report-viewer'])) {
            return back()->with('error', "The \"{$role->name}\" role is built-in and cannot be deleted.");
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', "Cannot delete role \"{$role->name}\" — it is assigned to {$role->users()->count()} user(s). Reassign them first.");
        }

        AuditLog::record('role_deleted', Role::class, $role->id,
            ['name' => $role->name, 'permissions' => $role->permissions->pluck('name')->toArray()], []);

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role deleted.");
    }

    // ── Permissions list & create ─────────────────────────────────────────────
    public function permissions()
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $permissions = Permission::withCount('roles')->orderBy('name')->get();

        return view('admin.settings.roles.permissions', compact('permissions'));
    }

    public function storePermission(Request $request)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100',
                       'unique:permissions,name',
                       'regex:/^[a-z0-9 \-]+$/'],
        ]);

        $perm = Permission::create(['name' => $data['name'], 'guard_name' => 'web']);

        AuditLog::record('permission_created', Permission::class, $perm->id, [], ['name' => $perm->name]);

        return back()->with('success', "Permission \"{$perm->name}\" created.");
    }

    public function destroyPermission(Permission $permission)
    {
        abort_unless(auth()->user()->hasRole('super-admin'), 403);

        // Check no roles use it
        if ($permission->roles()->count() > 0) {
            return back()->with('error', "Cannot delete — this permission is assigned to {$permission->roles()->count()} role(s).");
        }

        AuditLog::record('permission_deleted', Permission::class, $permission->id,
            ['name' => $permission->name], []);

        $permission->delete();

        return back()->with('success', "Permission deleted.");
    }
}
