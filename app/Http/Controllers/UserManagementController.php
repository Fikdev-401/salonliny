<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('role')
            ->whereHas('role', function ($query) {
                $query->where('slug', '!=', 'super-admin');
            })
            ->orderBy('nama')
            ->get();
        
        return view('users.index', compact('users'));
    }

    public function create()
    {
        // Hanya boleh membuat admin, pimpinan, kasir
        $roles = Role::whereIn('slug', ['admin', 'pimpinan', 'kasir'])
            ->orderBy('nama')
            ->get();
        
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'string', Password::min(8)->letters()->numbers()],
            'role_id' => 'required|exists:roles,id',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Validate role is not super-admin
        $role = Role::find($validated['role_id']);
        if ($role->slug === 'super-admin') {
            return back()->with('error', 'Tidak dapat membuat akun Super Admin.')->withInput();
        }

        $validated['password'] = Hash::make($validated['password']);
        
        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        // Tidak boleh edit super admin
        if ($user->isSuperAdmin()) {
            return redirect()->route('users.index')->with('error', 'Tidak dapat mengedit akun Super Admin.');
        }

        $roles = Role::whereIn('slug', ['admin', 'pimpinan', 'kasir'])
            ->orderBy('nama')
            ->get();
        
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        // Tidak boleh update super admin
        if ($user->isSuperAdmin()) {
            return redirect()->route('users.index')->with('error', 'Tidak dapat mengupdate akun Super Admin.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', Password::min(8)->letters()->numbers()],
            'role_id' => 'required|exists:roles,id',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        // Validate role is not super-admin
        $role = Role::find($validated['role_id']);
        if ($role->slug === 'super-admin') {
            return back()->with('error', 'Tidak dapat mengubah role menjadi Super Admin.')->withInput();
        }

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        // Tidak boleh hapus super admin
        if ($user->isSuperAdmin()) {
            return redirect()->route('users.index')->with('error', 'Tidak dapat menghapus akun Super Admin.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }

    public function resetPassword(User $user)
    {
        // Tidak boleh reset password super admin
        if ($user->isSuperAdmin()) {
            return redirect()->route('users.index')->with('error', 'Tidak dapat mereset password akun Super Admin.');
        }

        $temporaryPassword = Str::password(12);

        $user->update([
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
        ]);

        return redirect()->route('users.index')
            ->with('success', "Password sementara untuk {$user->username}: {$temporaryPassword}. User wajib mengganti password saat login.");
    }
}
