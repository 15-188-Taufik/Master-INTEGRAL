<?php

namespace App\Http\Controllers;

use App\Models\Training;  
use App\Models\Participant; 
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel; 
use App\Imports\ParticipantImport;
use App\Exports\ParticipantTemplateExport;
use App\Exports\ParticipantExport;
use Illuminate\Support\Facades\Auth; 
use App\Helpers\LogHelper;

class TrainingController extends Controller
{
    public function index()
    {
        $query = Training::withCount('participants');
        
        // Isolasi data per bidang (jika bukan superadmin)
        if (Auth::user()->role !== 'superadmin') {
            $query->where('bidang', Auth::user()->bidang);
        }

        $trainings = $query->latest()->get();
        return view('trainings.index', compact('trainings'));
    }

    public function create(Request $request)
    {
        $model = $request->query('model', 'standar'); // standar atau blended
        return view('trainings.create', compact('model'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nama_pelatihan' => 'required|string|max:255',
            'bidang' => 'required',
            'model' => 'required|in:standar,blended',
            'lokasi' => 'required|string|max:255',
            'angkatan' => 'required|string|max:50',
            'jumlah_peserta' => 'required|numeric',
            'jp' => 'required|numeric',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date',
        ];
        if ($request->model === 'standar') { 
            $rules['metode'] = 'required'; 
        }
        $data = $request->validate($rules);

        if ($request->model === 'blended') { 
            $data['metode'] = 'blended'; 
        }

        // Simpan Data Pelatihan (Token 6 digit otomatis dibuat via Model boot)
        $training = Training::create($data);

        // Simpan Tahapan jika Pelatihan Blended
        if ($request->model === 'blended' && $request->has('stages')) {
            foreach ($request->stages as $stage) {
                $training->stages()->create([
                    'nama_tahapan' => $stage['nama'],
                    'metode' => $stage['metode'],
                    'tgl_mulai' => $stage['mulai'],
                    'tgl_selesai' => $stage['selesai'],
                ]);
            }
        }

        LogHelper::record('Pelatihan', 'Membuat pelatihan baru: ' . $training->nama_pelatihan . ' (Token: ' . $training->invitation_code . ')');

        return redirect()->route('trainings.index')->with('success', 'Pelatihan berhasil dibuat dengan Kode Token: ' . $training->invitation_code);
    }

    public function edit($id)
    {
        $training = Training::with('stages')->findOrFail($id);
        
        // Isolasi akses per bidang
        if (Auth::user()->role !== 'superadmin' && $training->bidang !== Auth::user()->bidang) {
            abort(403, 'Anda tidak memiliki akses ke pelatihan bidang ini.');
        }

        $model = $training->model;
        return view('trainings.edit', compact('training', 'model'));
    }

    public function update(Request $request, $id)
    {
        $training = Training::findOrFail($id);

        if (Auth::user()->role !== 'superadmin' && $training->bidang !== Auth::user()->bidang) {
            abort(403, 'Anda tidak memiliki akses ke pelatihan bidang ini.');
        }

        $rules = [
            'nama_pelatihan' => 'required|string|max:255',
            'bidang' => 'required',
            'lokasi' => 'required|string|max:255',
            'angkatan' => 'required|string|max:50',
            'jumlah_peserta' => 'required|numeric',
            'jp' => 'required|numeric',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date',
        ];

        if ($training->model === 'standar') {
            $rules['metode'] = 'required';
        }

        $data = $request->validate($rules);
        $training->update($data);

        // Jika model blended, perbarui tahapan
        if ($training->model === 'blended' && $request->has('stages')) {
            $training->stages()->delete();
            foreach ($request->stages as $stage) {
                $training->stages()->create([
                    'nama_tahapan' => $stage['nama'],
                    'metode' => $stage['metode'],
                    'tgl_mulai' => $stage['mulai'],
                    'tgl_selesai' => $stage['selesai'],
                ]);
            }
        }

        LogHelper::record('Pelatihan', 'Memperbarui data pelatihan: ' . $training->nama_pelatihan);

        return redirect()->route('trainings.index')->with('success', 'Data pelatihan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $training = Training::findOrFail($id);

        if (Auth::user()->role !== 'superadmin' && $training->bidang !== Auth::user()->bidang) {
            abort(403, 'Anda tidak memiliki akses ke pelatihan bidang ini.');
        }

        $nama = $training->nama_pelatihan;
        $training->delete();

        LogHelper::record('Pelatihan', 'Menghapus pelatihan: ' . $nama);

        return redirect()->route('trainings.index')->with('success', 'Pelatihan berhasil dihapus dari sistem.');
    }

    public function manage($id)
    {
        $training = Training::withCount('participants')->with('stages')->findOrFail($id);
        
        if (Auth::user()->role !== 'superadmin' && $training->bidang !== Auth::user()->bidang) {
            abort(403, 'Anda tidak memiliki akses ke pelatihan bidang ini.');
        }

        return view('trainings.manage', compact('training'));
    }

    public function generateNewCode($id)
    {
        $training = Training::findOrFail($id);

        if (Auth::user()->role !== 'superadmin' && $training->bidang !== Auth::user()->bidang) {
            abort(403, 'Anda tidak memiliki akses ke pelatihan bidang ini.');
        }

        $newCode = strtoupper(\Illuminate\Support\Str::random(6));
        $training->update(['invitation_code' => $newCode]);

        LogHelper::record('Pelatihan', 'Generate token baru untuk ' . $training->nama_pelatihan . ': ' . $newCode);

        return redirect()->back()->with('success', 'Kode Undangan berhasil diperbarui: ' . $newCode);
    }

    public function setLmsLink(Request $request, $id)
    {
        $request->validate([
            'link_lms' => 'required|url'
        ], [
            'link_lms.url' => 'Format tautan tidak valid (harus diawali http:// atau https://)'
        ]);

        $training = Training::findOrFail($id);

        if (Auth::user()->role !== 'superadmin' && $training->bidang !== Auth::user()->bidang) {
            abort(403, 'Anda tidak memiliki akses ke pelatihan bidang ini.');
        }

        $training->update(['link_lms' => $request->link_lms]);

        return redirect()->back()->with('success', 'Tautan ruang belajar digital (LMS) berhasil diperbarui.');
    }

    // --- MANAJEMEN PESERTA PELATIHAN (SINGLE INPUT DATA & SMART LINKING) ---

    public function showParticipants(Request $request, $id)
    {
        $training = Training::findOrFail($id);

        if (Auth::user()->role !== 'superadmin' && $training->bidang !== Auth::user()->bidang) {
            abort(403, 'Anda tidak memiliki akses ke pelatihan bidang ini.');
        }

        $search = $request->query('search');

        $participants = Participant::where('training_id', $id)
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                      ->orWhere('nip_nik', 'LIKE', "%$search%")
                      ->orWhere('instansi', 'LIKE', "%$search%")
                      ->orWhere('kabupaten_kota', 'LIKE', "%$search%");
                });
            })
            ->latest()
            ->get();
        
        return view('trainings.participants', compact('training', 'participants', 'search'));
    }

    public function storeParticipant(Request $request, $id)
    {
        $request->validate([
            'nip_nik' => 'required|string|unique:participants,nip_nik,NULL,id,training_id,' . $id,
            'name' => 'required|string|max:255',
            'gender' => 'required',
            'phone' => 'required|string|max:20',
            'jabatan' => 'required',
            'instansi' => 'required',
            'provinsi' => 'required',
            'kabupaten_kota' => 'required',
            'status_kepegawaian' => 'required',
        ]);

        $nip = ltrim($request->nip_nik, "'");
        // Smart linking: hubungkan user_id jika user sudah terdaftar di sistem
        $user = User::where('nip_nik', $nip)->first();

        Participant::create([
            'training_id'        => $id,
            'user_id'            => $user ? $user->id : null,
            'nip_nik'            => $nip,
            'name'               => $request->name,
            'gender'             => $request->gender,
            'phone'              => $request->phone,
            'jabatan'            => $request->jabatan,
            'instansi'           => $request->instansi,
            'provinsi'           => $request->provinsi,
            'kabupaten_kota'     => $request->kabupaten_kota,
            'status_kepegawaian' => $request->status_kepegawaian,
        ]);

        LogHelper::record('Peserta', 'Menambahkan peserta manual (' . $request->name . ') ke pelatihan ID: ' . $id);

        return redirect()->back()->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function updateParticipant(Request $request, $id)
    {
        $participant = Participant::findOrFail($id);

        $request->validate([
            'nip_nik' => 'required|string|unique:participants,nip_nik,' . $id . ',id,training_id,' . $participant->training_id,
            'name' => 'required|string|max:255',
            'gender' => 'required',
            'phone' => 'required|string|max:20',
            'jabatan' => 'required',
            'instansi' => 'required',
            'provinsi' => 'required',
            'kabupaten_kota' => 'required',
            'status_kepegawaian' => 'required',
        ]);

        $nip = ltrim($request->nip_nik, "'");
        $user = User::where('nip_nik', $nip)->first();

        $participant->update([
            'user_id'            => $user ? $user->id : $participant->user_id,
            'nip_nik'            => $nip,
            'name'               => $request->name,
            'gender'             => $request->gender,
            'phone'              => $request->phone,
            'jabatan'            => $request->jabatan,
            'instansi'           => $request->instansi,
            'provinsi'           => $request->provinsi,
            'kabupaten_kota'     => $request->kabupaten_kota,
            'status_kepegawaian' => $request->status_kepegawaian,
        ]);

        LogHelper::record('Peserta', 'Memperbarui data peserta: ' . $participant->name);

        return redirect()->back()->with('success', 'Data peserta berhasil diperbarui.');
    }

    public function destroyParticipant($id)
    {
        $participant = Participant::findOrFail($id);
        $name = $participant->name;
        $participant->delete();

        LogHelper::record('Peserta', 'Menghapus peserta: ' . $name);

        return redirect()->back()->with('success', 'Peserta berhasil dihapus.');
    }

    public function importParticipants(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        Excel::import(new ParticipantImport($id), $request->file('file'));

        LogHelper::record('Peserta', 'Import data peserta Excel ke pelatihan ID: ' . $id);

        return redirect()->back()->with('success', 'Data peserta berhasil diimport dengan smart-linking.');
    }

    public function downloadTemplate()
    {
        return Excel::download(
            new ParticipantTemplateExport(), 
            'template_peserta_integral.xlsx'
        );
    }

    public function exportParticipants($id)
    {
        $training = Training::findOrFail($id);
        $fileName = 'DATA_PESERTA_' . str_replace(' ', '_', $training->nama_pelatihan) . '.xlsx';

        return Excel::download(new ParticipantExport($id), $fileName);
    }
}
