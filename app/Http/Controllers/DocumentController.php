<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\File;
use App\Models\Folder;
use App\Models\Training;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    private const DEFAULT_BIDANGS = [
        'Bidang Sertifikasi Kompetensi dan Pengelolaan Kelembagaan',
        'Bidang Pengembangan Kompetensi Teknis Inti',
        'Bidang Pengembangan Kompetensi Teknis Umum',
        'Pengembangan Kompetensi Manajerial',
    ];

    public function index(Request $request)
    {
        $user = Auth::user();
        $this->ensureTrainingFoldersExist($user);
        $folderId = $request->query('folder');
        $selectedBidang = $request->query('bidang');

        // 1. Landing Page Superadmin (Folder Global + Daftar Bidang)
        if ($user->role === 'superadmin' && !$folderId && !$selectedBidang) {
            $globalFolders = Folder::with('user')
                ->withCount(['children', 'files'])
                ->where('bidang', 'Semua Bidang')
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get();

            $bidangNames = collect(self::DEFAULT_BIDANGS)
                ->merge(User::whereNotNull('bidang')->where('bidang', '!=', '')->pluck('bidang'))
                ->merge(Training::whereNotNull('bidang')->where('bidang', '!=', '')->pluck('bidang'))
                ->merge(Folder::whereNotNull('bidang')->whereNotIn('bidang', ['', 'Semua Bidang'])->pluck('bidang'))
                ->unique()
                ->values();

            $listBidang = $bidangNames->map(fn ($b) => (object) ['bidang' => $b]);

            $documentStats = [
                'folders' => Folder::count(),
                'files' => File::count(),
                'size' => (int) File::sum('file_size'),
                'global' => Folder::where('bidang', 'Semua Bidang')->whereNull('parent_id')->count(),
            ];

            return view('documents.index', compact('listBidang', 'globalFolders', 'documentStats'));
        }

        // 2. Tentukan konteks bidang & folder aktif
        $currentFolder = null;
        $breadcrumbs = collect();

        if ($folderId) {
            $currentFolder = Folder::with('user')->findOrFail($folderId);
            if (!$this->canViewFolder($user, $currentFolder)) {
                abort(403, 'Anda tidak memiliki akses ke folder bidang ini.');
            }
            $targetBidang = $selectedBidang ?: $currentFolder->bidang;
            $breadcrumbs = $this->buildBreadcrumbs($currentFolder);
        } else {
            if ($user->role === 'superadmin') {
                $targetBidang = $selectedBidang ?: 'Semua Bidang';
            } else {
                if ($selectedBidang && $selectedBidang !== $user->bidang && $selectedBidang !== 'Semua Bidang') {
                    abort(403, 'Anda hanya dapat mengakses ruang dokumen bidang Anda.');
                }
                $targetBidang = $selectedBidang === 'Semua Bidang' ? 'Semua Bidang' : $user->bidang;
            }
        }

        // 3. Ambil daftar folder & berkas pada level ini
        if ($currentFolder) {
            $folders = Folder::with('user')
                ->withCount(['children', 'files'])
                ->where('parent_id', $currentFolder->id)
                ->orderBy('name')
                ->get();

            $files = File::with('user')
                ->where('folder_id', $currentFolder->id)
                ->latest()
                ->get();
        } else {
            $foldersQuery = Folder::with('user')
                ->withCount(['children', 'files'])
                ->whereNull('parent_id');

            if ($targetBidang === 'Semua Bidang') {
                $foldersQuery->where('bidang', 'Semua Bidang');
            } else {
                $foldersQuery->where(function ($q) use ($targetBidang) {
                    $q->where('bidang', $targetBidang)
                        ->orWhere('bidang', 'Semua Bidang');
                });
            }

            $folders = $foldersQuery
                ->orderByRaw("CASE WHEN bidang = 'Semua Bidang' THEN 0 ELSE 1 END")
                ->orderBy('name')
                ->get();

            $files = collect();
        }

        $canManageCurrent = $currentFolder
            ? $this->canManageFolder($user, $currentFolder)
            : ($targetBidang === 'Semua Bidang' ? $user->role === 'superadmin' : true);

        $canCreateFolderHere = $currentFolder
            ? $this->canManageFolder($user, $currentFolder)
            : ($targetBidang === 'Semua Bidang' ? $user->role === 'superadmin' : in_array($user->role, ['superadmin', 'admin_bidang'], true));

        $scopeQuery = Folder::query();
        if ($user->role !== 'superadmin') {
            $scopeQuery->whereIn('bidang', [$user->bidang, 'Semua Bidang']);
        } elseif ($targetBidang) {
            $scopeQuery->where('bidang', $targetBidang);
        }
        $scopedFolderIds = (clone $scopeQuery)->pluck('id');

        $documentStats = [
            'folders' => $scopedFolderIds->count(),
            'files' => File::whereIn('folder_id', $scopedFolderIds)->count(),
            'size' => (int) File::whereIn('folder_id', $scopedFolderIds)->sum('file_size'),
            'global' => Folder::where('bidang', 'Semua Bidang')->whereNull('parent_id')->count(),
        ];

        return view('documents.index', [
            'folders' => $folders,
            'files' => $files,
            'currentFolder' => $currentFolder,
            'currentBidang' => $targetBidang,
            'breadcrumbs' => $breadcrumbs,
            'canManageCurrent' => $canManageCurrent,
            'canCreateFolderHere' => $canCreateFolderHere,
            'documentStats' => $documentStats,
        ]);
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
            'bidang' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $parent = null;

        if ($request->filled('parent_id')) {
            $parent = Folder::findOrFail($request->parent_id);
            if (!$this->canManageFolder($user, $parent)) {
                abort(403, 'Anda tidak memiliki izin membuat subfolder di dalam folder ini.');
            }
            $bidangToSave = $parent->bidang;
        } else {
            if ($user->role === 'superadmin') {
                $bidangToSave = $request->input('bidang') ?: 'Semua Bidang';
            } else {
                $bidangToSave = $user->bidang;
            }
        }

        $folder = Folder::create([
            'name' => trim($request->name),
            'parent_id' => $parent?->id,
            'bidang' => $bidangToSave,
            'training_id' => $parent?->training_id,
            'user_id' => $user->id,
        ]);

        LogHelper::record('Dokumen', "Membuat folder '{$folder->name}' pada ruang '{$folder->bidang}'");

        return back()->with('success', 'Folder berhasil dibuat.');
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'folder_id' => 'required|exists:folders,id',
            'attachments' => 'required|array|min:1',
            'attachments.*' => 'file|max:20480',
        ]);

        $user = Auth::user();
        $folder = Folder::findOrFail($request->folder_id);

        if (!$this->canManageFolder($user, $folder)) {
            abort(403, 'Anda tidak memiliki izin mengunggah berkas ke folder ini.');
        }

        $uploadedCount = 0;
        foreach ($request->file('attachments') as $uploadedFile) {
            $storedPath = $uploadedFile->store('documents', 'public');

            File::create([
                'folder_id' => $folder->id,
                'display_name' => $uploadedFile->getClientOriginalName(),
                'file_path' => $storedPath,
                'file_type' => strtolower($uploadedFile->getClientOriginalExtension()),
                'file_size' => $uploadedFile->getSize(),
                'user_id' => $user->id,
            ]);
            $uploadedCount++;
        }

        LogHelper::record('Dokumen', "Mengunggah {$uploadedCount} berkas ke folder '{$folder->name}'");

        return back()->with('success', "{$uploadedCount} berkas berhasil diunggah.");
    }

    public function viewFile(File $file)
    {
        $folder = $file->folder;
        if (!$this->canViewFolder(Auth::user(), $folder)) {
            abort(403, 'Anda tidak memiliki akses ke berkas ini.');
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            return back()->with('error', 'Berkas fisik tidak ditemukan di penyimpanan.');
        }

        return response()->file(Storage::disk('public')->path($file->file_path));
    }

    public function downloadFile(File $file)
    {
        $folder = $file->folder;
        if (!$this->canViewFolder(Auth::user(), $folder)) {
            abort(403, 'Anda tidak memiliki akses mengunduh berkas ini.');
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            return back()->with('error', 'Berkas fisik tidak ditemukan di penyimpanan.');
        }

        return Storage::disk('public')->download($file->file_path, $file->display_name);
    }

    public function destroyFile(File $file)
    {
        $folder = $file->folder;
        if (!$this->canManageFolder(Auth::user(), $folder)) {
            abort(403, 'Anda tidak memiliki izin menghapus berkas ini.');
        }

        $name = $file->display_name;
        if (Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }
        $file->delete();

        LogHelper::record('Dokumen', "Menghapus berkas '{$name}' dari folder '{$folder->name}'");

        return back()->with('success', 'Berkas berhasil dihapus.');
    }

    public function destroyFolder(Folder $folder)
    {
        if (!$this->canManageFolder(Auth::user(), $folder)) {
            abort(403, 'Anda tidak memiliki izin menghapus folder ini.');
        }

        $name = $folder->name;
        $this->deleteFolderTree($folder);

        LogHelper::record('Dokumen', "Menghapus folder '{$name}' beserta seluruh isinya");

        return back()->with('success', 'Folder beserta seluruh isinya berhasil dihapus.');
    }

    public function canViewFolder(User $user, Folder $folder): bool
    {
        if ($user->role === 'superadmin') {
            return true;
        }

        return $folder->bidang === 'Semua Bidang' || $folder->bidang === $user->bidang;
    }

    public function canManageFolder(User $user, Folder $folder): bool
    {
        if ($user->role === 'superadmin') {
            return true;
        }

        if ($user->role !== 'admin_bidang') {
            return false;
        }

        if ($folder->bidang === 'Semua Bidang') {
            return (int) $folder->user_id === (int) $user->id;
        }

        return $folder->bidang === $user->bidang;
    }

    private function buildBreadcrumbs(Folder $folder)
    {
        $chain = collect();
        $cursor = $folder;
        $guard = 0;

        while ($cursor && $guard < 50) {
            $chain->prepend($cursor);
            $cursor = $cursor->parent;
            $guard++;
        }

        return $chain;
    }

    private function deleteFolderTree(Folder $folder): void
    {
        $folder->load(['children', 'files']);

        foreach ($folder->children as $child) {
            $this->deleteFolderTree($child);
        }

        foreach ($folder->files as $file) {
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            $file->delete();
        }

        $folder->delete();
    }

    private function ensureTrainingFoldersExist(User $user): void
    {
        $trainingsWithoutFolder = Training::whereDoesntHave('folders', function ($q) {
            $q->whereNull('parent_id');
        })->get();

        foreach ($trainingsWithoutFolder as $training) {
            $root = Folder::firstOrCreate(
                ['training_id' => $training->id, 'parent_id' => null],
                [
                    'name' => $training->nama_pelatihan,
                    'bidang' => $training->bidang,
                    'user_id' => $user->id,
                ]
            );

            foreach (['JADWAL PELATIHAN', 'DATA PESERTA', 'HASIL EVALUASI DAMPAK'] as $sub) {
                Folder::firstOrCreate([
                    'training_id' => $training->id,
                    'parent_id' => $root->id,
                    'name' => $sub,
                ], [
                    'bidang' => $training->bidang,
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
