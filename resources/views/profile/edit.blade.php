@extends('layouts.master')

@section('title', 'Pengaturan Profil')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Akun /</span> Pengaturan Profil</h4>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            {{-- CARD DETAIL PROFIL --}}
            <div class="card mb-4">
                <h5 class="card-header">Detail Profil</h5>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="card-body">
                        <div class="d-flex align-items-start align-items-sm-center gap-4">
                            @if($user->profile_photo)
                                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="user-avatar" class="d-block rounded shadow" height="100" width="100" id="uploadedAvatar" style="object-fit: cover;" />
                            @else
                                <div class="avatar avatar-xl me-2">
                                    <span class="avatar-initial rounded bg-label-primary" style="font-size: 40px;">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif

                            <div class="button-wrapper">
                                <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                    <span class="d-none d-sm-block text-uppercase small fw-bold">Ganti Foto</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                    <input type="file" id="upload" name="profile_photo" class="account-file-input" hidden accept="image/png, image/jpeg" />
                                </label>
                                <p class="text-muted mb-0 small">Hanya JPG atau PNG. Maksimal 2MB.</p>
                            </div>
                        </div>
                    </div>
                    <hr class="my-0" />
                    <div class="card-body">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label fw-bold">Nama Lengkap</label>
                                <input class="form-control" type="text" name="name" value="{{ $user->name }}" required />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label fw-bold">WhatsApp</label>
                                <input class="form-control" type="text" name="whatsapp" value="{{ $user->whatsapp }}" required />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label fw-bold text-muted">Username (NIP/Email)</label>
                                <input class="form-control bg-light" type="text" value="{{ $user->username }}" disabled />
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label fw-bold text-muted">Bidang / Unit Kerja</label>
                                <input class="form-control bg-light" type="text" value="{{ $user->bidang }}" disabled />
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- CARD GANTI PASSWORD (VERSI SIMPLE) --}}
            <div class="card">
                <h5 class="card-header border-bottom mb-3">Keamanan Akun</h5>
                <div class="card-body">
                    <div class="alert alert-warning mb-4">
                        <i class="bx bx-info-circle me-1"></i>
                        Jika Anda lupa password lama, Anda dapat langsung memasukkan password baru di bawah ini.
                    </div>
                    
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row">
                            <div class="mb-3 col-md-6 form-password-toggle">
                                <label class="form-label fw-bold">Password Baru</label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control" type="password" name="new_password" placeholder="••••••••" required />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="mb-3 col-md-6 form-password-toggle">
                                <label class="form-label fw-bold">Konfirmasi Password Baru</label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control" type="password" name="new_password_confirmation" placeholder="••••••••" required />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-warning fw-bold shadow-sm">
                                <i class="bx bx-lock-open-alt me-1"></i> PERBARUI PASSWORD
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection