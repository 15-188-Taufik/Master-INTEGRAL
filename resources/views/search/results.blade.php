@extends('layouts.master')

@section('title', 'Hasil Pencarian Global')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Pencarian /</span> Hasil untuk: <span class="text-primary">"{{ $query }}"</span>
        </h4>
        <button onclick="window.history.back()" class="btn btn-sm btn-outline-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali
        </button>
    </div>

    <div class="row">
        <!-- SEKSI PELATIHAN -->
        <div class="col-12 mb-4">
            <div class="card shadow-none border">
                <div class="card-header bg-label-primary py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold"><i class="bx bx-collection me-2"></i>Data Pelatihan</h5>
                    <span class="badge bg-primary">{{ $trainings->count() }} Ditemukan</span>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Pelatihan</th>
                                <th>Model & Metode</th>
                                <th>Token</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trainings as $t)
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark">{{ $t->nama_pelatihan }}</span><br>
                                    <small class="text-muted">Angkatan {{ $t->angkatan }} &bull; {{ $t->jp }} JP</small>
                                </td>
                                <td>
                                    <span class="badge {{ $t->model == 'blended' ? 'bg-label-warning' : 'bg-label-info' }}">{{ strtoupper($t->model) }}</span>
                                    <small class="text-muted d-block mt-1">{{ strtoupper($t->metode) }}</small>
                                </td>
                                <td>
                                    <code class="text-primary fw-bold">{{ $t->invitation_code }}</code>
                                </td>
                                <td>
                                    @if($t->sisa_hari < 0)
                                        <span class="badge bg-label-danger">Selesai</span>
                                    @else
                                        <span class="badge bg-label-success">Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('trainings.manage', $t->id) }}" class="btn btn-sm btn-label-primary">
                                        Kelola
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada data pelatihan yang cocok.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SEKSI PESERTA -->
        <div class="col-12 mb-4">
            <div class="card shadow-none border">
                <div class="card-header bg-label-success py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-success fw-bold"><i class="bx bx-user me-2"></i>Data Peserta (Smart Linking)</h5>
                    <span class="badge bg-success">{{ $participants->count() }} Ditemukan</span>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nama & NIP</th>
                                <th>Instansi & Jabatan</th>
                                <th>Wilayah</th>
                                <th>Pelatihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($participants as $p)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded-circle bg-label-success">{{ substr($p->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <span class="fw-bold d-block text-dark">{{ $p->name }}</span>
                                            <code class="text-muted" style="font-size: 11px;">{{ $p->nip_nik }}</code>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">{{ $p->instansi }}</span>
                                    <small class="text-muted">{{ $p->jabatan }}</small>
                                </td>
                                <td>
                                    <span class="small text-dark">{{ $p->kabupaten_kota }}</span>
                                    <small class="text-muted d-block">{{ $p->provinsi }}</small>
                                </td>
                                <td>
                                    <small class="d-block fw-bold text-primary">{{ $p->training ? $p->training->nama_pelatihan : '-' }}</small>
                                    <small class="text-muted">Angkatan {{ $p->training ? $p->training->angkatan : '-' }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">Data peserta tidak ditemukan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection