@extends('layouts.master')

@section('title', 'Log Aktivitas Admin (Audit Trail)')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Sistem /</span> Audit Trail & Log Aktivitas
            </h4>
            <p class="text-muted mb-0 small">Pencatatan riwayat aktivitas operasional admin, IP Address, dan browser perangkat</p>
        </div>

        <div>
            <form action="{{ route('activity-logs.index') }}" method="GET" style="min-width: 280px;">
                <div class="input-group input-group-merge shadow-sm">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Cari aktivitas, admin, atau IP..." 
                           value="{{ $search ?? '' }}">
                    @if(!empty($search))
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary px-2">
                            <i class="bx bx-x"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if(!empty($search))
        <div class="mb-3 animate__animated animate__fadeIn">
            <small class="text-muted">Hasil pencarian untuk: <span class="fw-bold text-primary">"{{ $search }}"</span></small>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Waktu</th>
                        <th>Pelaku (Admin)</th>
                        <th>Modul</th>
                        <th>Aktivitas</th>
                        <th>IP Address</th>
                        <th>Browser / Perangkat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>
                            <span class="fw-semibold text-dark">{{ $log->created_at->format('d/m/Y') }}</span>
                            <small class="text-muted d-block">{{ $log->created_at->format('H:i:s') }} WIB</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="badge badge-center rounded-pill bg-label-primary me-2">
                                    {{ $log->user ? substr($log->user->name, 0, 1) : '?' }}
                                </div>
                                <div>
                                    <span class="fw-bold text-dark">{{ $log->user->name ?? 'User Dihapus' }}</span>
                                    <small class="text-muted d-block" style="font-size: 11px;">
                                        {{ $log->user ? strtoupper(str_replace('_', ' ', $log->user->role)) : '-' }}
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-label-info">{{ $log->module }}</span>
                        </td>
                        <td class="text-wrap" style="max-width: 320px;">
                            <span class="text-dark">{{ $log->activity }}</span>
                        </td>
                        <td>
                            <code class="text-muted" style="font-size: 12px;">{{ $log->ip_address }}</code>
                        </td>
                        <td class="text-wrap" style="max-width: 250px;">
                            <small class="text-muted d-block text-truncate" style="max-width: 240px;" title="{{ $log->user_agent }}">
                                <i class="bx bx-devices me-1"></i> {{ $log->user_agent ?? 'N/A' }}
                            </small>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bx bx-info-circle fs-3 d-block mb-2"></i>
                            Belum ada catatan log aktivitas yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="card-footer border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Menampilkan {{ $logs->firstItem() }} s/d {{ $logs->lastItem() }} dari {{ $logs->total() }} log</small>
                <div>
                    {{ $logs->withQueryString()->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection