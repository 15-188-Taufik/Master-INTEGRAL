@extends('layouts.auth')

@section('title', 'Helpdesk Kendala Akses Login')

@section('content')
<div class="container-xxl py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="mb-3">
                <a href="{{ route('login') }}" class="text-decoration-none small text-muted">
                    <i class="bx bx-chevron-left"></i> Kembali ke Halaman Login
                </a>
            </div>
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h4 class="text-white mb-1 fw-bold text-uppercase">Helpdesk Kendala Akses</h4>
                    <p class="mb-0 opacity-75 small">Laporkan kendala login Google OAuth, lupa password, atau penautan NIP akun Anda</p>
                </div>

                <div class="card-body p-4">
                    @if (session('success'))
                        <div class="alert alert-success border-0 shadow-sm mb-4">
                            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('helpdesk.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">NIP / NIK (Opsional)</label>
                                <input type="text" name="nip_nik" class="form-control" value="{{ old('nip_nik') }}" placeholder="Contoh: 19950303...">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email / Akun Google</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Kategori Kendala</label>
                                <select name="kategori_kendala" class="form-select" required>
                                    <option value="">-- Pilih Kendala --</option>
                                    <option value="Gagal Login Google OAuth" {{ old('kategori_kendala') == 'Gagal Login Google OAuth' ? 'selected' : '' }}>Gagal Login Google OAuth</option>
                                    <option value="Lupa Password Admin" {{ old('kategori_kendala') == 'Lupa Password Admin' ? 'selected' : '' }}>Lupa Password Admin</option>
                                    <option value="Akun Tidak Tertaut NIP" {{ old('kategori_kendala') == 'Akun Tidak Tertaut NIP' ? 'selected' : '' }}>Akun Tidak Tertaut NIP</option>
                                    <option value="Lainnya" {{ old('kategori_kendala') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Deskripsi Kendala</label>
                            <textarea name="deskripsi" rows="4" class="form-control" placeholder="Jelaskan kendala login yang Anda alami secara singkat..." required>{{ old('deskripsi') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow">
                            <i class="bx bx-send me-1"></i> KIRIM TIKET BANTUAN
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection