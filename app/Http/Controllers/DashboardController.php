<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\Participant;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Redirect peserta langsung ke portal dashboard peserta
        if ($user->role === 'participant') {
            return redirect()->route('participant.dashboard');
        }

        $currentYear = date('Y');
        $query = Training::query();

        // Isolasi data per bidang (jika bukan superadmin)
        if ($user->role !== 'superadmin') {
            $query->where('bidang', $user->bidang);
        }

        $trainingIds = $query->pluck('id');

        // 1. STATISTIK UTAMA PELATIHAN & PESERTA
        $totalTrainings = $query->count();
        $totalStandar = (clone $query)->where('model', 'standar')->count();
        $totalBlended = (clone $query)->where('model', 'blended')->count();
        $totalActive = (clone $query)->where('tgl_selesai', '>=', now()->toDateString())->count();
        $totalCompleted = (clone $query)->where('tgl_selesai', '<', now()->toDateString())->count();
        $totalParticipants = Participant::whereIn('training_id', $trainingIds)->count();
        $totalAdmins = User::where('role', 'admin_bidang')->count();

        // 2. TRACKING JP PESERTA TAHUN BERJALAN (Minimal 20 JP / Tahun)
        $participantsThisYear = Participant::whereIn('training_id', $trainingIds)
            ->whereHas('training', function($q) use ($currentYear) {
                $q->whereYear('tgl_mulai', $currentYear);
            })
            ->with('training')
            ->get();

        $jpPerNip = [];
        foreach ($participantsThisYear as $p) {
            $nip = $p->nip_nik;
            $jp = $p->training ? $p->training->jp : 0;
            if (!isset($jpPerNip[$nip])) {
                $jpPerNip[$nip] = 0;
            }
            $jpPerNip[$nip] += $jp;
        }

        $totalUniquePesertaYear = count($jpPerNip);
        $pesertaReached20Jp = 0;
        $pesertaBelow20Jp = 0;

        foreach ($jpPerNip as $totalJp) {
            if ($totalJp >= 20) {
                $pesertaReached20Jp++;
            } else {
                $pesertaBelow20Jp++;
            }
        }

        $jpComplianceRate = $totalUniquePesertaYear > 0 
            ? round(($pesertaReached20Jp / $totalUniquePesertaYear) * 100) 
            : 0;

        // 3. SEBARAN POHON WILAYAH (Provinsi -> Kabupaten/Kota)
        $topProvinces = Participant::whereIn('training_id', $trainingIds)
            ->whereNotNull('provinsi')
            ->where('provinsi', '!=', '')
            ->select('provinsi', DB::raw('count(*) as total'))
            ->groupBy('provinsi')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        $topCities = Participant::whereIn('training_id', $trainingIds)
            ->whereNotNull('kabupaten_kota')
            ->where('kabupaten_kota', '!=', '')
            ->select('kabupaten_kota', 'provinsi', DB::raw('count(*) as total'))
            ->groupBy('kabupaten_kota', 'provinsi')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // 4. PELATIHAN TERBARU DENGAN STATUS TOKEN & JADWAL SEBAR PASCA
        $latestTrainings = Training::withCount('participants')
            ->whereIn('id', $trainingIds)
            ->latest()
            ->take(6)
            ->get();

        // 5. AUDIT TRAIL / LOG AKTIVITAS TERBARU
        $recentLogs = ActivityLog::with('user')->latest()->take(6)->get();

        return view('dashboard.index', compact(
            'totalTrainings',
            'totalStandar',
            'totalBlended',
            'totalActive',
            'totalCompleted',
            'totalParticipants',
            'totalAdmins',
            'totalUniquePesertaYear',
            'pesertaReached20Jp',
            'pesertaBelow20Jp',
            'jpComplianceRate',
            'topProvinces',
            'topCities',
            'latestTrainings',
            'recentLogs'
        ));
    }
}