<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <!-- Tombol Menu untuk Mobile -->
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search (Otomatis sembunyi di HP jika layar terlalu kecil atau menyesuaikan) -->
        <form action="{{ route('global.search') }}" method="GET" class="navbar-nav align-items-center w-100">
            <div class="nav-item d-flex align-items-center w-100">
                <i class="bx bx-search fs-4 lh-0"></i>
                <input 
                    type="text" 
                    name="q" {{-- Berikan nama input 'q' --}}
                    class="form-control border-0 shadow-none" 
                    placeholder="Cari Pelatihan, Peserta, atau Dokumen..." 
                    aria-label="Search..."
                    value="{{ request('q') }}" {{-- Menjaga teks pencarian tetap ada setelah submit --}}
                />
            </div>
        </form>
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- User Profile Dropdown -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        {{-- LOGIKA FOTO PROFIL NAVBAR --}}
                        @if(auth()->user()->profile_photo)
                            {{-- Prioritas 1: Foto yang diunggah manual --}}
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt class="w-px-40 h-auto rounded-circle" style="object-fit: cover; aspect-ratio: 1/1;" />
                        @elseif(auth()->user()->avatar)
                            {{-- Prioritas 2: Avatar dari Google Login --}}
                            <img src="{{ auth()->user()->avatar }}" alt class="w-px-40 h-auto rounded-circle" style="object-fit: cover; aspect-ratio: 1/1;" />
                        @else
                            {{-- Fallback: Inisial Nama --}}
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </span>
                        @endif
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        {{-- LOGIKA FOTO PROFIL DI DALAM MENU --}}
                                        @if(auth()->user()->profile_photo)
                                            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt class="w-px-40 h-auto rounded-circle" style="object-fit: cover; aspect-ratio: 1/1;" />
                                        @elseif(auth()->user()->avatar)
                                            <img src="{{ auth()->user()->avatar }}" alt class="w-px-40 h-auto rounded-circle" style="object-fit: cover; aspect-ratio: 1/1;" />
                                        @else
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                {{ substr(auth()->user()->name, 0, 1) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">{{ auth()->user()->name }}</span>
                                    <small class="text-muted">{{ strtoupper(auth()->user()->role) }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="bx bx-user me-2"></i>
                            <span class="align-middle">Profil Saya</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <!-- TOMBOL LOGOUT -->
                    <li>
                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class='bx bx-power-off me-2'></i>
                            <span class="align-middle">Log Out</span>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>