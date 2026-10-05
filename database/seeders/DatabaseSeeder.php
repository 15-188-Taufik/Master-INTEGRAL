<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seeder Akun Superadmin Default
        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'role' => 'superadmin',
                'whatsapp' => '081234567890',
                'bidang' => null,
                'password' => 'password',
            ]
        );

        // 2. Seeder Akun Admin Bidang
        User::firstOrCreate(
            ['username' => 'admin_teknis'],
            [
                'name' => 'Admin Bidang Teknis',
                'role' => 'admin_bidang',
                'whatsapp' => '081234567891',
                'bidang' => 'Bidang Pengembangan Kompetensi Teknis Inti',
                'password' => 'password123',
            ]
        );

        // 3. Seeder Akun Peserta Demo
        User::firstOrCreate(
            ['username' => 'peserta'],
            [
                'name' => 'Peserta Demo',
                'role' => 'participant',
                'nip_nik' => '199503032024011001',
                'whatsapp' => '081234567892',
                'gender' => 'Laki-Laki',
                'jabatan' => 'Analis SDM',
                'instansi' => 'BPSDM Jawa Barat',
                'provinsi' => 'JAWA BARAT',
                'kabupaten_kota' => 'KOTA BANDUNG',
                'status_kepegawaian' => 'PNS',
                'password' => 'password123',
            ]
        );
    }
}
