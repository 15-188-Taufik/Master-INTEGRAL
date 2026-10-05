<!DOCTYPE html>
<html lang="id" class="light-style">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Integral | BPSDM Jawa Barat</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}" />
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        html { scroll-behavior: smooth; }

        :root {
            --integral-primary: #696cff;
            --integral-dark: #233446;
            --integral-gradient: linear-gradient(135deg, #696cff 0%, #30336b 100%);
        }

        body {
            font-family: 'Public Sans', sans-serif;
            background-color: #fff;
            color: #566a7f;
        }

        .navbar-custom {
            padding: 15px 0;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1050;
            transition: all 0.3s ease;
        }

        .navbar-custom.scrolled {
            padding: 10px 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .hero-section {
            padding-top: 140px;
            padding-bottom: 80px;
            background: radial-gradient(circle at 10% 20%, rgba(105, 108, 255, 0.06) 0%, rgba(255, 255, 255, 1) 90.2%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.15;
            color: var(--integral-dark);
            margin-bottom: 1.5rem;
        }

        .hero-title span { color: var(--integral-primary); }

        .hero-img {
            max-width: 100%;
            height: auto;
            animation: floating 3.5s ease-in-out infinite;
            filter: drop-shadow(0 20px 30px rgba(105, 108, 255, 0.2));
        }

        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .feature-card {
            border: 1px solid #f0f2f4;
            padding: 40px;
            border-radius: 20px;
            background: #fff;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(105, 108, 255, 0.12);
            border-color: var(--integral-primary);
        }

        .icon-box {
            width: 70px;
            height: 70px;
            background: var(--integral-gradient);
            color: #fff;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 25px;
            box-shadow: 0 10px 20px rgba(105, 108, 255, 0.25);
        }

        .btn-integral {
            padding: 12px 32px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary-gradient {
            background: var(--integral-gradient);
            color: #fff;
            border: none;
            box-shadow: 0 4px 15px rgba(105, 108, 255, 0.4);
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(105, 108, 255, 0.5);
            color: #fff;
        }

        .section-tag {
            font-weight: 700;
            color: var(--integral-primary);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
            margin-bottom: 10px;
            display: block;
        }

        .footer {
            background-color: var(--integral-dark);
            padding: 70px 0 30px;
            color: #fff;
        }

        .icon-box-white {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            color: #fff;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .navbar-nav .nav-link {
            color: var(--integral-dark) !important;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: var(--integral-primary) !important;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-custom" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#top">
                <img src="https://res.cloudinary.com/dnwyqw6gn/image/upload/v1786770700/Integral_1_ykmzxx.png" alt="Logo" width="36" class="me-2">
                <span class="fw-bold text-dark h4 mb-0" style="letter-spacing: 1px;">INTEGRAL</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold px-3" href="#top">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold px-3" href="#features">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold px-3" href="#stats">Statistik</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold px-3" href="#pelatihan-aktif">Pelatihan Aktif</a>
                    </li>
                </ul>
                
                <div class="ms-auto">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-integral btn-primary-gradient shadow-sm">
                            <i class="bx bx-home-circle me-1"></i> Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-integral btn-primary-gradient shadow-sm">
                            <i class="bx bx-log-in-circle me-1"></i> Masuk Sistem
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero-section" id="top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 animate__animated animate__fadeInLeft">
                    <span class="section-tag">Identity & Data Master Pelatihan</span>
                    <h1 class="hero-title">BPSDM<span> Provinsi</span> Jawa Barat</h1>
                    <p class="fs-5 mb-5 text-muted" style="line-height: 1.6;">
                        Gerbang awal data (Single Input Data), manajemen akun & bidang, otomasi pendaftaran pelatihan via Token 6-Digit, serta tracking capaian hak 20 JP tahunan peserta.
                    </p>
                    <div class="d-flex gap-3 hero-btns">
                        <a href="{{ route('login') }}" class="btn btn-integral btn-primary-gradient btn-lg shadow">Mulai Sekarang</a>
                        <a href="#features" class="btn btn-outline-secondary btn-lg btn-integral">Pelajari Fitur <i class="bx bx-down-arrow-alt ms-1"></i></a>
                    </div>
                </div>
                <div class="col-lg-6 text-center mt-5 mt-lg-0 animate__animated animate__fadeInRight">
                    <img src="https://res.cloudinary.com/dnwyqw6gn/image/upload/v1786891751/pngegg_aolaux.png" 
                         alt="Integral Technology" class="hero-img img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section id="features" class="py-5 bg-white">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="section-tag">Ruang Lingkup Modul 1</span>
                <h2 class="fw-bold text-dark h1">Ekosistem Data Terintegrasi</h2>
                <p class="text-muted mx-auto" style="max-width: 650px;">
                    Membangun standardisasi Single Input Data, Role-Based Access Control, dan otomasi manajemen siklus pelatihan secara akurat.
                </p>
            </div>
            <div class="row g-4 mt-2">
                <!-- Fitur 1: Siklus Pelatihan -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box"><i class="bx bx-layer"></i></div>
                        <h4 class="fw-bold text-dark">Siklus Pelatihan & Trigger</h4>
                        <p class="text-muted">Mendukung model Standar & Blended Learning dinamis dengan kalkulasi otomatis jadwal sebar pasca (4 bulan teknis, 1 tahun manajerial).</p>
                    </div>
                </div>
                <!-- Fitur 2: Smart Linking -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box"><i class="bx bx-key"></i></div>
                        <h4 class="fw-bold text-dark">Smart Linking & Token</h4>
                        <p class="text-muted">Pendaftaran peserta instan melalui Token 6 Digit acak/unik dan import Excel dengan smart-linking akun berbasis NIP/Google Account.</p>
                    </div>
                </div>
                <!-- Fitur 3: Tracking JP & Pohon Wilayah -->
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box"><i class="bx bx-map-pin"></i></div>
                        <h4 class="fw-bold text-dark">Pohon Wilayah & 20 JP</h4>
                        <p class="text-muted">Pemantauan capaian minimal 20 JP kompetensi tahunan per peserta serta visualisasi hierarki sebaran wilayah Provinsi &rarr; Kab/Kota.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATS SECTION -->
    <section id="stats" class="py-5" style="background: linear-gradient(135deg, #696cff 0%, #30336b 100%); position: relative; overflow: hidden;">
        <div class="container py-5 position-relative" style="z-index: 2;">
            <div class="row text-center">
                <!-- Statistik 1: Total Pelatihan -->
                <div class="col-6 col-md-3 mb-4 mb-md-0 animate__animated animate__fadeInUp">
                    <div class="p-3">
                        <div class="icon-box-white mx-auto mb-3">
                            <i class="bx bx-collection"></i>
                        </div>
                        <h2 class="text-white fw-bold counter" data-target="{{ $stats['total_training'] }}">0</h2>
                        <p class="text-white opacity-75 text-uppercase small fw-bold">Total Pelatihan</p>
                    </div>
                </div>

                <!-- Statistik 2: Total Peserta -->
                <div class="col-6 col-md-3 mb-4 mb-md-0 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                    <div class="p-3">
                        <div class="icon-box-white mx-auto mb-3">
                            <i class="bx bx-group"></i>
                        </div>
                        <h2 class="text-white fw-bold counter" data-target="{{ $stats['total_participants'] }}">0</h2>
                        <p class="text-white opacity-75 text-uppercase small fw-bold">Peserta Terdaftar</p>
                    </div>
                </div>

                <!-- Statistik 3: Blended Learning -->
                <div class="col-6 col-md-3 mb-4 mb-md-0 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <div class="p-3">
                        <div class="icon-box-white mx-auto mb-3">
                            <i class="bx bx-sync"></i>
                        </div>
                        <h2 class="text-white fw-bold counter" data-target="{{ $stats['total_blended'] }}">0</h2>
                        <p class="text-white opacity-75 text-uppercase small fw-bold">Model Blended</p>
                    </div>
                </div>

                <!-- Statistik 4: Kepatuhan 20 JP -->
                <div class="col-6 col-md-3 mb-4 mb-md-0 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                    <div class="p-3">
                        <div class="icon-box-white mx-auto mb-3">
                            <i class="bx bx-time-five"></i>
                        </div>
                        <h2 class="text-white fw-bold"><span class="counter" data-target="{{ $stats['jp_compliance_rate'] }}">0</span><small class="fs-4">%</small></h2>
                        <p class="text-white opacity-75 text-uppercase small fw-bold">Capaian Target 20 JP</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PELATIHAN AKTIF HARI INI -->
    <section id="pelatihan-aktif" class="py-5 bg-light">
        <div class="container py-5">
            <div class="text-center mb-5 animate__animated animate__fadeIn">
                <span class="section-tag">Pelatihan Berjalan</span>
                <h2 class="fw-bold text-dark">Pelatihan Aktif Hari Ini</h2>
                <p class="text-muted">Pantau pelatihan yang sedang dalam masa pelaksanaan aktif</p>
                <div class="badge bg-label-primary px-3 py-2">
                    <i class="bx bx-calendar me-1"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </div>
            </div>

            <div class="row justify-content-center">
                @forelse($trainingsToday as $t)
                    <div class="col-lg-10 mb-4">
                        <div class="card border-0 shadow-sm overflow-hidden animate__animated animate__fadeInUp">
                            <div class="row g-0">
                                <!-- Sisi Kiri: Info Pelatihan -->
                                <div class="col-md-5 bg-primary p-4 text-white d-flex flex-column justify-content-center">
                                    <small class="text-uppercase opacity-75 fw-bold" style="letter-spacing: 1px;">{{ $t->bidang }}</small>
                                    <h4 class="text-white fw-bold mb-2">{{ $t->nama_pelatihan }}</h4>
                                    <div class="d-flex align-items-center mb-3">
                                        <span class="badge bg-white text-primary me-2">Angkatan {{ $t->angkatan }}</span>
                                        <span class="badge bg-label-white">{{ strtoupper($t->model) }}</span>
                                    </div>
                                    <hr class="opacity-25">
                                    <div class="small">
                                        <i class="bx bx-calendar me-1"></i> {{ \Carbon\Carbon::parse($t->tgl_mulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($t->tgl_selesai)->format('d/m/Y') }}
                                    </div>
                                </div>

                                <!-- Sisi Kanan: Detail & Token Akses -->
                                <div class="col-md-7 p-4 bg-white d-flex flex-column justify-content-between">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <span class="badge bg-label-primary mb-2">{{ $t->jp }} Jam Pelajaran (JP)</span>
                                            <div class="small text-muted mb-1">
                                                <i class="bx bx-map-pin text-danger me-1"></i> {{ $t->lokasi }}
                                            </div>
                                            <div class="small text-muted">
                                                <i class="bx bx-group text-success me-1"></i> {{ $t->participants_count }} / {{ $t->jumlah_peserta }} Peserta Terdaftar
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            @php $sisa = $t->sisa_hari; @endphp
                                            <span class="badge {{ $sisa <= 1 ? 'bg-label-danger' : 'bg-label-success' }} fw-bold">
                                                {{ $sisa == 0 ? 'HARI TERAKHIR' : $sisa . ' HARI LAGI' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="p-3 bg-light rounded border d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            <small class="text-muted d-block" style="font-size: 10px;">KODE UNDANGAN TOKEN:</small>
                                            <code class="text-primary fw-bold fs-6">{{ $t->invitation_code }}</code>
                                        </div>
                                        <a href="{{ route('login') }}" class="btn btn-sm btn-primary rounded-pill px-3">
                                            Daftar via Token <i class="bx bx-right-arrow-alt ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-md-6 text-center py-5">
                        <i class="bx bx-calendar-x display-1 text-muted opacity-25"></i>
                        <h5 class="mt-3 text-muted">Tidak ada program pelatihan yang aktif hari ini.</h5>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="row mb-5 align-items-center">
                <div class="col-md-6 text-center text-md-start mb-4 mb-md-0">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start mb-3">
                        <img src="https://res.cloudinary.com/dnwyqw6gn/image/upload/v1786770700/Integral_1_ykmzxx.png" width="45" style="filter: brightness(0) invert(1);">
                        <h3 class="text-white mb-0 ms-3 fw-bold">INTEGRAL</h3>
                    </div>
                    <p class="opacity-75 small">Badan Pengembangan Sumber Daya Manusia (BPSDM)<br>Pemerintah Provinsi Jawa Barat</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="opacity-75 small mb-0">Modul 1: Identity, Data Master & Manajemen Siklus Pelatihan</p>
                </div>
            </div>
            <hr class="bg-light opacity-25">
            <div class="text-center pt-3">
                <p class="small opacity-50 mb-0">&copy; {{ date('Y') }} INTEGRAL BPSDM Jawa Barat. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>

    <script>
        window.onscroll = function() {
            const nav = document.getElementById('mainNavbar');
            if (window.pageYOffset > 50) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        };

        const counters = document.querySelectorAll('.counter');
        const speed = 150;

        const runCounter = () => {
            counters.forEach(counter => {
                const updateCount = () => {
                    const target = +counter.getAttribute('data-target');
                    const count = +counter.innerText;
                    const inc = target / speed;

                    if (count < target) {
                        counter.innerText = Math.ceil(count + inc);
                        setTimeout(updateCount, 20);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
            });
        };

        const statsSection = document.querySelector('#stats');
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                runCounter();
                observer.unobserve(statsSection);
            }
        }, { threshold: 0.4 });

        if (statsSection) {
            observer.observe(statsSection);
        }
    </script>
</body>
</html>