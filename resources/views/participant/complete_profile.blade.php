@extends('layouts.auth')

@section('title', 'Lengkapi Profil Peserta')

@section('content')
<div class="container-xxl py-5">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8">
            <div class="card shadow-lg border-0">
                <!-- Header Branding -->
                <div class="card-header bg-primary text-white text-center py-4">
                    <div class="mb-2">
                        <img src="https://res.cloudinary.com/dnwyqw6gn/image/upload/v1786770700/Integral_1_ykmzxx.png" alt="Integral Logo" style="width: 50px; filter: brightness(0) invert(1);">
                    </div>
                    <h4 class="text-white mb-1 fw-bold text-uppercase">Lengkapi Profil Peserta</h4>
                    <p class="mb-0 opacity-75 small">Silakan lengkapi data identitas Anda untuk integrasi sistem INTEGRAL</p>
                </div>

                <div class="card-body p-4">
                    <!-- Alert Error Validasi -->
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <div class="fw-bold mb-1"><i class="bx bx-error-circle me-1"></i> Gagal menyimpan:</div>
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('participant.profile.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Identitas Utama -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">NIP / NIK (Wajib)</label>
                                <input type="text" name="nip_nik" class="form-control border-primary" 
                                       placeholder="Contoh: 19950303..." value="{{ old('nip_nik', auth()->user()->nip_nik) }}" required>
                                <div class="form-text small text-info">Gunakan NIP asli Anda untuk sinkronisasi riwayat pelatihan.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nomor WhatsApp</label>
                                <input type="number" name="whatsapp" class="form-control border-primary" 
                                       placeholder="62812345678" value="{{ old('whatsapp', auth()->user()->whatsapp) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Gender & Status -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Gender</label>
                                <select name="gender" class="form-select border-primary" required>
                                    <option value="">-- Pilih Gender --</option>
                                    <option value="Laki-Laki" {{ old('gender', auth()->user()->gender) == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                    <option value="Perempuan" {{ old('gender', auth()->user()->gender) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Status Kepegawaian</label>
                                <select name="status_kepegawaian" class="form-select border-primary" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="PNS" {{ old('status_kepegawaian') == 'PNS' ? 'selected' : '' }}>PNS</option>
                                    <option value="PPPK" {{ old('status_kepegawaian') == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                                    <option value="Non-ASN" {{ old('status_kepegawaian') == 'Non-ASN' ? 'selected' : '' }}>Non-ASN</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Jabatan & Instansi -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Jabatan Saat Ini</label>
                                <input type="text" name="jabatan" class="form-control border-primary" 
                                       placeholder="Contoh: Analis SDM Aparatur" value="{{ old('jabatan') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Instansi / Unit Kerja</label>
                                <input type="text" name="instansi" class="form-control border-primary" 
                                       placeholder="Contoh: BPSDM Provinsi Jawa Barat" value="{{ old('instansi') }}" required>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="text-primary mb-3 fw-bold"><i class="bx bx-map me-1"></i>Wilayah Domisili / Kerja</h6>
                        
                        <div class="row">
                            <!-- Dropdown Tree Model: Provinsi -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Provinsi</label>
                                <select id="provinsi" name="provinsi" class="form-select border-primary" required>
                                    <option value="">Memuat Provinsi...</option>
                                </select>
                            </div>
                            <!-- Dropdown Tree Model: Kabupaten/Kota -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kabupaten / Kota</label>
                                <select id="kabupaten" name="kabupaten_kota" class="form-select border-primary" required disabled>
                                    <option value="">Pilih Provinsi Terlebih Dahulu</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100 shadow">
                                <i class="bx bx-save me-1"></i> SIMPAN PROFIL & LANJUTKAN
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer bg-light text-center py-3 border-top">
                    <small class="text-muted">INTEGRAL © {{ date('Y') }} | BPSDM Provinsi Jawa Barat</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT DROPDOWN WILAYAH INDONESIA --}}
<script>
    const provSelect = document.getElementById('provinsi');
    const kabSelect = document.getElementById('kabupaten');

    // 1. Load Semua Provinsi
    fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json`)
    .then(response => response.json())
    .then(provinces => {
        let options = '<option value="">-- Pilih Provinsi --</option>';
        provinces.forEach(item => {
            options += `<option data-id="${item.id}" value="${item.name}">${item.name}</option>`;
        });
        provSelect.innerHTML = options;
    });

    // 2. Load Kabupaten berdasarkan ID Provinsi (Tree Model)
    provSelect.addEventListener('change', function() {
        const provinceId = this.options[this.selectedIndex].getAttribute('data-id');
        
        if (provinceId) {
            kabSelect.disabled = false;
            kabSelect.innerHTML = '<option value="">Memuat Kabupaten/Kota...</option>';
            
            fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`)
            .then(response => response.json())
            .then(regencies => {
                let options = '<option value="">-- Pilih Kabupaten/Kota --</option>';
                regencies.forEach(item => {
                    options += `<option value="${item.name}">${item.name}</option>`;
                });
                kabSelect.innerHTML = options;
            });
        } else {
            kabSelect.disabled = true;
            kabSelect.innerHTML = '<option value="">Pilih Provinsi Terlebih Dahulu</option>';
        }
    });
</script>

<style>
    body { background: #f5f5f9; }
    .form-select-lg { font-size: 1rem; }
</style>
@endsection