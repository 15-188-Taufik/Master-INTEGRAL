<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme shadow-sm">
    <!-- Logo & Brand -->
    <div class="app-brand demo" style="height: 75px;">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
                {{-- LOGO INTEGRAL --}}
                <img src="https://res.cloudinary.com/dnwyqw6gn/image/upload/v1786770700/Integral_1_ykmzxx.png" 
                     alt="Integral Logo" 
                     style="width: 32px; filter: drop-shadow(0px 2px 4px rgba(105, 108, 255, 0.3));">
            </span>
            <span class="app-brand-text demo menu-text fw-bolder ms-2 text-uppercase" style="letter-spacing: 1px; font-size: 1.25rem;">Integral</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-3">
        {{-- MENU UNTUK ADMIN & SUPERADMIN --}}
        @if(Auth::user()->role !== 'participant')
            <li class="menu-item {{ request()->is('dashboard*') || request()->is('/') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div class="fw-bold">Dashboard Admin</div>
                </a>
            </li>

            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Manajemen Pelatihan</span>
            </li>
            
            <li class="menu-item {{ request()->is('trainings*') ? 'active' : '' }}">
                <a href="{{ route('trainings.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-collection"></i>
                    <div>Daftar Pelatihan</div>
                </a>
            </li>

            @if(Auth::check() && Auth::user()->role == 'superadmin')
            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Pengaturan Sistem</span>
            </li>
            <li class="menu-item {{ request()->is('users*') ? 'active' : '' }}">
                <a href="{{ route('users.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-shield-quarter"></i>
                    <div>Kelola Admin Bidang</div>
                </a>
            </li>
            <li class="menu-item {{ request()->is('activity-logs*') ? 'active' : '' }}">
                <a href="{{ route('activity-logs.index') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-history"></i>
                    <div>Audit Trail & Log</div>
                </a>
            </li>
            @endif
        @endif

        {{-- MENU KHUSUS PESERTA --}}
        @if(Auth::user()->role === 'participant')
            <li class="menu-item {{ request()->routeIs('participant.dashboard') ? 'active' : '' }}">
                <a href="{{ route('participant.dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-alt"></i>
                    <div class="fw-bold">Dashboard Saya</div>
                </a>
            </li>

            <li class="menu-header small text-uppercase">
                <span class="menu-header-text">Portal Peserta</span>
            </li>

            <li class="menu-item {{ request()->routeIs('participant.trainings') ? 'active' : '' }}">
                <a href="{{ route('participant.trainings') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-list-ul"></i>
                    <div>Daftar Pelatihan</div>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('participant.history') ? 'active' : '' }}">
                <a href="{{ route('participant.history') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-history"></i>
                    <div>Riwayat & Capaian JP</div>
                </a>
            </li>
        @endif
    </ul>
</aside>

{{-- CSS CUSTOM UNTUK INTERAKTIVITAS --}}
@push('css')
<style>
    .menu-vertical .menu-item .menu-link {
        transition: all 0.2s ease-in-out;
        border-radius: 0.375rem;
        margin: 0.15rem 1rem;
        padding-left: 1rem;
    }

    .menu-vertical .menu-item:not(.active):not(.open) .menu-link:hover {
        background-color: rgba(105, 108, 255, 0.08) !important;
        transform: translateX(5px);
        color: #696cff !important;
    }

    .menu-vertical .menu-item:not(.active):not(.open) .menu-link:hover i {
        color: #696cff !important;
        transform: scale(1.1);
    }

    .menu-header {
        margin: 1.5rem 0 0.5rem 0 !important;
        padding-left: 1.5rem;
    }

    .menu-header-text {
        color: #a1acb8 !important;
        font-weight: 700 !important;
        letter-spacing: 1px;
    }

    .menu-vertical .menu-item.active > .menu-link {
        box-shadow: 0px 4px 8px rgba(105, 108, 255, 0.25);
        background: linear-gradient(72.47deg, #696cff 22.16%, rgba(105, 108, 255, 0.7) 76.47%) !important;
        color: #fff !important;
    }

    .menu-vertical .menu-item.active > .menu-link i {
        color: #fff !important;
    }
</style>
@endpush