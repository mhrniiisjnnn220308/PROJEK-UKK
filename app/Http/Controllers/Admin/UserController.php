<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|max:45|unique:users,username',
            'password' => 'required|min:6',
            'nama' => 'required|max:45',
            'role' => 'required|in:admin,kasir,owner',
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama' => $request->nama,
            'role' => $request->role,
            'status' => 'aktif',
        ]);

        Log::create([
            'id_user' => Auth::id(),
            'activity' => "Menambahkan user baru: {$request->nama} ({$request->role})"
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|max:45|unique:users,username,' . $id,
            'nama' => 'required|max:45',
            'role' => 'required|in:admin,kasir,owner',
        ]);

        $user = User::findOrFail($id);
        
        $data = [
            'username' => $request->username,
            'nama' => $request->nama,
            'role' => $request->role,
        ];

        // Update password jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        Log::create([
            'id_user' => Auth::id(),
            'activity' => "Mengupdate user: {$request->nama}"
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User berhasil diupdate!');
    }

    public function toggleStatus($id)
    {
        if ($id == Auth::id()) {
            return redirect()->route('admin.users.index')
                             ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri!');
        }

        $user = User::findOrFail($id);
        $newStatus = $user->status == 'aktif' ? 'nonaktif' : 'aktif';
        $user->update(['status' => $newStatus]);

        Log::create([
            'id_user' => Auth::id(),
            'activity' => "Mengubah status user '{$user->nama}' menjadi {$newStatus}"
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Status user berhasil diubah!');
    }
}