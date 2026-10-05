<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str; 
use App\Models\User;
use Laravel\Socialite\Facades\Socialite; 
use Illuminate\Support\Facades\Auth; // Penting untuk Auth::login
use Illuminate\Http\Request;

class LoginController extends Controller implements HasMiddleware
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | Controller ini menangani otentikasi pengguna untuk aplikasi dan
    | mengarahkan mereka ke layar beranda. Controller ini menggunakan trait
    | untuk menyediakan fungsionalitasnya secara efisien.
    |
    */

    use AuthenticatesUsers;

    /**
     * Tentukan field username yang digunakan untuk login (Default: email).
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Izinkan login menggunakan Username ataupun NIP/NIK
     */
    protected function credentials(Request $request)
    {
        $login = $request->input($this->username());

        $user = User::where('username', $login)
                    ->orWhere('nip_nik', $login)
                    ->first();

        if ($user) {
            return [
                'id' => $user->id,
                'password' => $request->input('password'),
            ];
        }

        return $request->only($this->username(), 'password');
    }

    /**
     * Redirect setelah login berhasil
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->role === 'participant') {
            return redirect()->route('participant.dashboard');
        }
        return redirect()->intended($this->redirectTo);
    }

    /**
     * Ke mana pengguna diarahkan setelah login.
     */
    protected $redirectTo = '/dashboard';

    /**
     * Pengaturan Middleware untuk Controller ini (Laravel 11/12 Style).
     */
    public static function middleware(): array
    {
        return [
            new Middleware('guest', except: ['logout']),
        ];
    }

    /**
     * Redirect ke Google OAuth.
     */
    public function redirectToGoogle() {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Callback dari Google.
     */
    public function handleGoogleCallback() {
        try {
            $user = Socialite::driver('google')->user();
            
            // Cari user berdasarkan google_id atau email (username)
            $finduser = User::where('google_id', $user->id)
                            ->orWhere('username', $user->email)
                            ->first();

            if($finduser){
                // Jika user ditemukan, langsung login
                Auth::login($finduser);
                
                // Jika sudah login tapi google_id masih kosong (user lama login google pertama kali)
                if(empty($finduser->google_id)){
                    $finduser->update([
                        'google_id' => $user->id,
                        'avatar' => $user->avatar
                    ]);
                }
            } else {
                // Jika user baru, buat akun dengan role participant
                $newUser = User::create([
                    'name' => $user->name,
                    'username' => $user->email,
                    'google_id'=> $user->id,
                    'avatar' => $user->avatar,
                    'role' => 'participant',
                    'bidang'    => null,
                    'password' => bcrypt(Str::random(16))
                ]);
                Auth::login($newUser);
            }

            // LOGIKA PROBIS: Cek jika NIP atau Gender masih kosong
            // Jika kosong, wajib ke halaman lengkapi profil sebelum ke dashboard
            if (empty(Auth::user()->nip_nik) || empty(Auth::user()->gender)) {
                return redirect()->route('participant.profile.complete');
            }

            return redirect()->intended($this->redirectTo);

        } catch (\Exception $e) {
            // Log error untuk debug jika diperlukan: \Log::error($e->getMessage());
            //dd($e->getMessage()); 
            return redirect('/login')->with('error', 'Gagal login via Google. Silakan coba lagi.');
        }
    }

    protected function loggedOut(Request $request)
    {
        // Arahkan ke rute landing page (/)
        return redirect('/');
    }
}