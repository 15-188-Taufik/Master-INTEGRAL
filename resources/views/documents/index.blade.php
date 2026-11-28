@php
if (!function_exists('getFileIcon')) {
    function getFileIcon($ext) {
        $ext = strtolower((string) $ext);
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) return 'bx-image-alt text-info';
        if (in_array($ext, ['pdf'])) return 'bxs-file-pdf text-danger';
        if (in_array($ext, ['doc', 'docx'])) return 'bxs-file-doc text-primary';
        if (in_array($ext, ['xls', 'xlsx', 'csv'])) return 'bxs-spreadsheet text-success';
        if (in_array($ext, ['ppt', 'pptx'])) return 'bxs-slideshow text-warning';
        if (in_array($ext, ['zip', 'rar', '7z'])) return 'bx-archive text-secondary';
        return 'bx-file text-primary';
    }
}

if (!function_exists('formatSizeUnits')) {
    function formatSizeUnits($bytes) {
        $bytes = (int) $bytes;
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        if ($bytes > 1) return $bytes . ' bytes';
        if ($bytes === 1) return '1 byte';
        return '0 bytes';
    }
}
@endphp
@extends('layouts.master')

@section('title', 'Manajemen Dokumen')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Header / Hero --}}
    <div class="document-hero d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3">
        <div>
            <div class="text-uppercase small fw-bold text-primary mb-2">
                <i class="bx bx-folder-open me-1"></i>Ruang Dokumen & Arsip Terpadu INTEGRAL
            </div>
            <h3 class="fw-bold mb-1">
                Manajemen Dokumen
                @if(isset($currentBidang))
                    <span class="text-primary">— {{ $currentBidang }}</span>
                @endif
            </h3>
            <p class="text-muted mb-0">
                Kelola hierarki berkas bersarang (nested folder), folder global lintas bidang, dan folder otomatis pelatihan.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            @if(Auth::user()->role === 'superadmin' && (request()->query('folder') || isset($currentBidang)))
                <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-arrow-back me-1"></i>Daftar Bidang
                </a>
            @endif

            @if(($currentFolder ?? null) && ($canManageCurrent ?? false))
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
                    <i class="bx bx-upload me-1"></i>Upload Berkas
                </button>
            @endif

            @if($canCreateFolderHere ?? true)
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFolderModal">
                    <i class="bx bx-folder-plus me-1"></i>{{ ($currentFolder ?? null) ? 'Subfolder Baru' : 'Folder Baru' }}
                </button>
            @endif
        </div>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Statistik Ringkas --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card document-stat h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon bg-label-warning"><i class="bx bx-folder"></i></span>
                    <div>
                        <h4 class="mb-0 fw-bold">{{ number_format($documentStats['folders'] ?? 0) }}</h4>
                        <small class="text-muted">Total Folder</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card document-stat h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon bg-label-primary"><i class="bx bx-file"></i></span>
                    <div>
                        <h4 class="mb-0 fw-bold">{{ number_format($documentStats['files'] ?? 0) }}</h4>
                        <small class="text-muted">Total Berkas</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card document-stat h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon bg-label-info"><i class="bx bx-data"></i></span>
                    <div>
                        <h4 class="mb-0 fw-bold">{{ formatSizeUnits($documentStats['size'] ?? 0) }}</h4>
                        <small class="text-muted">Ukuran Tersimpan</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card document-stat h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon bg-label-success"><i class="bx bx-globe"></i></span>
                    <div>
                        <h4 class="mb-0 fw-bold">{{ number_format($documentStats['global'] ?? 0) }}</h4>
                        <small class="text-muted">Folder Global</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pencarian Cepat --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="input-group">
                <span class="input-group-text border-0 bg-transparent"><i class="bx bx-search"></i></span>
                <input type="search" id="documentSearch" class="form-control border-0 shadow-none" placeholder="Cari nama bidang, folder, atau berkas...">
                <span class="input-group-text border-0 bg-transparent text-muted small" id="searchResultInfo">Ketik untuk mencari</span>
            </div>
        </div>
    </div>

    {{-- KONDISI 1: LANDING PAGE SUPERADMIN (FOLDER GLOBAL + DAFTAR BIDANG) --}}
    @if(Auth::user()->role === 'superadmin' && !request()->query('folder') && !isset($currentBidang))
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold"><i class="bx bx-globe text-primary me-1"></i>Folder Global (Terlihat di Semua Bidang)</h5>
        </div>
        <div class="row g-4 mb-5">
            @foreach(($globalFolders ?? collect()) as $folder)
                <div class="col-md-3 col-6 document-search-item" data-search="{{ strtolower($folder->name . ' global semua bidang') }}">
                    <div class="card shadow-none border text-center h-100 folder-card border-primary position-relative">
                        <div class="card-body">
                            <div class="dropdown position-absolute end-0 top-0 me-2 mt-2">
                                <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li>
                                        <form action="{{ route('documents.folder.destroy', $folder->id) }}" method="POST" onsubmit="return confirm('Hapus folder global ini beserta seluruh subfolder dan berkas di dalamnya?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger">
                                                <i class="bx bx-trash me-2"></i>Hapus Folder
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>

                            <a href="{{ route('documents.index', ['folder' => $folder->id, 'bidang' => 'Semua Bidang']) }}" class="text-body d-block mt-2">
                                <i class="bx bx-folder text-primary mb-2 animate-folder" style="font-size: 4rem;"></i>
                                <h6 class="fw-bold mb-1 folder-name">{{ $folder->name }}</h6>
                                <small class="text-muted d-block mb-2">{{ $folder->children_count }} subfolder · {{ $folder->files_count }} berkas</small>
                                <span class="badge bg-label-primary">GLOBAL</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="col-md-3 col-6">
                <div class="card shadow-none border border-dashed text-center h-100 folder-card" data-bs-toggle="modal" data-bs-target="#addFolderModal">
                    <div class="card-body d-flex flex-column justify-content-center py-4">
                        <i class="bx bx-plus-circle display-5 text-primary mb-2"></i>
                        <p class="fw-semibold text-primary mb-0">Tambah Folder Global</p>
                        <small class="text-muted">Otomatis muncul di semua bidang</small>
                    </div>
                </div>
            </div>
        </div>

        <h5 class="mb-3 fw-bold"><i class="bx bx-buildings text-warning me-1"></i>Daftar Ruang Dokumen Per Bidang</h5>
        <div class="row g-4">
            @foreach($listBidang as $b)
                <div class="col-md-4 col-lg-3 document-search-item" data-search="{{ strtolower($b->bidang) }}">
                    <div class="card shadow-none border text-center h-100 folder-card">
                        <div class="card-body py-4 d-flex flex-column justify-content-between">
                            <div>
                                <div class="avatar avatar-xl bg-label-primary mx-auto mb-3">
                                    <span class="avatar-initial rounded-circle"><i class="bx bxs-briefcase fs-3"></i></span>
                                </div>
                                <h6 class="fw-bold mb-3 folder-name">{{ $b->bidang }}</h6>
                            </div>
                            <div>
                                <a href="{{ route('documents.index', ['bidang' => $b->bidang]) }}" class="btn btn-sm btn-primary">
                                    <i class="bx bx-folder-open me-1"></i>Buka Ruang Dokumen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    {{-- KONDISI 2: EXPLORER NESTED FOLDER & BERKAS --}}
    @else
        {{-- Breadcrumbs Multi-Level --}}
        <div class="card mb-4 shadow-none border bg-light">
            <div class="card-body py-2 px-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 flex-wrap">
                        @if(Auth::user()->role === 'superadmin')
                            <li class="breadcrumb-item">
                                <a href="{{ route('documents.index') }}">Daftar Bidang</a>
                            </li>
                        @endif
                        <li class="breadcrumb-item {{ !($currentFolder ?? null) ? 'active fw-bold text-primary' : '' }}">
                            @if($currentFolder ?? null)
                                <a href="{{ route('documents.index', ['bidang' => $currentBidang]) }}">
                                    <i class="bx bx-home-alt me-1"></i>{{ $currentBidang === 'Semua Bidang' ? 'Dokumen Global' : ($currentBidang ?: 'Ruang Dokumen') }}
                                </a>
                            @else
                                <i class="bx bx-home-alt me-1"></i>{{ $currentBidang === 'Semua Bidang' ? 'Dokumen Global' : ($currentBidang ?: 'Ruang Dokumen') }}
                            @endif
                        </li>

                        @foreach(($breadcrumbs ?? collect()) as $crumb)
                            @if($loop->last)
                                <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">
                                    <i class="bx bx-folder-open me-1"></i>{{ $crumb->name }}
                                </li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ route('documents.index', ['folder' => $crumb->id, 'bidang' => $currentBidang]) }}">
                                        {{ $crumb->name }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Grid Folder / Subfolder --}}
        <div class="row g-4 mb-4">
            @forelse($folders as $folder)
                @php
                    $canManageItem = Auth::user()->role === 'superadmin'
                        || ($folder->bidang !== 'Semua Bidang' && $folder->bidang === Auth::user()->bidang)
                        || ((int) $folder->user_id === (int) Auth::id());
                @endphp
                <div class="col-md-4 col-xl-3 col-6 document-search-item" data-search="{{ strtolower($folder->name . ' ' . $folder->bidang . ' ' . ($folder->user?->name ?? '')) }}">
                    <div class="card shadow-none border text-center h-100 folder-card position-relative">
                        <div class="card-body">
                            @if($canManageItem)
                                <div class="dropdown position-absolute end-0 top-0 me-2 mt-2">
                                    <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow">
                                        <li>
                                            <form action="{{ route('documents.folder.destroy', $folder->id) }}" method="POST" onsubmit="return confirm('Hapus folder {{ addslashes($folder->name) }} beserta seluruh subfolder dan berkas di dalamnya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="dropdown-item text-danger">
                                                    <i class="bx bx-trash me-2"></i>Hapus Folder
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @endif

                            <a href="{{ route('documents.index', ['folder' => $folder->id, 'bidang' => $currentBidang]) }}" class="text-body d-block mt-2">
                                <i class="bx bx-folder {{ $folder->bidang === 'Semua Bidang' ? 'text-primary' : 'text-warning' }} mb-2 animate-folder" style="font-size: 4.25rem;"></i>
                                <h6 class="fw-bold mb-1 px-2 folder-name">{{ $folder->name }}</h6>
                                <small class="text-muted d-block mb-2">{{ $folder->children_count }} subfolder · {{ $folder->files_count }} berkas</small>

                                @if($folder->bidang === 'Semua Bidang')
                                    <span class="badge bg-label-primary">GLOBAL</span>
                                @elseif($folder->training_id)
                                    <span class="badge bg-label-info">Pelatihan</span>
                                @else
                                    <span class="badge bg-label-secondary">{{ $folder->bidang }}</span>
                                @endif

                                <small class="text-muted d-block mt-1" style="font-size: 11px;">
                                    Oleh: {{ $folder->user?->name ?? 'Sistem' }}
                                </small>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                @if($files->isEmpty())
                    <div class="col-12 text-center py-5">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-label-warning mb-3" style="width: 92px; height: 92px;">
                            <i class="bx bx-folder-open text-warning" style="font-size: 3.5rem;"></i>
                        </span>
                        <h6 class="fw-bold mb-1">{{ ($currentFolder ?? null) ? 'Folder ini masih kosong' : 'Belum ada folder pada bidang ini' }}</h6>
                        <p class="text-muted mb-3">
                            @if($currentFolder ?? null)
                                Klik <strong>Subfolder Baru</strong> untuk membuat hierarki bersarang atau <strong>Upload Berkas</strong> untuk mengunggah dokumen.
                            @else
                                Buat folder baru secara manual atau tambahkan Pelatihan baru untuk memicu pembuatan folder otomatis (Trigger 2).
                            @endif
                        </p>
                    </div>
                @endif
            @endforelse
        </div>

        {{-- Tabel Berkas di Dalam Folder --}}
        @if($files->count() > 0)
            <div class="card shadow-sm border document-files-card">
                <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold"><i class="bx bx-file me-2"></i>Daftar Berkas Dokumen ({{ $files->count() }})</h6>
                </div>
                <div class="table-responsive document-files-table">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Berkas</th>
                                <th>Ukuran</th>
                                <th>Waktu Unggah</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                                <tr class="document-search-item" data-search="{{ strtolower($file->display_name . ' ' . $file->file_type . ' ' . ($file->user?->name ?? '')) }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bx {{ getFileIcon($file->file_type) }} fs-3 me-2"></i>
                                            <div class="min-w-0">
                                                <span class="fw-semibold d-block document-file-name">{{ $file->display_name }}</span>
                                                <small class="text-muted text-uppercase">{{ $file->file_type ?: 'FILE' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><small class="text-muted">{{ formatSizeUnits($file->file_size) }}</small></td>
                                    <td>
                                        <small class="d-block">{{ $file->created_at?->format('d M Y, H:i') }}</small>
                                        <small class="text-muted">oleh {{ $file->user?->name ?? 'Sistem' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-flex gap-1">
                                            <a href="{{ route('documents.file.view', $file->id) }}" target="_blank" class="btn btn-sm btn-icon btn-outline-primary" title="Lihat Berkas">
                                                <i class="bx bx-show"></i>
                                            </a>
                                            <a href="{{ route('documents.file.download', $file->id) }}" class="btn btn-sm btn-icon btn-outline-secondary" title="Unduh Berkas">
                                                <i class="bx bx-download"></i>
                                            </a>
                                            @if($canManageCurrent ?? false)
                                                <form action="{{ route('documents.file.destroy', $file->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus berkas {{ addslashes($file->display_name) }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus Berkas">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif
</div>

{{-- Modal Buat Folder / Subfolder --}}
<div class="modal fade" id="addFolderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('documents.folder.create') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $currentFolder->id ?? '' }}">
            <input type="hidden" name="bidang" value="{{ $currentBidang ?? 'Semua Bidang' }}">

            <div class="modal-header border-bottom">
                <h5 class="modal-title">
                    @if($currentFolder ?? null)
                        Buat Subfolder di "{{ $currentFolder->name }}"
                    @elseif(!isset($currentBidang) || $currentBidang === 'Semua Bidang')
                        Buat Folder Global (Semua Bidang)
                    @else
                        Buat Folder Baru — {{ $currentBidang }}
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4">
                <label class="form-label fw-bold">Nama Folder</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Laporan Evaluasi Semester 1" required autofocus>
                @if(!isset($currentBidang) && !($currentFolder ?? null) && Auth::user()->role === 'superadmin')
                    <div class="form-text mt-2">
                        Folder Global akan otomatis tampil pada ruang dokumen seluruh bidang.
                    </div>
                @endif
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-check me-1"></i>Simpan Folder</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Upload Berkas --}}
@if($currentFolder ?? null)
<div class="modal fade" id="uploadFileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('documents.upload') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <input type="hidden" name="folder_id" value="{{ $currentFolder->id }}">
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Upload Berkas ke "{{ $currentFolder->name }}"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-4">
                <label class="form-label fw-bold">Pilih Berkas Dokumen</label>
                <input type="file" name="attachments[]" class="form-control" multiple required>
                <div class="form-text mt-2">Anda dapat memilih beberapa berkas sekaligus (maks. 20 MB per berkas).</div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success"><i class="bx bx-upload me-1"></i>Mulai Upload</button>
            </div>
        </form>
    </div>
</div>
@endif

<style>
    .folder-name { white-space: normal; overflow-wrap: anywhere; word-break: break-word; line-height: 1.35; }
    .document-file-name { white-space: normal; overflow-wrap: anywhere; word-break: break-word; }
    .folder-card { transition: all 0.2s ease-in-out; cursor: pointer; }
    .folder-card:hover {
        transform: translateY(-4px);
        border-color: #696cff !important;
        box-shadow: 0 4px 15px rgba(105, 108, 255, 0.12) !important;
    }
    .folder-card:hover .animate-folder { transform: scale(1.08); transition: transform 0.2s ease; }
    .document-hero {
        padding: 1.5rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, rgba(105,108,255,.10), rgba(3,195,236,.06));
        border: 1px solid rgba(105,108,255,.14);
    }
    .document-stat { border: 0; box-shadow: 0 .25rem 1rem rgba(67,89,113,.08); }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 14px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1.5rem; flex: 0 0 48px;
    }
    .document-search-item.is-filtered { display: none !important; }
</style>
@endsection

@push('js')
<script>
    @if(session('success'))
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('success')),
                timer: 2500,
                showConfirmButton: false
            });
        }
    @endif

    @if(session('error'))
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: @json(session('error')),
            });
        }
    @endif

    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('documentSearch');
        const info = document.getElementById('searchResultInfo');
        if (!input) return;

        input.addEventListener('input', function () {
            const keyword = this.value.trim().toLowerCase();
            const items = Array.from(document.querySelectorAll('.document-search-item'));
            let visible = 0;
            items.forEach(function (item) {
                const matched = !keyword || (item.dataset.search || '').includes(keyword);
                item.classList.toggle('is-filtered', !matched);
                if (matched) visible++;
            });
            if (info) {
                info.textContent = keyword ? (visible + ' hasil ditemukan') : 'Ketik untuk mencari';
            }
        });
    });
</script>
@endpush
