<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\NamedRange;

class ParticipantTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new MainTemplateSheet(),
            new DataSourceSheet(), 
        ];
    }
}

// --- SHEET 1: TEMPLATE UTAMA ---
class MainTemplateSheet implements FromArray, WithTitle, WithEvents
{
    public function title(): string { return 'Template Import'; }

    public function array(): array
    {
        return [
            ['nip_nik', 'nama_lengkap', 'nomor_hp', 'gender', 'status', 'jabatan', 'instansi', 'provinsi', 'kabupaten_kota'],
            ["'199503032024011001", "Contoh Nama", "0812345678", "Laki-Laki", "PNS", "Staff", "BPSDM", "JAWA BARAT", "KOTA BANDUNG"]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Terapkan dropdown menggunakan NAMED RANGE (Ditandai dengan tanda '=')
                $this->createDropdown($sheet, 'D', '=GENDER_LIST');
                $this->createDropdown($sheet, 'E', '=STATUS_LIST');
                $this->createDropdown($sheet, 'H', '=PROVINSI_LIST');

                // Proteksi NIP/NIK kolom A agar tetap text (Formatting)
                $sheet->getStyle('A2:A200')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            },
        ];
    }

    private function createDropdown($sheet, $col, $formula) {
        // Terapkan ke baris 2 sampai 100
        $range = $col . '2:' . $col . '100';
        $objValidation = $sheet->getDataValidation($range);
        $objValidation->setType(DataValidation::TYPE_LIST);
        $objValidation->setErrorStyle(DataValidation::STYLE_STOP);
        $objValidation->setAllowBlank(true);
        $objValidation->setShowInputMessage(true);
        $objValidation->setShowErrorMessage(true);
        $objValidation->setShowDropDown(true);
        $objValidation->setFormula1($formula);
    }
}

// --- SHEET 2: DATA SOURCE (LIST PROVINSI DLL) ---
class DataSourceSheet implements FromArray, WithTitle, WithEvents
{
    public function title(): string { return 'DataLists'; }

    public function array(): array
    {
        // Susun data dalam bentuk baris-baris
        $gender = ['Laki-Laki', 'Perempuan'];
        $status = ['PNS', 'PPPK', 'Non-ASN'];
        $provinces = ['ACEH', 'SUMATERA UTARA', 'SUMATERA BARAT', 'RIAU', 'JAMBI', 'SUMATERA SELATAN', 'BENGKULU', 'LAMPUNG', 'KEP. BANGKA BELITUNG', 'KEP. RIAU', 'DKI JAKARTA', 'JAWA BARAT', 'JAWA TENGAH', 'DI YOGYAKARTA', 'JAWA TIMUR', 'BANTEN', 'BALI', 'NUSA TENGGARA BARAT', 'NUSA TENGGARA TIMUR', 'KALIMANTAN BARAT', 'KALIMANTAN TENGAH', 'KALIMANTAN SELATAN', 'KALIMANTAN TIMUR', 'KALIMANTAN UTARA', 'SULAWESI UTARA', 'SULAWESI TENGAH', 'SULAWESI SELATAN', 'SULAWESI TENGGARA', 'GORONTALO', 'SULAWESI BARAT', 'MALUKU', 'MALUKU UTARA', 'PAPUA BARAT', 'PAPUA'];

        $rows = [];
        $max = max(count($gender), count($status), count($provinces));
        for ($i = 0; $i < $max; $i++) {
            $rows[] = [
                $gender[$i] ?? '',
                $status[$i] ?? '',
                $provinces[$i] ?? ''
            ];
        }
        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $workbook = $sheet->getParent();

                // DAFTARKAN NAMED RANGES (Kunci agar tidak corrupt)
                $workbook->addNamedRange(new NamedRange('GENDER_LIST', $sheet, '$A$1:$A$2'));
                $workbook->addNamedRange(new NamedRange('STATUS_LIST', $sheet, '$B$1:$B$3'));
                $workbook->addNamedRange(new NamedRange('PROVINSI_LIST', $sheet, '$C$1:$C$34'));

                // Sembunyikan sheet ini
                $sheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_VERYHIDDEN);
            },
        ];
    }
}