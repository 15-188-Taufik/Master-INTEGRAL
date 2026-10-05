<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\Participant;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $currentYear = date('Y');

        // 1. Data Pelatihan yang Sedang Berjalan
        $trainingsToday = Training::withCount('participants')
            ->where('tgl_mulai', '<=', $today)
            ->where('tgl_selesai', '>=', $today)
            ->get();

        // 2. Kalkulasi Tracking JP Tahunan untuk Metrik Landing Page
        $participantsThisYear = Participant::whereHas('training', function($q) use ($currentYear) {
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

        $totalUnique = count($jpPerNip);
        $reached20 = 0;
        foreach ($jpPerNip as $totalJp) {
            if ($totalJp >= 20) {
                $reached20++;
            }
        }
        $jpComplianceRate = $totalUnique > 0 ? round(($reached20 / $totalUnique) * 100) : 100;

        // 3. Data Statistik Modul 1
        $stats = [
            'total_training'     => Training::count(),
            'total_participants' => Participant::count(),
            'total_blended'      => Training::where('model', 'blended')->count(),
            'jp_compliance_rate' => $jpComplianceRate,
        ];

        return view('welcome', compact('trainingsToday', 'stats'));
    }
}