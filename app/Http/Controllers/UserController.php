<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users with their roles.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::with('roles')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('users.index', compact('users', 'roles', 'search'));
    }

    /**
     * Update the role assigned to a user.
     */
    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $oldRole = $user->roles->first()?->name ?? 'No Role';
        $newRole = $request->input('role');

        $user->syncRoles([$newRole]);

        activity()
            ->useLog('user_management')
            ->performedOn($user)
            ->event('change_role')
            ->withProperties([
                'old_role' => $oldRole,
                'new_role' => $newRole,
            ])
            ->log("Role user '{$user->name}' diubah dari '{$oldRole}' ke '{$newRole}'");

        return redirect()->route('users.index')
            ->with('success', "Role user {$user->name} berhasil diubah menjadi {$request->input('role')}.");
    }

    /**
     * Toggle the active status of a user.
     */
    public function toggleStatus(User $user)
    {
        // Don't allow toggling own status
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $event = $user->is_active ? 'activate' : 'deactivate';

        activity()
            ->useLog('user_management')
            ->performedOn($user)
            ->event($event)
            ->withProperties([
                'is_active' => $user->is_active
            ])
            ->log("User '{$user->name}' telah {$statusStr}");

        return redirect()->route('users.index')
            ->with('success', "Status user {$user->name} berhasil {$statusStr}.");
    }
}
