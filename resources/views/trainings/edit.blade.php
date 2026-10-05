@extends('layouts.master')

@section('content')
<h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Pelatihan /</span> Edit Pelatihan
</h4>

<form action="{{ route('trainings.update', $training->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-12 col-lg-7">
            <div class="card mb-4">
                <h5 class="card-header">Informasi Umum</h5>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Pelatihan</label>
                        <input type="text" name="nama_pelatihan" class="form-control" value="{{ $training->nama_pelatihan }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Penyelenggara Pelatihan</label>
                        <select name="bidang" class="form-select" required>
                            @php
                                $bidangs = [
                                    'Bidang Sertifikasi Kompetensi & Pengelolaan Kelembagaan',
                                    'Bidang Pengembangan Kompetensi Teknis Inti',
                                    'Bidang Pengembangan Kompetensi Teknis Umum',
                                    'Bidang Pengembangan Kompetensi Manajerial'
                                ];
                            @endphp
                            @foreach($bidangs as $b)
                                <option value="{{ $b }}" {{ $training->bidang == $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    @if($training->model === 'standar')
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">METODE PELATIHAN</label>
                        <select name="metode" class="form-select border-primary" required>
                            <option value="klasikal" {{ $training->metode == 'klasikal' ? 'selected' : '' }}>Klasikal</option>
                            <option value="full learning" {{ $training->metode == 'full learning' ? 'selected' : '' }}>Full Learning</option>
                        </select>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Angkatan</label>
                            <input type="text" name="angkatan" class="form-control" value="{{ $training->angkatan }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jumlah Peserta</label>
                            <input type="number" name="jumlah_peserta" class="form-control" value="{{ $training->jumlah_peserta }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Total JP</label>
                            <input type="number" name="jp" class="form-control" value="{{ $training->jp }}">
                        </div>
                    </div>
                </div>
            </div>

            @if($training->model === 'blended')
            <div class="card mb-4 border-start border-primary border-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold">PENGATURAN TAHAPAN</h5>
                    <button type="button" class="btn btn-primary btn-sm" id="add-stage"><i class="bx bx-plus"></i></button>
                </div>
                <div class="card-body">
                    <div id="stage-container">
                        @foreach($training->stages as $index => $st)
                        <div class="stage-row border rounded p-3 mb-3 bg-light">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">NAMA TAHAPAN</label>
                                    <input type="text" name="stages[{{ $index }}][nama]" class="form-control" value="{{ $st->nama_tahapan }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold">METODE</label>
                                    <select name="stages[{{ $index }}][metode]" class="form-select">
                                        <option value="full learning" {{ $st->metode == 'full learning' ? 'selected' : '' }}>Full Learning</option>
                                        <option value="blended" {{ $st->metode == 'blended' ? 'selected' : '' }}>Blended</option>
                                        <option value="klasikal" {{ $st->metode == 'klasikal' ? 'selected' : '' }}>Klasikal</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold">MULAI</label>
                                    <input type="date" name="stages[{{ $index }}][mulai]" class="form-control" value="{{ $st->tgl_mulai }}" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold">AKHIR</label>
                                    <input type="date" name="stages[{{ $index }}][selesai]" class="form-control" value="{{ $st->tgl_selesai }}" required>
                                </div>
                                <div class="col-md-1 text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm border-0 remove-stage"><i class="bx bx-trash"></i></button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-12 col-lg-5">
            <div class="card mb-4 border-2 border-primary">
                <h5 class="card-header text-primary text-uppercase">Waktu & Lokasi Global</h5>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" value="{{ $training->lokasi }}" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small">TGL MULAI GLOBAL</label>
                            <input type="date" name="tgl_mulai" class="form-control" value="{{ $training->tgl_mulai }}" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small">TGL SELESAI GLOBAL</label>
                            <input type="date" name="tgl_selesai" class="form-control" value="{{ $training->tgl_selesai }}" required>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-lg btn-primary w-100">Simpan Perubahan</button>
        </div>
    </div>
</form>
@endsection