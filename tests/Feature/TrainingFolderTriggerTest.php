<?php

namespace Tests\Feature;

use App\Models\Folder;
use App\Models\Training;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TrainingFolderTriggerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_training_creation_creates_root_and_standard_subfolders(): void
    {
        $user = User::create([
            'name' => 'Admin Uji',
            'username' => 'admin-uji',
            'role' => 'superadmin',
            'password' => 'password',
        ]);
        $this->actingAs($user);

        $training = Training::create([
            'nama_pelatihan' => 'Pelatihan Uji Trigger Folder',
            'bidang' => 'Bidang Teknis',
            'model' => 'standar',
            'metode' => 'klasikal',
            'lokasi' => 'Cimahi',
            'angkatan' => '1',
            'jumlah_peserta' => 1,
            'jp' => 10,
            'tgl_mulai' => '2026-01-10',
            'tgl_selesai' => '2026-01-12',
        ]);

        $root = Folder::where('training_id', $training->id)
            ->whereNull('parent_id')
            ->first();

        $this->assertNotNull($root);
        $this->assertSame($training->nama_pelatihan, $root->name);
        $this->assertSame($training->bidang, $root->bidang);
        $this->assertSame(
            ['DATA PESERTA', 'HASIL EVALUASI DAMPAK', 'JADWAL PELATIHAN'],
            Folder::where('parent_id', $root->id)->orderBy('name')->pluck('name')->all()
        );
    }
}
