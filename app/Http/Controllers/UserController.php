<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Helpers\LogHelper;

class UserController extends Controller
{
    // List bidang agar mudah dipanggil di mana-mana
    public static $listBidang = [
        'Bidang Sertifikasi Kompetensi & Pengelolaan Kelembagaan',
        'Bidang Pengembangan Kompetensi Teknis Inti',
        'Bidang Pengembangan Kompetensi Teknis Umum',
        'Bidang Pengembangan Kompetensi Manajerial'
    ];

    public function index(Request $request)
    {
        $search = $request->query('search');

        // Ambil data user dengan filter pencarian
        $users = User::latest()
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                    ->orWhere('username', 'LIKE', "%$search%")
                    ->orWhere('nip_nik', 'LIKE', "%$search%")
                    ->orWhere('bidang', 'LIKE', "%$search%")
                    ->orWhere('role', 'LIKE', "%$search%");
                });
            })
            ->get();

        $listBidang = self::$listBidang;

        return view('users.index', compact('users', 'listBidang', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'whatsapp' => 'required|numeric',
            'role' => 'required|in:superadmin,admin_bidang',
            'bidang' => ['required', Rule::in(self::$listBidang)],
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'whatsapp' => $request->whatsapp,
            'role' => $request->role,
            'bidang' => $request->bidang,
            'password' => Hash::make($request->password),
        ]);

        LogHelper::record('User', 'Menambahkan user baru (' . $user->name . ') sebagai ' . strtoupper($user->role));

        return redirect()->back()->with('success', 'User berhasil ditambahkan.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $nama = $user->name;
        $user->delete();

        LogHelper::record('User', 'Menghapus user: ' . $nama);

        return redirect()->back()->with('success', 'Akun berhasil dihapus.');
    }

    public function resetPassword(User $user)
    {
        $user->update(['password' => Hash::make('password123')]);

        LogHelper::record('User', 'Reset password untuk user: ' . $user->name);

        return redirect()->back()->with('success', "Password direset ke: password123");
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:superadmin,admin_bidang,participant',
            'whatsapp' => 'required|numeric',
            'bidang' => 'required',
        ]);

        $user->update([
            'name' => $request->name,
            'role' => $request->role,
            'whatsapp' => $request->whatsapp,
            'bidang' => $request->bidang,
        ]);

        LogHelper::record('User', 'Memperbarui profil user: ' . $user->name);

        return redirect()->back()->with('success', 'Data user ' . $user->name . ' berhasil diperbarui.');
    }
}