@extends('layouts.master')

@section('title', 'Helpdesk Kendala Akses')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Sistem /</span> Helpdesk Kendala Akses</h4>
            <p class="text-muted mb-0 small">Daftar tiket laporan kendala login internal yang masuk dari halaman autentikasi</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('helpdesk.index') }}" class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Cari kode tiket, nama, NIP, atau email..." value="{{ $search }}">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="open" {{ $status == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="diproses" {{ $status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                        <option value="selesai" {{ $status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode & Waktu</th>
                        <th>Pelapor</th>
                        <th>Kendala</th>
                        <th>Status</th>
                        <th>Tindak Lanjut Superadmin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td>
                                <span class="fw-bold text-primary">{{ $ticket->ticket_code }}</span><br>
                                <small class="text-muted">{{ $ticket->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $ticket->nama }}</div>
                                <small class="text-muted">{{ $ticket->email }}</small>
                                @if($ticket->nip_nik)
                                    <br><small class="text-info">NIP: {{ $ticket->nip_nik }}</small>
                                @endif
                            </td>
                            <td style="max-width: 280px;">
                                <span class="badge bg-label-info mb-1">{{ $ticket->kategori_kendala }}</span>
                                <div class="small text-wrap">{{ $ticket->deskripsi }}</div>
                            </td>
                            <td>
                                @php
                                    $badge = match($ticket->status) {
                                        'selesai' => 'bg-label-success',
                                        'diproses' => 'bg-label-warning',
                                        default => 'bg-label-danger',
                                    };
                                @endphp
                                <span class="badge {{ $badge }} text-uppercase">{{ $ticket->status }}</span>
                            </td>
                            <td style="min-width: 240px;">
                                <form action="{{ route('helpdesk.update-status', $ticket) }}" method="POST" class="d-flex flex-column gap-1">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="diproses" {{ $ticket->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                        <option value="selesai" {{ $ticket->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    </select>
                                    <input type="text" name="catatan_admin" class="form-control form-control-sm" placeholder="Catatan tindak lanjut..." value="{{ $ticket->catatan_admin }}">
                                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada tiket helpdesk kendala akses.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $tickets->links() }}
        </div>
    </div>
</div>
@endsection