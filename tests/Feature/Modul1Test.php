<?php

namespace Tests\Feature;

use App\Models\HelpdeskTicket;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class Modul1Test extends TestCase
{
    use DatabaseTransactions;

    public function test_peserta_ditolak_mengakses_halaman_admin_trainings(): void
    {
        $peserta = User::create([
            'name' => 'Peserta Uji RBAC',
            'username' => 'peserta_rbac_' . uniqid(),
            'role' => 'participant',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($peserta)->get(route('trainings.index'));
        $response->assertStatus(403);
    }

    public function test_admin_bidang_ditolak_mengakses_pelatihan_bidang_lain(): void
    {
        $adminPkti = User::create([
            'name' => 'Admin PKTI Uji',
            'username' => 'admin_pkti_' . uniqid(),
            'role' => 'admin_bidang',
            'bidang' => 'Bidang Pengembangan Kompetensi Teknis Inti',
            'password' => Hash::make('password123'),
        ]);

        $trainingSkpk = Training::create([
            'nama_pelatihan' => 'Pelatihan Uji Isolasi SKPK',
            'bidang' => 'Bidang Sertifikasi Kompetensi & Pengelolaan Kelembagaan',
            'model' => 'standar',
            'metode' => 'Full Learning',
            'lokasi' => 'Zoom',
            'angkatan' => '1',
            'jumlah_peserta' => 10,
            'jp' => 20,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2026-11-05',
        ]);

        $response = $this->actingAs($adminPkti)->get(route('trainings.manage', $trainingSkpk->id));
        $response->assertStatus(403);
    }

    public function test_trigger_1_token_6_digit_unik_dan_trigger_3_jadwal_evaluasi(): void
    {
        $teknis = Training::create([
            'nama_pelatihan' => 'Pelatihan Teknis Uji',
            'bidang' => 'Bidang Pengembangan Kompetensi Teknis Inti',
            'model' => 'standar',
            'metode' => 'Full Learning',
            'lokasi' => 'Zoom',
            'angkatan' => '1',
            'jumlah_peserta' => 10,
            'jp' => 20,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2026-11-10',
        ]);

        $manajerial = Training::create([
            'nama_pelatihan' => 'Pelatihan Manajerial Uji',
            'bidang' => 'Bidang Pengembangan Kompetensi Manajerial',
            'model' => 'blended',
            'metode' => 'blended',
            'lokasi' => 'Cimahi',
            'angkatan' => '1',
            'jumlah_peserta' => 10,
            'jp' => 20,
            'tgl_mulai' => '2026-11-01',
            'tgl_selesai' => '2026-11-10',
        ]);

        // Trigger 1 & NF-01: Token 6 karakter & unik
        $this->assertEquals(6, strlen($teknis->invitation_code));
        $this->assertEquals(6, strlen($manajerial->invitation_code));
        $this->assertNotEquals($teknis->invitation_code, $manajerial->invitation_code);

        // Trigger 3 (F-10): +4 bulan untuk Teknis, +1 tahun untuk Manajerial
        $this->assertEquals('2027-03-10', $teknis->tgl_sebar_l34->format('Y-m-d'));
        $this->assertEquals('2027-11-10', $manajerial->tgl_sebar_l34->format('Y-m-d'));
    }

    public function test_helpdesk_kendala_login_dan_tindak_lanjut_superadmin(): void
    {
        // Pengguna publik kirim tiket dari halaman login (F-07)
        $response = $this->post(route('helpdesk.store'), [
            'nama' => 'Peserta Uji Helpdesk',
            'nip_nik' => '199901012026011001',
            'email' => 'uji.helpdesk@example.com',
            'kategori_kendala' => 'Gagal Login Google OAuth',
            'deskripsi' => 'Tidak bisa masuk menggunakan akun Google dinas.',
        ]);

        $response->assertRedirect(route('helpdesk.create'));

        $ticket = HelpdeskTicket::where('email', 'uji.helpdesk@example.com')->first();
        $this->assertNotNull($ticket);
        $this->assertEquals('open', $ticket->status);

        // Superadmin tindak lanjuti tiket
        $superadmin = User::create([
            'name' => 'Superadmin Uji',
            'username' => 'superadmin_uji_' . uniqid(),
            'role' => 'superadmin',
            'password' => Hash::make('password123'),
        ]);

        $updateResponse = $this->actingAs($superadmin)
            ->put(route('helpdesk.update-status', $ticket), [
                'status' => 'selesai',
                'catatan_admin' => 'Akun sudah ditautkan ulang.',
            ]);

        $updateResponse->assertRedirect();
        $this->assertEquals('selesai', $ticket->fresh()->status);
    }
}