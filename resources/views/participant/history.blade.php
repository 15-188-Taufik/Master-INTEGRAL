@extends('layouts.master')

@section('title', 'Riwayat & Capaian JP')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Akun /</span> Riwayat Pelatihan & Capaian JP
            </h4>
            <p class="text-muted mb-0 small">Daftar riwayat seluruh pelatihan yang pernah diikuti dan akumulasi jam pelajaran (JP)</p>
        </div>
        <div class="card p-3 border shadow-xs bg-light">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm bg-label-primary rounded">
                    <i class="bx bx-time fs-4"></i>
                </div>
                <div>
                    <small class="text-muted d-block" style="font-size: 10px;">TOTAL KOMPETENSI</small>
                    <span class="fw-bold text-primary fs-6">{{ $totalJpLifetime ?? 0 }} JP</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Daftar Pelatihan Terdaftar</h5>
            <span class="badge bg-label-primary">NIP: {{ auth()->user()->nip_nik }}</span>
        </div>
        
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="35%">Nama Pelatihan</th>
                        <th width="25%">Penyelenggara</th>
                        <th width="10%">Durasi</th>
                        <th width="15%">Waktu Pelaksanaan</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($history as $index => $h)
                    @if($h->training)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="fw-bold text-dark">{{ $h->training->nama_pelatihan }}</span><br>
                            <span class="badge bg-label-info btn-xs text-uppercase">{{ $h->training->model }}</span>
                            <small class="text-muted ms-1">Angkatan {{ $h->training->angkatan }}</small>
                        </td>
                        <td class="text-wrap" style="max-width: 250px;">
                            <small class="text-muted">{{ $h->training->bidang }}</small>
                        </td>
                        <td>
                            <span class="badge bg-label-primary fw-bold">{{ $h->training->jp }} JP</span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <small class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($h->training->tgl_mulai)->format('d/m/Y') }}</small>
                                <small class="text-muted" style="font-size: 10px;">s.d {{ \Carbon\Carbon::parse($h->training->tgl_selesai)->format('d/m/Y') }}</small>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('participant.training.show', $h->training->id) }}" class="btn btn-sm btn-primary">
                                <i class="bx bx-show-alt me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bx bx-history display-1 mb-3 opacity-25"></i>
                                <h5>Belum Ada Riwayat Pelatihan</h5>
                                <p class="small">Anda belum terdaftar dalam pelatihan apapun.</p>
                                <a href="{{ route('participant.trainings') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bx bx-search me-1"></i> Cari Pelatihan Tersedia
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection