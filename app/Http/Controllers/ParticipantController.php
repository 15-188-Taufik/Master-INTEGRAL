<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\LogHelper;

class ParticipantController extends Controller
{
    /**
     * Tampilan Dashboard khusus Peserta (Tracking Capaian 20 JP / Tahun)
     */
    public function index()
    {
        if (Auth::user()->role !== 'participant') {
            return redirect()->route('dashboard');
        }

        $user = Auth::user();
        $currentYear = date('Y');

        // 1. Total Pelatihan yang Diikuti
        $totalFollowed = Participant::where('nip_nik', $user->nip_nik)
            ->orWhere('user_id', $user->id)
            ->count();

        // 2. Capaian JP Tahun Berjalan (Target Minimal 20 JP per Tahun)
        $myJpThisYear = Participant::where(function($q) use ($user) {
                $q->where('nip_nik', $user->nip_nik)
                  ->orWhere('user_id', $user->id);
            })
            ->whereHas('training', function($q) use ($currentYear) {
                $q->whereYear('tgl_mulai', $currentYear);
            })
            ->with('training')
            ->get()
            ->sum(function($p) {
                return $p->training ? $p->training->jp : 0;
            });

        // 3. Riwayat Pelatihan Terbaru
        $recentTrainings = Participant::where(function($q) use ($user) {
                $q->where('nip_nik', $user->nip_nik)
                  ->orWhere('user_id', $user->id);
            })
            ->with('training')
            ->latest()
            ->take(5)
            ->get();

        return view('participant.dashboard', compact('user', 'totalFollowed', 'myJpThisYear', 'recentTrainings'));
    }

    /**
     * Menu: Daftar Pelatihan Tersedia
     */
    public function availableTrainings(Request $request)
    {
        $search = $request->query('search');

        $trainings = Training::withCount('participants')
            ->with('participants')
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nama_pelatihan', 'LIKE', "%$search%")
                      ->orWhere('bidang', 'LIKE', "%$search%")
                      ->orWhere('lokasi', 'LIKE', "%$search%");
                });
            })
            ->orderByRaw("tgl_selesai < '" . now()->toDateString() . "' ASC")
            ->orderBy('tgl_mulai', 'desc')
            ->get();

        return view('participant.available_trainings', compact('trainings', 'search'));
    }

    /**
     * Halaman Lengkapi Profil (Single Input Data: Pohon Wilayah Provinsi -> Kab/Kota)
     */
    public function completeProfile()
    {
        $user = Auth::user();
        return view('participant.complete_profile', compact('user'));
    }

    /**
     * Simpan Pelengkapan Profil
     */
    public function storeProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $request->validate([
            'nip_nik' => 'required|unique:users,nip_nik,' . $user->id,
            'gender' => 'required',
            'jabatan' => 'required',
            'instansi' => 'required',
            'provinsi' => 'required',
            'kabupaten_kota' => 'required',
            'status_kepegawaian' => 'required',
            'whatsapp' => 'required'
        ]);

        $user->update($request->all());

        LogHelper::record('Peserta', 'Melengkapi profil peserta (Single Input Data): ' . $user->name);

        return redirect()->route('participant.dashboard')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Pendaftaran Pelatihan via Kode Undangan (Token 6 Digit) & Smart Linking
     */
    public function enroll(Request $request, $id)
    {
        $training = Training::findOrFail($id);
        $user = Auth::user();

        // Validasi Token 6 Digit
        if (strtoupper(trim($request->invitation_code)) !== strtoupper(trim($training->invitation_code))) {
            return redirect()->back()->with('error', 'Kode Token Undangan salah! Silakan periksa kembali token 6 digit dari admin.');
        }

        // SMART LINKING: Hubungkan user_id dan salin profil ke record participant
        Participant::updateOrCreate(
            [
                'training_id' => $id,
                'nip_nik'     => $user->nip_nik,
            ],
            [
                'user_id'            => $user->id,
                'name'               => $user->name,
                'gender'             => $user->gender,
                'phone'              => $user->whatsapp,
                'jabatan'            => $user->jabatan,
                'instansi'           => $user->instansi,
                'provinsi'           => $user->provinsi,
                'kabupaten_kota'     => $user->kabupaten_kota,
                'status_kepegawaian' => $user->status_kepegawaian,
            ]
        );

        LogHelper::record('Peserta', 'Peserta ' . $user->name . ' berhasil join pelatihan via token: ' . $training->nama_pelatihan);

        return redirect()->route('participant.training.show', $id)
            ->with('success', 'Pendaftaran Berhasil! Akun Anda telah terhubung secara otomatis ke pelatihan ini.');
    }

    /**
     * Detail Pelatihan Peserta
     */
    public function showTrainingDetail($id)
    {
        $training = Training::with('stages')->findOrFail($id);
        $user = Auth::user();
        
        $participant = Participant::where('training_id', $id)
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('nip_nik', $user->nip_nik);
            })
            ->firstOrFail();

        return view('participant.training_detail', compact('training', 'participant', 'user'));
    }

    /**
     * Riwayat Pelatihan Peserta & Akumulasi JP
     */
    public function myHistory()
    {
        $user = Auth::user();

        $history = Participant::with('training')
            ->where(function($q) use ($user) {
                $q->where('nip_nik', $user->nip_nik)
                  ->orWhere('user_id', $user->id);
            })
            ->latest()
            ->get();

        $totalJpLifetime = $history->sum(function($p) {
            return $p->training ? $p->training->jp : 0;
        });

        return view('participant.history', compact('history', 'totalJpLifetime'));
    }
}