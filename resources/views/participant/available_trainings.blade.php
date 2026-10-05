@extends('layouts.master')

@section('title', 'Daftar Pelatihan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Peserta /</span> Daftar Pelatihan
        </h4>

        <!-- KOLOM SEARCH MODERN -->
        <div class="col-md-4">
            <form action="{{ route('participant.trainings') }}" method="GET">
                <div class="input-group input-group-merge shadow-sm">
                    <span class="input-group-text" id="basic-addon-search31"><i class="bx bx-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari Pelatihan atau Bidang..." 
                           value="{{ $search ?? '' }}" aria-label="Search..." aria-describedby="basic-addon-search31">
                    @if($search)
                        <a href="{{ route('participant.trainings') }}" class="btn btn-outline-secondary px-2 border-start-0">
                            <i class="bx bx-x"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if($search)
        <div class="mb-4 animate__animated animate__fadeIn">
            <small class="text-muted">Menampilkan hasil pencarian untuk: <span class="fw-bold text-primary">"{{ $search }}"</span></small>
        </div>
    @endif

    <div class="row">
        @forelse($trainings as $t)
            @php
                $isFinished = \Carbon\Carbon::parse($t->tgl_selesai)->isPast();
                $percent = ($t->jumlah_peserta > 0) ? ($t->participants_count / $t->jumlah_peserta) * 100 : 0;
                // Cek apakah user login sudah terdaftar
                $isEnrolled = $t->participants->where('user_id', auth()->id())->first();
                if(!$isEnrolled) {
                    $isEnrolled = $t->participants->where('nip_nik', auth()->user()->nip_nik)->first();
                }
            @endphp
            
            <div class="col-md-6 col-lg-4 mb-4">
                {{-- Card Dynamic Class berdasarkan Status --}}
                <div class="card h-100 shadow-sm border-0 transition-all 
                    {{ $isFinished ? 'opacity-75 bg-light grayscale' : 'hover-shadow' }} 
                    {{ $isEnrolled ? 'border-start border-success border-3' : '' }}">
                    
                    {{-- Header: Nama Bidang (Lega & Wrap) --}}
                    <div class="card-header pb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <span class="badge bg-label-primary text-wrap p-2" 
                                  style="font-size: 10px; line-height: 1.6; text-align: left; display: block; width: 85%; min-height: 42px; letter-spacing: 0.3px;">
                                <i class="bx bx-buildings me-1"></i> {{ $t->bidang }}
                            </span>
                            @if($isEnrolled)
                                <span class="badge badge-center rounded-pill bg-success" title="Anda sudah terdaftar">
                                    <i class="bx bx-check"></i>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body pt-2">
                        <h5 class="card-title fw-bold {{ $isFinished ? 'text-muted' : 'text-dark' }} mb-3">
                            {{ $t->nama_pelatihan }}
                        </h5>
                        
                        {{-- Info JP & Waktu --}}
                        <div class="d-flex justify-content-between mb-3 bg-white p-2 rounded border border-light shadow-xs">
                            <div class="text-center flex-fill border-end">
                                <small class="text-muted d-block x-small">DURASI</small>
                                <span class="fw-bold text-primary">{{ $t->jp }} JP</span>
                            </div>
                            <div class="text-center flex-fill">
                                <small class="text-muted d-block x-small">ANGKATAN</small>
                                <span class="fw-bold text-dark">{{ $t->angkatan }}</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block mb-1"><i class="bx bx-calendar me-1"></i> Jadwal:</small>
                            <span class="fw-semibold small {{ $isFinished ? 'text-decoration-line-through text-muted' : 'text-dark' }}">
                                {{ \Carbon\Carbon::parse($t->tgl_mulai)->translatedFormat('d M') }} - 
                                {{ \Carbon\Carbon::parse($t->tgl_selesai)->translatedFormat('d M Y') }}
                            </span>
                        </div>

                        {{-- Progres Kuota --}}
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="fw-bold">Pendaftar</small>
                                <small class="fw-bold text-primary">{{ $t->participants_count }} / {{ $t->jumlah_peserta }}</small>
                            </div>
                            
                            @php
                                $barColor = $isFinished ? 'bg-secondary' : ($percent >= 100 ? 'bg-danger' : ($percent >= 80 ? 'bg-warning' : 'bg-success'));
                            @endphp
                            
                            <div class="progress shadow-none" style="height: 8px; background-color: #eee;">
                                <div class="progress-bar progress-bar-striped {{ $barColor }} animate-progress" 
                                     role="progressbar" 
                                     style="width: {{ $percent }}%"></div>
                            </div>

                            @if($isFinished)
                                <small class="text-danger mt-2 d-block fw-bold small text-center bg-label-danger rounded py-1">
                                    <i class="bx bx-lock-alt me-1"></i> PENDAFTARAN DITUTUP
                                </small>
                            @elseif($isEnrolled)
                                <small class="text-success mt-2 d-block fw-bold small text-center bg-label-success rounded py-1">
                                    <i class="bx bx-check-double me-1"></i> ANDA SUDAH TERDAFTAR
                                </small>
                            @elseif($percent >= 100)
                                <small class="text-danger mt-2 d-block fw-bold small text-center bg-label-danger rounded py-1">
                                    <i class="bx bx-error-circle me-1"></i> KUOTA PENUH
                                </small>
                            @else
                                <small class="text-muted mt-1 d-block italic text-end" style="font-size: 11px;">
                                    Tersisa {{ $t->jumlah_peserta - $t->participants_count }} slot lagi
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="card-footer border-top bg-transparent pt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge {{ $t->model == 'blended' ? 'bg-label-warning' : 'bg-label-info' }} rounded-pill">
                                {{ strtoupper($t->model) }}
                            </span>

                            @if($isFinished)
                                <button class="btn btn-secondary btn-sm px-4 shadow-none opacity-50" disabled>Selesai</button>
                            @elseif($isEnrolled)
                                {{-- UX: Tombol Lihat Detail lebih menonjol jika sudah terdaftar --}}
                                <a href="{{ route('participant.training.show', $t->id) }}" class="btn btn-success btn-sm px-4 shadow">
                                    <i class="bx bx-right-arrow-alt me-1"></i> Lihat Detail
                                </a>
                            @elseif($percent >= 100)
                                <button class="btn btn-outline-danger btn-sm px-4" disabled>Penuh</button>
                            @else
                                <button class="btn btn-primary btn-sm px-4 shadow-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalEnroll{{ $t->id }}">
                                    Ikuti Pelatihan
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Modal Pendaftaran tetap berfungsi sama --}}
                <div class="modal fade" id="modalEnroll{{ $t->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <form action="{{ route('participant.training.join', $t->id) }}" method="POST" class="modal-content">
                            @csrf
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title">Konfirmasi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center py-4">
                                <div class="avatar avatar-xl bg-label-primary mx-auto mb-3">
                                    <i class="bx bx-key"></i>
                                </div>
                                <h6 class="fw-bold mb-3">{{ $t->nama_pelatihan }}</h6>
                                <p class="text-muted small">Masukkan 6 digit <b>Kode Undangan</b>:</p>
                                <input type="text" name="invitation_code" class="form-control form-control-lg text-center fw-bold border-primary" placeholder="KODE" maxlength="6" style="letter-spacing: 5px; text-transform: uppercase;" required>
                            </div>
                            <div class="modal-footer border-top p-2">
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary btn-sm px-4">Daftar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="mb-3">
                    <i class="bx bx-search-alt display-1 text-light"></i>
                </div>
                <h5 class="text-muted mt-3">Tidak ditemukan pelatihan dengan kata kunci "{{ $search }}"</h5>
                <a href="{{ route('participant.trainings') }}" class="btn btn-primary btn-sm mt-2 shadow">Lihat Semua Pelatihan</a>
            </div>
        @endforelse
    </div>
</div>

<style>
    .transition-all { transition: all 0.3s ease-in-out; }
    .hover-shadow:hover { 
        transform: translateY(-8px); 
        box-shadow: 0 12px 20px rgba(105, 108, 255, 0.15) !important;
        border-color: #696cff !important;
    }
    .card-title {
        min-height: 48px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
    }
    .grayscale { filter: grayscale(1); }
    .x-small { font-size: 9px; letter-spacing: 0.5px; }
    .shadow-xs { box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .animate-progress { transition: width 1s ease-in-out; }
</style>
@endsection