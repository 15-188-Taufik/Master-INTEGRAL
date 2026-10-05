@extends('layouts.master')

@section('title', 'Manajemen Peserta')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header dengan Tombol Aksi Terkelompok -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold py-3 mb-0">
                <span class="text-muted fw-light">Pelatihan /</span> Peserta: {{ $training->nama_pelatihan }}
            </h4>
        </div>
            <div class="d-flex flex-wrap gap-2">
            <!-- FORM PENCARIAN PESERTA -->
            <form action="{{ route('trainings.participants', $training->id) }}" method="GET" style="min-width: 250px;">
                <div class="input-group input-group-merge shadow-sm">
                    <span class="input-group-text" id="basic-addon-search31"><i class="bx bx-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari Nama / NIP..." 
                           aria-label="Search..." aria-describedby="basic-addon-search31" value="{{ $search ?? '' }}">
                    @if($search)
                        <a href="{{ route('trainings.participants', $training->id) }}" class="btn btn-outline-secondary px-2">
                            <i class="bx bx-x"></i>
                        </a>
                    @endif
                </div>
            </form>

            <a href="{{ route('trainings.manage', $training->id) }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Pengelolaan
            </a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdd">
                <i class="bx bx-plus me-1"></i> Tambah
            </button>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalImport">
                <i class="bx bx-file me-1"></i> Import
            </button>
        </div>
    </div>
    @if($search)
        <div class="mb-3 animate__animated animate__fadeIn">
            <small class="text-muted">Menampilkan hasil pencarian untuk: <span class="fw-bold text-primary">"{{ $search }}"</span></small>
        </div>
    @endif

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible border-0 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="bx bx-check-circle me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible border-0 shadow-sm" role="alert">
            <div class="d-flex">
                <i class="bx bx-error-circle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabel Peserta Modern -->
    <div class="card shadow-sm">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Peserta Pelatihan</h5>
            <span class="badge bg-label-primary">{{ count($participants) }} ORANG</span>
        </div>
        
        <div class="card shadow-none border">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover" style="table-layout: fixed; width: 100%;">
                    <thead>
                        <tr class="text-nowrap bg-light">
                            <th style="width: 170px;" class="fw-bold">NIP / NIK</th>
                            <th style="width: 220px;" class="fw-bold">NAMA LENGKAP</th>
                            <th style="width: 140px;" class="fw-bold">GENDER/STATUS</th>
                            <th style="width: 180px;" class="fw-bold">JABATAN</th>
                            <th style="width: 200px;" class="fw-bold">INSTANSI & WILAYAH</th>
                            <th style="width: 140px;" class="fw-bold">KONTAK</th>
                            <th style="width: 100px;" class="text-center fw-bold">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse($participants as $p)
                        <tr class="participant-row">
                            <!-- Kolom NIP dengan fitur Copy -->
                            <td class="align-top">
                                <div class="d-flex align-items-center">
                                    <code class="fw-bold text-danger me-2" id="nip-{{ $p->id }}" style="font-size: 0.8rem;">{{ $p->nip_nik }}</code>
                                    <button class="btn btn-xs btn-icon btn-outline-secondary border-0" 
                                            onclick="copyToClipboard('{{ $p->nip_nik }}', this)" 
                                            title="Salin NIP">
                                        <i class="bx bx-copy"></i>
                                    </button>
                                </div>
                            </td>

                            <!-- Kolom Nama dengan Avatar Inisial -->
                            <td class="align-top text-wrap">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs me-2">
                                        <span class="avatar-initial rounded-circle bg-label-primary" style="font-size: 10px;">
                                            {{ substr($p->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <span class="fw-bold text-dark" style="font-size: 0.85rem;">{{ strtoupper($p->name) }}</span>
                                </div>
                            </td>

                            <!-- Kolom Gender & Status Dinamis -->
                            <td class="align-top">
                                <small class="d-block mb-1 text-muted">
                                    <i class="bx {{ $p->gender == 'Laki-Laki' ? 'bx-male-sign text-info' : 'bx-female-sign text-danger' }} me-1"></i>{{ $p->gender }}
                                </small>
                                @php
                                    $statusColor = 'bg-label-secondary';
                                    if(str_contains(strtoupper($p->status_kepegawaian), 'PNS')) $statusColor = 'bg-label-success';
                                    if(str_contains(strtoupper($p->status_kepegawaian), 'PPPK')) $statusColor = 'bg-label-warning';
                                    if(str_contains(strtoupper($p->status_kepegawaian), 'ASN')) $statusColor = 'bg-label-primary';
                                @endphp
                                <span class="badge {{ $statusColor }} btn-xs fw-bold" style="font-size: 0.6rem;">
                                    {{ $p->status_kepegawaian }}
                                </span>
                            </td>
                            
                            <!-- Kolom Jabatan -->
                            <td class="align-top text-wrap">
                                <div style="line-height: 1.3; font-size: 0.8rem;" class="text-dark">
                                    {{ $p->jabatan }}
                                </div>
                            </td>

                            <!-- Kolom Instansi & Wilayah -->
                            <td class="align-top text-wrap">
                                <div class="fw-bold text-dark mb-1" style="font-size: 0.8rem;">{{ $p->instansi }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">
                                    <i class="bx bx-map-pin me-1" style="font-size: 9px;"></i>{{ $p->kabupaten_kota }}, {{ $p->provinsi }}
                                </small>
                            </td>

                            <!-- Kolom WhatsApp -->
                            <td class="align-top">
                                @if($p->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $p->phone) }}" 
                                    target="_blank" 
                                    class="btn btn-sm btn-outline-success rounded-pill px-3 w-100"
                                    style="font-size: 0.7rem;">
                                        <i class="bx bxl-whatsapp me-1"></i> WhatsApp
                                    </a>
                                @else
                                    <span class="text-light small italic">N/A</span>
                                @endif
                            </td>

                            <!-- Kolom Aksi -->
                            <td class="align-top text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button class="btn btn-sm btn-icon btn-label-warning" 
                                        onclick="editParticipant({{ json_encode($p) }})"
                                        data-bs-toggle="modal" data-bs-target="#modalEdit"
                                        title="Ubah Data">
                                        <i class="bx bx-edit"></i>
                                    </button>

                                    <form action="{{ route('participants.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus peserta ini?')">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="Hapus">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <img src="{{ asset('assets/img/illustrations/empty-box.png') }}" width="100" class="mb-3 opacity-50">
                                <p class="text-muted fw-light">Belum ada data peserta yang terdaftar.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('participants.store', $training->id) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Tambah Peserta Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">NIP / NIK</label>
                    <input type="text" name="nip_nik" class="form-control" placeholder="Masukkan NIP/NIK" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nomor WhatsApp</label>
                    <input type="number" name="phone" class="form-control" placeholder="628..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama tanpa gelar disarankan" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Jabatan</label>
                        <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Analis SDM" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Instansi</label>
                        <input type="text" name="instansi" class="form-control" placeholder="Contoh: BPSDM" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="Laki-Laki">Laki-Laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status Kepegawaian</label>
                        <select name="status_kepegawaian" class="form-select" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="PNS">PNS</option>
                            <option value="PPPK">PPPK</option>
                            <option value="Non-ASN">Non-ASN</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Provinsi</label>
                        <select id="add_provinsi" name="provinsi" class="form-select border-primary" required>
                            <option value="">Memuat Provinsi...</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Kabupaten / Kota</label>
                        <select id="add_kabupaten" name="kabupaten_kota" class="form-select border-primary" required disabled>
                            <option value="">Pilih Provinsi Dahulu</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top p-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL IMPORT EXCEL -->
<div class="modal fade" id="modalImport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('participants.import', $training->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Import Peserta via Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info shadow-none">
                    <small class="text-primary fw-bold d-block mb-1">PETUNJUK EXCEL:</small>
                    <ul class="small mb-0">
                        <li>Gunakan dropdown yang tersedia pada kolom <strong>Gender, Status, dan Provinsi</strong>.</li>
                        <li>Awali NIP dengan tanda petik (<strong>'</strong>).</li>
                        <li>Nomor HP awali dengan <strong>08</strong> atau <strong>62</strong>.</li>
                    </ul>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih File Excel (.xlsx / .xls)</label>
                    <input type="file" name="file" class="form-control" accept=".xlsx, .xls" required>
                </div>
            </div>
            <div class="modal-footer border-top p-3">
                <a href="{{ route('participants.template') }}" class="btn btn-outline-secondary">
                    <i class="bx bx-download me-1"></i> Template
                </a>
                <button type="submit" class="btn btn-success">Mulai Import</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT PESERTA -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="" method="POST" id="formEditParticipant" class="modal-content">
            @csrf 
            @method('PUT')
            <div class="modal-header border-bottom">
                <h5 class="modal-title">Edit Data Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">NIP / NIK</label>
                    <input type="text" name="nip_nik" id="edit_nip" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nomor WhatsApp</label>
                    <input type="number" name="phone" id="edit_phone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Lengkap</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Jabatan</label>
                        <input type="text" name="jabatan" id="edit_jabatan" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Instansi</label>
                        <input type="text" name="instansi" id="edit_instansi" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Gender</label>
                        <select name="gender" id="edit_gender" class="form-select" required>
                            <option value="Laki-Laki">Laki-Laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Status Kepegawaian</label>
                        <input type="text" name="status_kepegawaian" id="edit_status" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Provinsi</label>
                        <select id="edit_provinsi" name="provinsi" class="form-select border-primary" required>
                            {{-- Akan diisi via JS --}}
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Kabupaten / Kota</label>
                        <select id="edit_kabupaten" name="kabupaten_kota" class="form-select border-primary" required>
                            {{-- Akan diisi via JS --}}
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top p-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Update Data</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('css')
<style>
    /* Paksa scroll horizontal muncul di dalam kartu, bukan di seluruh halaman */
    .table-responsive {
        overflow-x: auto !important;
        scrollbar-width: thin; /* Untuk Firefox */
    }

    /* Mempercantik scrollbar untuk Chrome/Webkit */
    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #d9dee3;
        border-radius: 10px;
    }

    /* Paksa teks turun ke bawah jika kolom terbatas */
    .text-wrap {
        white-space: normal !important;
        word-wrap: break-word;
    }

    .participant-row:hover {
        background-color: #fcfcff !important;
    }

    /* Style tombol label khas Sneat */
    .btn-label-warning { background: #fff3e0; color: #ffab00; }
    .btn-label-danger { background: #ffebee; color: #ff3e1d; }
</style>
@endpush

@push('js')
<script>
    const apiProvinsi = "https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json";
    const apiKabupaten = "https://www.emsifa.com/api-wilayah-indonesia/api/regencies/";
    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const originalIcon = btn.innerHTML;
            btn.innerHTML = '<i class="bx bx-check text-success"></i>';
            setTimeout(() => {
                btn.innerHTML = originalIcon;
            }, 1500);
        });
    }
    $(document).ready(function() {
        // --- LOGIKA UNTUK MODAL TAMBAH ---
        fetch(apiProvinsi)
            .then(response => response.json())
            .then(provinces => {
                let options = '<option value="">-- Pilih Provinsi --</option>';
                provinces.forEach(p => {
                    options += `<option data-id="${p.id}" value="${p.name}">${p.name}</option>`;
                });
                $('#add_provinsi, #edit_provinsi').html(options);
            });

        $('#add_provinsi').change(function() {
            const provinceId = $(this).find(':selected').data('id');
            const kabSelect = $('#add_kabupaten');
            
            if (provinceId) {
                kabSelect.prop('disabled', false).html('<option value="">Memuat...</option>');
                fetch(`${apiKabupaten}${provinceId}.json`)
                    .then(response => response.json())
                    .then(regencies => {
                        let opts = '<option value="">-- Pilih Kabupaten/Kota --</option>';
                        regencies.forEach(r => { opts += `<option value="${r.name}">${r.name}</option>`; });
                        kabSelect.html(opts);
                    });
            } else {
                kabSelect.prop('disabled', true).html('<option value="">Pilih Provinsi Dahulu</option>');
            }
        });

        // --- LOGIKA UNTUK MODAL EDIT ---
        window.editParticipant = function(data) {
            const url = "{{ url('participants') }}/" + data.id;
            $('#formEditParticipant').attr('action', url);

            // Isi field standar
            $('#edit_nip').val(data.nip_nik);
            $('#edit_phone').val(data.phone);
            $('#edit_name').val(data.name);
            $('#edit_jabatan').val(data.jabatan);
            $('#edit_instansi').val(data.instansi);
            $('#edit_gender').val(data.gender);
            $('#edit_status').val(data.status_kepegawaian);

            // Handle Dropdown Wilayah di Edit (Trickier)
            $('#edit_provinsi').val(data.provinsi).trigger('change');
            
            // Kita perlu fetch kabupaten secara manual untuk mode edit agar nama kota muncul
            // Cari ID provinsi berdasarkan nama yang tersimpan
            fetch(apiProvinsi)
                .then(res => res.json())
                .then(provinces => {
                    const prov = provinces.find(p => p.name === data.provinsi);
                    if (prov) {
                        fetch(`${apiKabupaten}${prov.id}.json`)
                            .then(res => res.json())
                            .then(regencies => {
                                let opts = '';
                                regencies.forEach(r => {
                                    const selected = (r.name === data.kabupaten_kota) ? 'selected' : '';
                                    opts += `<option value="${r.name}" ${selected}>${r.name}</option>`;
                                });
                                $('#edit_kabupaten').html(opts);
                            });
                    }
                });
        };
    });
</script>
@endpush