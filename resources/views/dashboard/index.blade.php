@extends('layouts.master')

@section('title', 'Dashboard Manajemen Pelatihan & Peserta')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- BANNER SELAMAT DATANG & KPI RINGKAS -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card bg-primary text-white shadow-sm border-0">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-white mb-2">Halo, {{ Auth::user()->name }}! 👋</h5>
                            <p class="mb-3 opacity-75">
                                Selamat datang di sistem <strong>Identity, Data Master & Manajemen Siklus Pelatihan</strong> (Modul 1 BPSDM).
                                @if(Auth::user()->role !== 'superadmin')
                                    <span class="d-block mt-1">Bidang: <strong>{{ Auth::user()->bidang }}</strong></span>
                                @else
                                    <span class="d-block mt-1">Role: <strong>Superadministrator (Akses Seluruh Bidang)</strong></span>
                                @endif
                            </p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('trainings.index') }}" class="btn btn-sm btn-white bg-white text-primary fw-bold shadow-xs">
                                    <i class="bx bx-list-ul me-1"></i> Kelola Pelatihan
                                </a>
                                @if(Auth::user()->role === 'superadmin')
                                <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-outline-white text-white border-white">
                                    <i class="bx bx-history me-1"></i> Log Aktivitas
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4 text-center">
                            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="135" alt="Dashboard Illustration" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-4">
            <div class="row">
                <div class="col-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <span class="fw-semibold d-block mb-1 text-muted small">Total Pelatihan</span>
                            <h3 class="card-title mb-1 text-primary fw-bold">{{ $totalTrainings }}</h3>
                            <small class="text-muted" style="font-size: 11px;">
                                <span class="text-success fw-bold">{{ $totalActive }}</span> Aktif | {{ $totalCompleted }} Selesai
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <span class="fw-semibold d-block mb-1 text-muted small">Total Peserta</span>
                            <h3 class="card-title mb-1 text-success fw-bold">{{ $totalParticipants }}</h3>
                            <small class="text-muted" style="font-size: 11px;">Terdaftar di sistem</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <span class="fw-semibold d-block mb-1 text-muted small">Model Blended</span>
                            <h3 class="card-title mb-1 text-warning fw-bold">{{ $totalBlended }}</h3>
                            <small class="text-muted" style="font-size: 11px;">{{ $totalStandar }} Model Standar</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <span class="fw-semibold d-block mb-1 text-muted small">Target 20 JP</span>
                            <h3 class="card-title mb-1 text-info fw-bold">{{ $jpComplianceRate }}%</h3>
                            <small class="text-muted" style="font-size: 11px;">Kepatuhan Tahunan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TRACKING JP TAHUNAN & SEBARAN POHON WILAYAH -->
    <div class="row">
        <!-- 1. TRACKING CAPAIAN MINIMAL 20 JP -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom bg-light py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <i class="bx bx-time-five text-primary me-2"></i>Tracking Capaian 20 JP Tahunan
                        </h5>
                        <small class="text-muted">Target minimal pengembangan kompetensi 20 JP per ASN / Peserta (Tahun {{ date('Y') }})</small>
                    </div>
                    <span class="badge bg-label-primary">{{ $totalUniquePesertaYear }} Peserta Aktif</span>
                </div>
                <div class="card-body pt-4">
                    <div class="row align-items-center mb-4">
                        <div class="col-6 text-center border-end">
                            <div class="avatar avatar-md bg-label-success mx-auto mb-2 rounded-circle">
                                <i class="bx bx-check-double fs-4"></i>
                            </div>
                            <h3 class="fw-bold mb-0 text-success">{{ $pesertaReached20Jp }}</h3>
                            <small class="text-muted fw-semibold">Memenuhi Target (&ge; 20 JP)</small>
                        </div>
                        <div class="col-6 text-center">
                            <div class="avatar avatar-md bg-label-warning mx-auto mb-2 rounded-circle">
                                <i class="bx bx-loader-alt fs-4"></i>
                            </div>
                            <h3 class="fw-bold mb-0 text-warning">{{ $pesertaBelow20Jp }}</h3>
                            <small class="text-muted fw-semibold">On Progress (&lt; 20 JP)</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small class="fw-bold text-dark">Persentase Pemenuhan Standar 20 JP</small>
                            <small class="fw-bold text-primary">{{ $jpComplianceRate }}%</small>
                        </div>
                        <div class="progress" style="height: 14px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                 role="progressbar" 
                                 style="width: {{ $jpComplianceRate }}%" 
                                 aria-valuenow="{{ $jpComplianceRate }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $jpComplianceRate }}%
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-light border shadow-none mb-0 small">
                        <i class="bx bx-info-circle text-primary me-1"></i>
                        Setiap ASN diwajibkan memenuhi hak pengembangan kompetensi sekurang-kurangnya <strong>20 Jam Pelajaran (JP)</strong> dalam 1 tahun anggaran.
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. PROFIL POHON WILAYAH (PROVINSI -> KAB/KOTA) -->
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header border-bottom bg-light py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bx bx-map-pin text-danger me-2"></i>Profil Sebaran Wilayah Peserta
                    </h5>
                    <small class="text-muted">Distribusi peserta berdasarkan hierarki wilayah (Provinsi &rarr; Kab/Kota)</small>
                </div>
                <div class="card-body pt-4">
                    <h6 class="small text-muted text-uppercase fw-bold mb-3">Top 5 Provinsi Asal Peserta</h6>
                    <div class="mb-4">
                        @forelse($topProvinces as $prov)
                            @php
                                $percentProv = $totalParticipants > 0 ? round(($prov->total / $totalParticipants) * 100) : 0;
                            @endphp
                            <div class="mb-2">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="fw-semibold text-dark"><i class="bx bx-chevron-right text-primary"></i> {{ $prov->provinsi }}</span>
                                    <span class="fw-bold">{{ $prov->total }} Peserta ({{ $percentProv }}%)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $percentProv }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small mb-0">Belum ada data wilayah provinsi peserta terdata.</p>
                        @endforelse
                    </div>

                    <h6 class="small text-muted text-uppercase fw-bold mb-2">Sebaran Kabupaten / Kota Terbanyak</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless">
                            <tbody>
                                @forelse($topCities as $city)
                                <tr>
                                    <td class="ps-0 py-1">
                                        <i class="bx bx-building text-secondary me-1"></i>
                                        <span class="fw-semibold text-dark">{{ $city->kabupaten_kota }}</span>
                                    </td>
                                    <td class="py-1 text-muted small text-truncate" style="max-width: 150px;">
                                        {{ $city->provinsi }}
                                    </td>
                                    <td class="text-end pe-0 py-1">
                                        <span class="badge bg-label-info">{{ $city->total }} Peserta</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted small py-2">Belum ada data kota/kabupaten.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PELATIHAN TERBARU & AUDIT TRAIL / LOG AKTIVITAS -->
    <div class="row">
        <!-- PELATIHAN TERBARU & TRIGGER SIKLUS -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header border-bottom bg-light py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <i class="bx bx-collection text-success me-2"></i>Daftar Pelatihan & Otomasi Trigger
                        </h5>
                        <small class="text-muted">Kode token 6 digit dan trigger kalkulasi jadwal sebar pasca pelatihan</small>
                    </div>
                    <a href="{{ route('trainings.index') }}" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Pelatihan</th>
                                <th>Model & JP</th>
                                <th>Token 6-Digit</th>
                                <th>Trigger Sebar Pasca</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestTrainings as $t)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark">{{ $t->nama_pelatihan }}</span>
                                    <small class="text-muted d-block">Angkatan {{ $t->angkatan }} | {{ $t->participants_count }} Peserta</small>
                                </td>
                                <td>
                                    <span class="badge {{ $t->model == 'blended' ? 'bg-label-warning' : 'bg-label-info' }}">
                                        {{ strtoupper($t->model) }}
                                    </span>
                                    <small class="fw-bold text-dark d-block mt-1">{{ $t->jp }} JP</small>
                                </td>
                                <td>
                                    <code class="text-primary fw-bold fs-6">{{ $t->invitation_code }}</code>
                                </td>
                                <td>
                                    <small class="fw-semibold text-dark d-block">
                                        {{ $t->tgl_sebar_l34->format('d/m/Y') }}
                                    </small>
                                    @php $sisaSebar = $t->sisa_hari_sebar; @endphp
                                    @if($sisaSebar > 0)
                                        <span class="badge bg-label-secondary" style="font-size: 10px;">
                                            <i class="bx bx-time me-1"></i> H-{{ $sisaSebar }} Hari
                                        </span>
                                    @else
                                        <span class="badge bg-label-success" style="font-size: 10px;">
                                            <i class="bx bx-check me-1"></i> Waktu Sebar Tiba
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('trainings.manage', $t->id) }}" class="btn btn-sm btn-label-primary">
                                        Kelola
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada pelatihan yang dibuat.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- AUDIT TRAIL LOG TERKINI -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header border-bottom bg-light py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0 fw-bold text-dark">
                            <i class="bx bx-history text-secondary me-2"></i>Audit Trail Terkini
                        </h5>
                        <small class="text-muted">Log aktivitas & IP browser</small>
                    </div>
                    @if(Auth::user()->role === 'superadmin')
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary btn-sm">Semua</a>
                    @endif
                </div>
                <div class="card-body pt-3">
                    <ul class="list-group list-group-flush">
                        @forelse($recentLogs as $log)
                        <li class="list-group-item px-0 py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="badge bg-label-primary" style="font-size: 10px;">{{ $log->module }}</span>
                                <small class="text-muted" style="font-size: 11px;">{{ $log->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="small text-dark mb-1 fw-semibold">{{ $log->activity }}</p>
                            <div class="d-flex justify-content-between small text-muted" style="font-size: 10px;">
                                <span><i class="bx bx-user me-1"></i>{{ $log->user->name ?? 'User' }}</span>
                                <span><i class="bx bx-globe me-1"></i>{{ $log->ip_address }}</span>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item px-0 py-4 text-center text-muted">Belum ada log aktivitas tercatat.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection