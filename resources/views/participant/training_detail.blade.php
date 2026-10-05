@extends('layouts.master')

@section('title', 'Detail Pelatihan Saya')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Pelatihan Saya /</span> {{ $training->nama_pelatihan }}
        </h4>
        <a href="{{ route('participant.trainings') }}" class="btn btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible border-0 shadow-sm mb-4" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Panel Kiri: Informasi Pelatihan & Akses LMS -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bx bx-info-circle me-2 text-primary"></i>Informasi Pelatihan
                    </h5>
                    <span class="badge bg-label-primary text-uppercase">{{ $training->model }}</span>
                </div>
                <div class="card-body pt-4">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <td class="ps-0 py-2 text-muted" width="200">Nama Pelatihan</td>
                                    <td class="py-2 fw-bold text-dark">: {{ $training->nama_pelatihan }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-0 py-2 text-muted">Angkatan</td>
                                    <td class="py-2 fw-semibold text-dark">: Angkatan {{ $training->angkatan }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-0 py-2 text-muted">Penyelenggara (Bidang)</td>
                                    <td class="py-2 text-dark">: {{ $training->bidang }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-0 py-2 text-muted">Metode Pelaksanaan</td>
                                    <td class="py-2">: <span class="badge bg-label-info">{{ strtoupper($training->metode) }}</span></td>
                                </tr>
                                <tr>
                                    <td class="ps-0 py-2 text-muted">Durasi Kompetensi</td>
                                    <td class="py-2 fw-bold text-primary">: {{ $training->jp }} Jam Pelajaran (JP)</td>
                                </tr>
                                <tr>
                                    <td class="ps-0 py-2 text-muted">Periode Tanggal</td>
                                    <td class="py-2 text-dark">: {{ \Carbon\Carbon::parse($training->tgl_mulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($training->tgl_selesai)->translatedFormat('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="ps-0 py-2 text-muted">Lokasi</td>
                                    <td class="py-2 text-dark">: {{ $training->lokasi }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Akses Ruang Belajar Digital (LMS) -->
                    @if($training->link_lms)
                        <div class="mt-4 p-4 border rounded bg-label-primary">
                            <div class="d-flex align-items-start">
                                <div class="avatar bg-primary p-2 rounded me-3 shadow-sm">
                                    <i class="bx bx-laptop text-white h3 mb-0"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold text-dark">Akses Ruang Belajar Digital (LMS / Zoom)</h6>
                                    <p class="small text-muted mb-3">Tautan kelas daring resmi telah disiapkan oleh admin bidang pelatihan.</p>
                                    <a href="{{ $training->link_lms }}" target="_blank" class="btn btn-primary shadow-sm">
                                        <i class="bx bx-rocket me-1"></i> MASUK KE RUANG BELAJAR
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mt-4 p-3 border rounded bg-light">
                            <small class="text-muted italic mb-0 d-block">
                                <i class="bx bx-info-circle me-1"></i> Tautan ruang belajar daring belum disematkan oleh admin. Silakan pantau pengumuman lebih lanjut.
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            @if($training->model === 'blended' && $training->stages->count() > 0)
            <!-- Tahapan Blended -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bx bx-layer me-2 text-warning"></i>Tahapan Pelaksanaan (Blended Learning)
                    </h5>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tahapan</th>
                                    <th>Metode</th>
                                    <th>Jadwal Pelaksanaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($training->stages as $i => $st)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="fw-bold text-dark">{{ $st->nama_tahapan }}</td>
                                    <td><span class="badge bg-label-info">{{ strtoupper($st->metode) }}</span></td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($st->tgl_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($st->tgl_selesai)->format('d/m/Y') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Panel Kanan: Status Registrasi Peserta (Single Input Data) -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-light border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="bx bx-badge-check me-2 text-success"></i>Status Registrasi Peserta
                    </h5>
                </div>
                <div class="card-body pt-4">
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xl bg-label-success mx-auto mb-3 rounded-circle shadow-sm">
                            <i class="bx bx-check-double fs-1"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">{{ $participant->name }}</h6>
                        <code class="text-primary fw-bold">{{ $participant->nip_nik }}</code>
                        <span class="badge bg-success d-block w-50 mx-auto mt-2">Terdaftar Resmi</span>
                    </div>

                    <div class="border-top pt-3">
                        <small class="text-muted text-uppercase fw-bold d-block mb-2" style="font-size: 11px;">Data Smart Linking</small>
                        <div class="mb-2">
                            <small class="text-muted d-block">Jabatan:</small>
                            <span class="fw-semibold text-dark">{{ $participant->jabatan }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Instansi:</small>
                            <span class="fw-semibold text-dark">{{ $participant->instansi }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Pohon Wilayah:</small>
                            <span class="fw-semibold text-dark">{{ $participant->kabupaten_kota }}, {{ $participant->provinsi }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Status Kepegawaian:</small>
                            <span class="badge bg-label-secondary">{{ $participant->status_kepegawaian }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection