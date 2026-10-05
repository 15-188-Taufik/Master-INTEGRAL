<?php

namespace App\Exports;

use App\Models\Participant;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ParticipantExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $trainingId;

    public function __construct($trainingId) {
        $this->trainingId = $trainingId;
    }

    public function headings(): array {
        return ['NIP/NIK', 'NAMA LENGKAP', 'NOMOR WA', 'GENDER', 'STATUS', 'JABATAN', 'INSTANSI', 'PROVINSI', 'KABUPATEN/KOTA'];
    }

    public function array(): array {
        return Participant::where('training_id', $this->trainingId)
            ->get()
            ->map(function($p) {
                return [
                    "'" . $p->nip_nik, // Prefix agar NIP tidak jadi angka saintifik
                    strtoupper($p->name),
                    $p->phone,
                    $p->gender,
                    $p->status_kepegawaian,
                    $p->jabatan,
                    $p->instansi,
                    $p->provinsi,
                    $p->kabupaten_kota
                ];
            })->toArray();
    }
}
