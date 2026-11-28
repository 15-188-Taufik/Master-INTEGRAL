<?php

namespace App\Imports;

use App\Models\Participant;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ParticipantImport implements ToModel, WithHeadingRow
{
    private $training_id;

    public function __construct($training_id)
    {
        $this->training_id = $training_id;
    }

    public function model(array $row)
    {
        if (!isset($row['nip_nik'])) {
            return null;
        }

        $nip = trim(ltrim((string) $row['nip_nik'], "'"));

        // Cek duplikat di pelatihan yang sama
        $exists = Participant::where('training_id', $this->training_id)
            ->where('nip_nik', $nip)
            ->exists();
        if ($exists) {
            return null;
        }

        // Smart Linking: tautkan otomatis ke user_id berdasarkan NIP
        $user = User::where('nip_nik', $nip)->first();

        return new Participant([
            'training_id'        => $this->training_id,
            'user_id'            => $user ? $user->id : null,
            'nip_nik'            => $nip,
            'name'               => $row['nama_lengkap'] ?? ($user?->name),
            'gender'             => $row['gender'] ?? ($user?->gender),
            'phone'              => $row['nomor_hp'] ?? ($user?->whatsapp),
            'jabatan'            => $row['jabatan'] ?? ($user?->jabatan),
            'instansi'           => $row['instansi'] ?? ($user?->instansi),
            'provinsi'           => $row['provinsi'] ?? ($user?->provinsi),
            'kabupaten_kota'     => $row['kabupaten_kota'] ?? ($user?->kabupaten_kota),
            'kecamatan'          => $row['kecamatan'] ?? ($user?->kecamatan),
            'kelurahan'          => $row['kelurahan'] ?? ($user?->kelurahan),
            'status_kepegawaian' => $row['status'] ?? ($user?->status_kepegawaian),
        ]);
    }
}