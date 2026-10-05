@extends('layouts.master')

@section('title', 'Kelola Pelatihan - Modul 1')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Pelatihan /</span> Kelola Data Pelatihan
        </h4>
        <a href="{{ route('trainings.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible border-0 shadow-sm mb-4" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Banner Info Pelatihan -->
    <div class="card mb-4 bg-primary text-white shadow-sm border-0">
        <div class="card-body py-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-xl bg-label-white p-2 rounded me-3 shadow-sm">
                        <i class="bx bx-buildings h2 mb-0 text-white"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-white text-primary fw-bold text-uppercase" style="font-size: 11px;">
                                Model {{ $training->model }}
                            </span>
                            <span class="badge bg-label-white text-white">
                                {{ strtoupper($training->metode) }}
                            </span>
                        </div>
                        <h4 class="text-white mb-1 fw-bold text-uppercase">{{ $training->nama_pelatihan }}</h4>
                        <p class="text-white mb-0 opacity-75">
                            Angkatan {{ $training->angkatan }} &bull; {{ $training->bidang }}
                        </p>
                    </div>
                </div>
                <div class="text-md-end">
                    <small class="text-white d-block opacity-75">Durasi Pelatihan</small>
                    <h3 class="text-white fw-bold mb-0">{{ $training->jp }} JP</h3>
                    <small class="text-white opacity-75">{{ \Carbon\Carbon::parse($training->tgl_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($training->tgl_selesai)->format('d/m/Y') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- SEKSI 1: KONTROL AKSES & OTOMASI SIKLUS -->
        <div class="col-lg-6">
            <div class="card h-100 border shadow-none">
                <div class="card-header border-bottom bg-light py-3">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bx bx-key me-2 text-primary"></i>Kontrol Akses & Otomasi Siklus
                    </h5>
                </div>
                <div class="card-body pt-4">
                    <!-- 1. Generator Token 6 Digit -->
                    <div class="p-3 mb-3 border rounded bg-label-primary shadow-xs">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted text-uppercase fw-bold">Kode Undangan / Token Peserta (6 Digit)</small>
                            <a href="{{ route('trainings.new_code', $training->id) }}" 
                               class="btn btn-xs btn-outline-primary"
                               onclick="return confirm('Generate kode baru? Kode lama tidak akan berlaku lagi.')">
                                <i class="bx bx-refresh me-1"></i> Buat Kode Baru
                            </a>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="mb-0 fw-bold text-primary letter-spacing-1">{{ $training->invitation_code }}</h3>
                            <button class="btn btn-primary btn-sm" onclick="copyInvitation('{{ $training->invitation_code }}', this)">
                                <i class="bx bx-copy me-1"></i> Salin Token
                            </button>
                        </div>
                        <small class="text-muted d-block mt-2" style="font-size: 11px;">
                            Bagikan token ini kepada peserta untuk proses mandiri mendaftar pelatihan (Smart Linking).
                        </small>
                    </div>

                    <!-- 2. Trigger Otomasi Jadwal Sebar Pasca -->
                    <div class="p-3 mb-3 border rounded bg-light">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="bx bx-calendar-event me-1 text-info"></i> Trigger Jadwal Sebar Pasca
                            </h6>
                            @php $sisaSebar = $training->sisa_hari_sebar; @endphp
                            @if($sisaSebar > 0)
                                <span class="badge bg-label-secondary">H-{{ $sisaSebar }} Hari</span>
                            @else
                                <span class="badge bg-label-success">Waktu Sebar Tiba</span>
                            @endif
                        </div>
                        <p class="small text-muted mb-2">
                            Kalkulasi otomatis tanggal sebar evaluasi pasca (<strong>{{ $training->bidang == 'Bidang Pengembangan Kompetensi Manajerial' ? '1 Tahun' : '4 Bulan' }}</strong> setelah pelatihan selesai).
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Target Tanggal Sebar:</small>
                            <span class="fw-bold text-dark">{{ $training->tgl_sebar_l34->translatedFormat('d F Y') }}</span>
                        </div>
                    </div>

                    <!-- 3. Tautan Ruang Belajar LMS -->
                    <div class="p-3 border rounded bg-light mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="bx bx-laptop me-1 text-secondary"></i> Akses LMS / Ruang Belajar
                            </h6>
                            <button class="btn btn-xs btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modalLms{{ $training->id }}">
                                <i class="bx bx-edit me-1"></i> Edit Tautan
                            </button>
                        </div>
                        @if($training->link_lms)
                            <a href="{{ $training->link_lms }}" target="_blank" class="text-truncate d-block small text-primary fw-bold mt-1">
                                <i class="bx bx-link-external me-1"></i> {{ $training->link_lms }}
                            </a>
                        @else
                            <small class="text-muted italic d-block mt-1">Belum ada tautan LMS yang disimpan.</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- SEKSI 2: DATA PESERTA & SMART LINKING -->
        <div class="col-lg-6">
            <div class="card h-100 border shadow-none">
                <div class="card-header border-bottom bg-light py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bx bx-group me-2 text-success"></i>Manajemen Peserta (Smart Linking)
                    </h5>
                    <span class="badge bg-label-success">{{ $training->participants_count }} / {{ $training->jumlah_peserta }} Kuota</span>
                </div>
                <div class="card-body pt-4">
                    <!-- Progress Keterisian Peserta -->
                    @php
                        $targetPeserta = $training->jumlah_peserta > 0 ? $training->jumlah_peserta : 1;
                        $persenTerisi = round(($training->participants_count / $targetPeserta) * 100);
                        if($persenTerisi > 100) $persenTerisi = 100;
                    @endphp
                    <div class="mb-4">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="fw-semibold text-dark">Keterisian Kuota Pelatihan</span>
                            <span class="fw-bold text-success">{{ $training->participants_count }} dari {{ $training->jumlah_peserta }} Peserta ({{ $persenTerisi }}%)</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ $persenTerisi }}%"></div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mb-4">
                        <a href="{{ route('trainings.participants', $training->id) }}" class="btn btn-primary shadow-sm py-2">
                            <i class="bx bx-user-check me-2"></i> KELOLA DAFTAR PESERTA
                        </a>
                        <a href="{{ route('participants.export_data', $training->id) }}" class="btn btn-outline-success text-start py-2">
                            <i class="bx bxs-spreadsheet me-2"></i> Ekspor Data Peserta (Excel)
                        </a>
                    </div>

                    <div class="alert alert-light border shadow-none small mb-0">
                        <h6 class="fw-bold mb-1 text-dark"><i class="bx bx-info-circle me-1 text-primary"></i> Single Input Data Architecture</h6>
                        Data peserta yang mendaftar melalui token akan secara otomatis menyinkronkan profil ASN (NIP, Jabatan, Instansi, Pohon Wilayah Provinsi & Kab/Kota) tanpa input berulang.
                    </div>
                </div>
            </div>
        </div>

        @if($training->model === 'blended')
        <!-- SEKSI 3: TAHAPAN PELATIHAN BLENDED -->
        <div class="col-12">
            <div class="card border shadow-none">
                <div class="card-header border-bottom bg-light py-3">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bx bx-layer me-2 text-warning"></i>Tahapan Pelatihan Blended Learning
                    </h5>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Tahapan</th>
                                    <th>Metode</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($training->stages as $index => $stage)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-bold text-dark">{{ $stage->nama_tahapan }}</td>
                                    <td><span class="badge bg-label-info">{{ strtoupper($stage->metode) }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($stage->tgl_mulai)->translatedFormat('d F Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($stage->tgl_selesai)->translatedFormat('d F Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">Belum ada tahapan yang dikonfigurasi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- MODAL INPUT/UPDATE LMS --}}
<div class="modal fade" id="modalLms{{ $training->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('trainings.set_lms', $training->id) }}" method="POST" class="modal-content shadow-lg">
            @csrf @method('PUT')
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Tautan Akses Pelatihan (LMS)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="mb-0">
                    <label class="form-label fw-bold">Link URL Ruang Belajar (Zoom/LMS/Drive)</label>
                    <input type="url" name="link_lms" class="form-control" placeholder="https://..." value="{{ $training->link_lms }}" required>
                    <div class="form-text mt-2 small text-muted">Pastikan URL lengkap diawali dengan http:// atau https://</div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan Tautan</button>
            </div>
        </form>
    </div>
</div>

@push('js')
<script>
function copyInvitation(code, btn) {
    navigator.clipboard.writeText(code).then(() => {
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bx bx-check me-1"></i> Tersalin';
        btn.classList.replace('btn-primary', 'btn-success');
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.replace('btn-success', 'btn-primary');
        }, 2000);
    });
}
</script>
@endpush
@endsection