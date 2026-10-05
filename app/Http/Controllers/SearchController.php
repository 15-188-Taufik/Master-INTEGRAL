<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $user = Auth::user();

        // Jika input kosong, balikkan ke halaman sebelumnya
        if (!$query) {
            return redirect()->back();
        }

        // 1. Pencarian Pelatihan (Isolasi Bidang jika bukan superadmin)
        $trainings = Training::query()
            ->when($user->role !== 'superadmin', function($q) use ($user) {
                return $q->where('bidang', $user->bidang);
            })
            ->where(function($q) use ($query) {
                $q->where('nama_pelatihan', 'LIKE', "%$query%")
                  ->orWhere('lokasi', 'LIKE', "%$query%")
                  ->orWhere('invitation_code', 'LIKE', "%$query%");
            })
            ->take(10)->get();

        // 2. Pencarian Peserta (Isolasi Bidang jika bukan superadmin)
        $participants = Participant::query()
            ->whereHas('training', function($q) use ($user) {
                if ($user->role !== 'superadmin') {
                    $q->where('bidang', $user->bidang);
                }
            })
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%$query%")
                  ->orWhere('nip_nik', 'LIKE', "%$query%")
                  ->orWhere('instansi', 'LIKE', "%$query%")
                  ->orWhere('provinsi', 'LIKE', "%$query%")
                  ->orWhere('kabupaten_kota', 'LIKE', "%$query%");
            })
            ->take(15)->get();

        return view('search.results', compact('trainings', 'participants', 'query'));
    }
}