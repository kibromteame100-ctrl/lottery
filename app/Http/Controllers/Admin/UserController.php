<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // ── Mobile customers ──────────────────────────────────────────────────────
    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $query = User::whereDoesntHave('roles')->withCount('ticketPurchases');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                                      ->orWhere('phone', 'like', "%{$s}%")
                                      ->orWhere('email', 'like', "%{$s}%"));
        }
        if ($request->filled('status')) { $query->where('status', $request->status); }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        Gate::authorize('view', $user);

        $tickets = $user->ticketPurchases()
            ->with(['lottery', 'lotteryNumber'])
            ->latest()
            ->paginate(10);

        return view('admin.users.show', compact('user', 'tickets'));
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot delete an admin user from this section.');
        }

        AuditLog::record('user_deleted', User::class, $user->id, $user->toArray(), []);
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        Gate::authorize('update', $user);

        if ($user->isAdmin()) {
            return back()->with('error', __('users.cannot_modify_admin'));
        }

        $old = $user->status;
        $user->update(['status' => $user->status === 'active' ? 'suspended' : 'active']);

        AuditLog::record('user_status_changed', User::class, $user->id,
            ['status' => $old], ['status' => $user->status]);

        return back()->with('success', __('users.status_updated'));
    }

    // ── Admin user management (super-admin only) ──────────────────────────────
    public function adminIndex(Request $request)
    {
        Gate::authorize('manageAdmins', User::class);

        $query = User::whereHas('roles')->with('roles');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")
                                      ->orWhere('email', 'like', "%{$s}%"));
        }
        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }

        $admins = $query->latest()->paginate(20)->withQueryString();
        $roles  = Role::orderBy('name')->get();

        return view('admin.users.admins', compact('admins', 'roles'));
    }

    public function createAdmin()
    {
        Gate::authorize('manageAdmins', User::class);
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create-admin', compact('roles'));
    }

    public function storeAdmin(Request $request)
    {
        Gate::authorize('manageAdmins', User::class);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', Password::min(8)->letters()->numbers(), 'confirmed'],
            'role'     => ['required', 'string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'status'   => 'active',
        ]);
        $user->assignRole($data['role']);

        AuditLog::record('admin_user_created', User::class, $user->id, [],
            ['name' => $user->name, 'email' => $user->email, 'role' => $data['role']]);

        return redirect()->route('admin.users.admins')
            ->with('success', 'Admin user created successfully.');
    }

    public function editAdmin(User $user)
    {
        Gate::authorize('manageAdmins', User::class);
        $roles = Role::orderBy('name')->get();
        return view('admin.users.edit-admin', compact('user', 'roles'));
    }

    public function updateAdmin(Request $request, User $user)
    {
        Gate::authorize('manageAdmins', User::class);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', "unique:users,email,{$user->id}"],
            'password' => ['nullable', Password::min(8)->letters()->numbers(), 'confirmed'],
            'role'     => ['required', 'string', 'exists:roles,name'],
            'status'   => ['required', 'in:active,inactive,suspended'],
        ]);

        $old = $user->toArray();

        $updateData = [
            'name'   => $data['name'],
            'email'  => $data['email'],
            'status' => $data['status'],
        ];
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$data['role']]);

        AuditLog::record('admin_user_updated', User::class, $user->id, $old,
            ['name' => $user->name, 'email' => $user->email, 'role' => $data['role']]);

        return redirect()->route('admin.users.admins')
            ->with('success', 'Admin user updated successfully.');
    }

    public function destroyAdmin(User $user)
    {
        Gate::authorize('manageAdmins', User::class);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        AuditLog::record('admin_user_deleted', User::class, $user->id, $user->toArray(), []);
        $user->delete();

        return redirect()->route('admin.users.admins')
            ->with('success', 'Admin user deleted.');
    }
}
