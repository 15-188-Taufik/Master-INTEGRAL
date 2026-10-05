@extends('layouts.master')

@section('title', 'Daftar Pelatihan - Modul 1')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Daftar Pelatihan</h4>
            <p class="text-muted mb-0 small">Manajemen master data pelatihan, alur pendaftaran, dan otomasi token siklus pelatihan</p>
        </div>
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                <i class="bx bx-plus me-1"></i> Buat Pelatihan
            </button>
            <ul class="dropdown-menu shadow">
                <li><a class="dropdown-item py-2" href="{{ route('trainings.create', ['model' => 'standar']) }}"><i class="bx bx-chalkboard me-2 text-primary"></i>Model Standar</a></li>
                <li><a class="dropdown-item py-2" href="{{ route('trainings.create', ['model' => 'blended']) }}"><i class="bx bx-sync me-2 text-warning"></i>Model Blended Learning</a></li>
            </ul>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center border-0 shadow-sm mb-4" role="alert">
            <i class="bx bx-check-circle me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main List -->
    <div class="row pb-5">
        @forelse($trainings as $t)
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm card-training-row">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <!-- 1. Info Pelatihan -->
                        <div class="col-lg-4 border-end-lg">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge {{ $t->model == 'blended' ? 'bg-label-warning' : 'bg-label-info' }} btn-xs text-uppercase me-2" style="font-size: 10px;">
                                    {{ $t->model }}
                                </span>
                                <span class="badge bg-label-secondary btn-xs text-uppercase me-2" style="font-size: 10px;">
                                    {{ $t->metode }}
                                </span>
                                <small class="text-muted fw-bold" style="font-size: 10px; letter-spacing: 1px;">ANGKATAN {{ $t->angkatan }}</small>
                            </div>
                            <h5 class="fw-bold mb-1 text-dark">{{ $t->nama_pelatihan }}</h5>
                            <div class="d-flex align-items-center gap-2">
                                <small class="text-muted"><i class="bx bx-map-pin text-danger me-1"></i>{{ $t->lokasi }}</small>
                                <span class="text-muted">&bull;</span>
                                <small class="text-primary fw-bold">{{ $t->jp }} JP</small>
                            </div>
                        </div>

                        <!-- 2. Bidang & Jadwal Pelaksanaan -->
                        <div class="col-lg-3 py-2 py-lg-0 border-end-lg px-lg-4">
                            <small class="text-muted d-block mb-1 text-uppercase fw-semibold" style="font-size: 10px;">Penyelenggara</small>
                            <p class="mb-2 text-dark small fw-bold text-wrap" style="line-height: 1.3;">{{ $t->bidang }}</p>
                            <div class="d-flex gap-3">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 10px;">Mulai</small>
                                    <small class="fw-bold text-dark">{{ \Carbon\Carbon::parse($t->tgl_mulai)->format('d/m/Y') }}</small>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 10px;">Selesai</small>
                                    <small class="fw-bold text-dark">{{ \Carbon\Carbon::parse($t->tgl_selesai)->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Token & Otomasi Trigger Siklus -->
                        <div class="col-lg-3 py-2 py-lg-0 border-end-lg px-lg-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted text-uppercase" style="font-size: 10px;">Token Peserta:</small>
                                <code class="text-primary fw-bold" style="font-size: 13px;">{{ $t->invitation_code }}</code>
                            </div>
                            <div class="p-2 rounded bg-light border">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted" style="font-size: 10px;">Peserta Terdaftar:</small>
                                    <span class="badge bg-label-success" style="font-size: 10px;">{{ $t->participants_count }} / {{ $t->jumlah_peserta }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-muted" style="font-size: 10px;">Jadwal Sebar Pasca:</small>
                                    @php $sisaSebar = $t->sisa_hari_sebar; @endphp
                                    <small class="fw-bold text-dark" style="font-size: 10px;">
                                        {{ $sisaSebar > 0 ? 'H-' . $sisaSebar . ' Hari' : 'Telah Tiba' }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Aksi -->
                        <div class="col-lg-2 text-center text-lg-end mt-3 mt-lg-0">
                            <div class="d-flex justify-content-lg-end align-items-center gap-2">
                                <a href="{{ route('trainings.manage', $t->id) }}" class="btn btn-primary btn-sm px-3">
                                    <i class="bx bx-cog me-1"></i> Kelola
                                </a>
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-icon btn-outline-secondary" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-lg">
                                        <a class="dropdown-item text-warning" href="{{ route('trainings.edit', $t->id) }}">
                                            <i class="bx bx-edit-alt me-2"></i> Edit Data
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('trainings.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus pelatihan ini dari sistem?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bx bx-trash me-2"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm py-5 text-center text-muted">
                <i class="bx bx-folder-open fs-1 d-block mb-2"></i>
                Belum ada pelatihan yang terdaftar untuk bidang Anda.
            </div>
        </div>
        @endforelse
    </div>
</div>

@push('css')
<style>
    .card-training-row {
        transition: all 0.25s ease-in-out;
        border: 1px solid transparent !important;
    }
    .card-training-row:hover {
        transform: translateY(-2px);
        border-color: #696cff !important;
        box-shadow: 0 8px 18px rgba(105, 108, 255, 0.12) !important;
    }
    @media (min-width: 992px) {
        .border-end-lg {
            border-right: 1px solid #ebebeb;
        }
    }
</style>
@endpush
@endsection