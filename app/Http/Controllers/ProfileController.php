<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $request->validate([
            'name' => 'required|string|max:255',
            'whatsapp' => 'required|numeric',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            // Hapus foto lama jika ada
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('avatars', 'public');
            $user->profile_photo = $path;
        }

        $user->name = $request->name;
        $user->whatsapp = $request->whatsapp;
        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        // Hanya validasi password baru dan konfirmasinya
        $request->validate([
            'new_password' => 'required|min:8|confirmed',
        ], [
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.'
        ]);

        $user = \App\Models\User::findOrFail(Auth::id());

        // Langsung update tanpa cek password lama
        $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        $user->save();

        // Catat log aktivitas jika diperlukan
        \App\Helpers\LogHelper::record('User', 'Mengubah password profil mandiri');

        return redirect()->back()->with('success', 'Password berhasil diperbarui.');
    }
}