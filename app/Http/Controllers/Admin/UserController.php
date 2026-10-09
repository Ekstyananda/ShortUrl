<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::withCount('shortLinks')->orderBy('name')->paginate(25);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create', ['user' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_MEMBER])],
            'password' => ['required', 'confirmed', Password::min(10)],
        ]);

        $user = User::create($data + ['is_active' => true]);

        AuditLogger::log('user.created', $user, ['email' => $user->email, 'role' => $user->role]);

        return redirect()->route('admin.users.index')->with('status', "Akun {$user->email} dibuat.");
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_MEMBER])],
            'password' => ['nullable', 'confirmed', Password::min(10)],
        ]);

        if ($user->is($request->user()) && $data['role'] !== User::ROLE_ADMIN) {
            return back()->withErrors(['role' => 'Anda tidak dapat menurunkan peran akun sendiri.'])->withInput();
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->fill($data);
        $changes = array_keys($user->getDirty());
        $user->save();

        if ($changes) {
            // Jangan pernah mencatat nilai password, cukup bahwa ia berubah.
            AuditLogger::log('user.updated', $user, ['changed' => $changes]);
        }

        return redirect()->route('admin.users.index')->with('status', "Akun {$user->email} diperbarui.");
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'Anda tidak dapat menonaktifkan akun sendiri.']);
        }

        $user->update(['is_active' => ! $user->is_active]);

        AuditLogger::log($user->is_active ? 'user.activated' : 'user.deactivated', $user, ['email' => $user->email]);

        return back()->with('status', $user->is_active ? "Akun {$user->email} diaktifkan." : "Akun {$user->email} dinonaktifkan.");
    }
}
