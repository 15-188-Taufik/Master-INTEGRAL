<?php

namespace App\Imports;

use App\Models\Participant;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParticipantImport implements ToModel, WithHeadingRow
{
    private $training_id;

    public function __construct($training_id) {
        $this->training_id = $training_id;
    }

    public function model(array $row)
    {
        // Validasi baris kosong
        if (!isset($row['nip_nik'])) return null;

        $nip = ltrim($row['nip_nik'], "'");
        
        // Cek Double di pelatihan yang sama
        $exists = Participant::where('training_id', $this->training_id)
            ->where('nip_nik', $nip)
            ->exists();
        if ($exists) return null;

        // Auto-sync User ID jika NIP sudah ada di tabel users
        $user = User::where('nip_nik', $nip)->first();

        return new Participant([
            'training_id'        => $this->training_id,
            'user_id'            => $user ? $user->id : null,
            'nip_nik'            => $nip,
            'name'               => $row['nama_lengkap'],
            'gender'             => $row['gender'],
            'phone'              => $row['nomor_hp'], // Kolom baru
            'jabatan'            => $row['jabatan'],
            'instansi'           => $row['instansi'],
            'provinsi'           => $row['provinsi'],
            'kabupaten_kota'     => $row['kabupaten_kota'],
            'status_kepegawaian' => $row['status'],
        ]);
    }
}