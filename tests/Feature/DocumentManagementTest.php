<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_superadmin_can_view_global_folders_and_bidang_list(): void
    {
        $superadmin = User::create([
            'name' => 'Superadmin Test',
            'username' => 'superadmin_doc_test',
            'role' => 'superadmin',
            'password' => bcrypt('password'),
        ]);

        Folder::create([
            'name' => 'Pedoman Umum',
            'bidang' => 'Semua Bidang',
            'user_id' => $superadmin->id,
        ]);

        $response = $this->actingAs($superadmin)->get(route('documents.index'));

        $response->assertOk();
        $response->assertSee('Pedoman Umum');
        $response->assertSee('Folder Global');
    }

    public function test_admin_bidang_can_create_nested_folder_and_upload_file_but_isolated_from_other_bidang(): void
    {
        Storage::fake('public');

        $adminTeknis = User::create([
            'name' => 'Admin Teknis',
            'username' => 'admin_teknis_doc_test',
            'role' => 'admin_bidang',
            'bidang' => 'Bidang Teknis',
            'password' => bcrypt('password'),
        ]);

        $otherFolder = Folder::create([
            'name' => 'Rahasia Manajerial',
            'bidang' => 'Bidang Manajerial',
            'user_id' => $adminTeknis->id,
        ]);

        // Isolasi bidang: Admin Teknis dilarang buka folder Bidang Manajerial
        $this->actingAs($adminTeknis)
            ->get(route('documents.index', ['folder' => $otherFolder->id]))
            ->assertForbidden();

        // Buat folder root di Bidang Teknis
        $this->actingAs($adminTeknis)
            ->post(route('documents.folder.create'), [
                'name' => 'Arsip Teknis',
                'bidang' => 'Bidang Teknis',
            ])
            ->assertRedirect();

        $rootFolder = Folder::where('name', 'Arsip Teknis')->firstOrFail();

        // Buat nested subfolder di dalam Arsip Teknis
        $this->actingAs($adminTeknis)
            ->post(route('documents.folder.create'), [
                'name' => 'Subfolder Surat',
                'parent_id' => $rootFolder->id,
                'bidang' => 'Bidang Teknis',
            ])
            ->assertRedirect();

        $subFolder = Folder::where('name', 'Subfolder Surat')
            ->where('parent_id', $rootFolder->id)
            ->firstOrFail();

        // Upload file ke dalam nested subfolder
        $uploaded = UploadedFile::fake()->create('surat-tugas.pdf', 120, 'application/pdf');
        $this->actingAs($adminTeknis)
            ->post(route('documents.upload'), [
                'folder_id' => $subFolder->id,
                'attachments' => [$uploaded],
            ])
            ->assertRedirect();

        $file = File::where('folder_id', $subFolder->id)->first();
        $this->assertNotNull($file);
        $this->assertSame('surat-tugas.pdf', $file->display_name);
        Storage::disk('public')->assertExists($file->file_path);

        // Hapus folder induk harus menghapus subfolder & file secara rekursif
        $this->actingAs($adminTeknis)
            ->delete(route('documents.folder.destroy', $rootFolder->id))
            ->assertRedirect();

        $this->assertNull(Folder::find($rootFolder->id));
        $this->assertNull(Folder::find($subFolder->id));
        $this->assertNull(File::find($file->id));
        Storage::disk('public')->assertMissing($file->file_path);
    }
}
