@extends('layouts.master')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Pelatihan /</span> Tambah Pelatihan {{ $model === 'blended' ? 'Blended Learning' : 'Standar' }}
</h4>

<form action="{{ route('trainings.store') }}" method="POST">
    @csrf
    <input type="hidden" name="model" value="{{ $model }}">
    
    <div class="row">
        <!-- Panel Kiri: Data Umum -->
        <div class="col-md-12 col-lg-7">
            <div class="card mb-4">
                <h5 class="card-header">Informasi Umum</h5>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Pelatihan</label>
                        <input type="text" name="nama_pelatihan" class="form-control" placeholder="Masukkan nama pelatihan..." required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Penyelenggara Pelatihan</label>
                        <select name="bidang" class="form-select" required>
                            <option value="Bidang Sertifikasi Kompetensi & Pengelolaan Kelembagaan">Bidang Sertifikasi Kompetensi & Pengelolaan Kelembagaan</option>
                            <option value="Bidang Pengembangan Kompetensi Teknis Inti">Bidang Pengembangan Kompetensi Teknis Inti</option>
                            <option value="Bidang Pengembangan Kompetensi Teknis Umum">Bidang Pengembangan Kompetensi Teknis Umum</option>
                            <option value="Bidang Pengembangan Kompetensi Manajerial">Bidang Pengembangan Kompetensi Manajerial</option>
                        </select>
                    </div>

                    @if($model === 'standar')
                    <!-- KHUSUS STANDAR: Pilih Metode Langsung -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">METODE PELATIHAN</label>
                        <select name="metode" class="form-select border-primary" required>
                            <option value="klasikal">Klasikal (Tatap Muka / Full Offline)</option>
                            <option value="full learning">Full Learning (Daring / Full Online)</option>
                        </select>
                        <div class="form-text text-muted">Metode ini akan menentukan set pertanyaan monitoring & evaluasi yang muncul.</div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Angkatan</label>
                            <input type="text" name="angkatan" class="form-control" placeholder="I / 2024">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jumlah Peserta</label>
                            <input type="number" name="jumlah_peserta" class="form-control" placeholder="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Total JP</label>
                            <input type="number" name="jp" class="form-control" placeholder="0">
                        </div>
                    </div>
                </div>
            </div>

            @if($model === 'blended')
            <!-- KHUSUS BLENDED: PENGATURAN TAHAPAN DINAMIS -->
            <div class="card mb-4 border-start border-primary border-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold"><i class="bx bx-layer me-2"></i>PENGATURAN TAHAPAN JADWAL</h5>
                    <button type="button" class="btn btn-primary btn-sm" id="add-stage">
                        <i class="bx bx-plus me-1"></i> Tambah Tahap
                    </button>
                </div>
                <div class="card-body">
                    <div id="stage-container">
                        <!-- Baris Tahapan Default untuk Blended -->
                        <div class="stage-row border rounded p-3 mb-3 bg-light">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">NAMA TAHAPAN</label>
                                    <input type="text" name="stages[0][nama]" class="form-control" value="Pembelajaran Mandiri" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">METODE</label>
                                    <select name="stages[0][metode]" class="form-select">
                                        <option value="full learning">Full Learning</option>
                                        <option value="blended">Blended</option>
                                        <option value="klasikal">Klasikal</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold">MULAI</label>
                                    <input type="date" name="stages[0][mulai]" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold">AKHIR</label>
                                    <input type="date" name="stages[0][selesai]" class="form-control" required>
                                </div>
                                <div class="col-md-1 text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm border-0 remove-stage">
                                        <i class="bx bx-trash bx-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Panel Kanan: Lokasi & Waktu Global -->
        <div class="col-md-12 col-lg-5">
            <div class="card mb-4 border-2 border-primary">
                <h5 class="card-header text-primary">Waktu & Lokasi Global</h5>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Lokasi Pelatihan</label>
                        <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Kampus LAN / Zoom" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label text-primary fw-bold small">TGL MULAI GLOBAL</label>
                            <input type="date" name="tgl_mulai" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-danger fw-bold small">TGL SELESAI GLOBAL</label>
                            <input type="date" name="tgl_selesai" class="form-control" required>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-grid mt-2">
                <button type="submit" class="btn btn-lg btn-primary shadow-lg">
                    <i class="bx bx-save me-2"></i>Simpan Data Pelatihan
                </button>
            </div>
        </div>
    </div>
</form>

@if($model === 'blended')
@push('js')
<script>
    $(document).ready(function() {
        let stageCount = 1;
        $('#add-stage').click(function() {
            let html = `
            <div class="stage-row border rounded p-3 mb-3 bg-light animate__animated animate__fadeIn">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">NAMA TAHAPAN</label>
                        <input type="text" name="stages[${stageCount}][nama]" class="form-control" placeholder="Nama Tahapan" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">METODE</label>
                        <select name="stages[${stageCount}][metode]" class="form-select">
                            <option value="full learning">Full Learning</option>
                            <option value="blended">Blended</option>
                            <option value="klasikal">Klasikal</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">MULAI</label>
                        <input type="date" name="stages[${stageCount}][mulai]" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">AKHIR</label>
                        <input type="date" name="stages[${stageCount}][selesai]" class="form-control" required>
                    </div>
                    <div class="col-md-1 text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm border-0 remove-stage">
                            <i class="bx bx-trash bx-sm"></i>
                        </button>
                    </div>
                </div>
            </div>`;
            $('#stage-container').append(html);
            stageCount++;
        });

        $(document).on('click', '.remove-stage', function() {
            $(this).closest('.stage-row').remove();
        });
    });
</script>
@endpush
@endif
@endsection