<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ \App\Models\Setting::get('store_name', 'Toko Nining') }}</title>
    @php
        $favIcon = \App\Models\Setting::get('store_favicon', '');
    @endphp
    @if($favIcon && file_exists(public_path($favIcon)))
        <link rel="icon" href="{{ asset($favIcon) }}">
    @else
        <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🏪</text></svg>">
    @endif
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ file_exists(public_path('css/app.css')) ? filemtime(public_path('css/app.css')) : time() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .header-user-date-wrapper {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .header-user-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            background: var(--bg-secondary);
            padding: 6px 12px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
            white-space: nowrap;
        }
        
        .mobile-logout-btn {
            display: none !important;
        }
        
        @media (max-width: 768px) {
            .header-right {
                display: flex !important;
            }
            .header-user-date-wrapper {
                display: none !important;
            }
            .mobile-logout-btn {
                display: flex !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <div style="display: flex; align-items: center; gap: 10px; min-width: 0;">
                @php
                    $layoutStoreLogo = \App\Models\Setting::get('store_logo', '');
                    $layoutStoreIcon = \App\Models\Setting::get('store_icon', 'fa-store');
                    $layoutStoreName = \App\Models\Setting::get('store_name', 'Toko Nining');
                @endphp
                @if($layoutStoreLogo && file_exists(public_path($layoutStoreLogo)))
                    <img src="{{ asset($layoutStoreLogo) }}" alt="{{ $layoutStoreName }}" style="height: 28px; width: auto; object-fit: contain; border-radius: 4px; vertical-align: middle;">
                @else
                    <i class="fa-solid {{ $layoutStoreIcon }}"></i>
                @endif
                <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px;">{{ $layoutStoreName }}</span>
            </div>
            <button type="button" id="btnCollapseSidebar" class="sidebar-collapse-btn" style="background: transparent; border: none; color: rgba(255,255,255,0.6); cursor: pointer; font-size: 16px; padding: 4px; display: flex; align-items: center; justify-content: center; transition: color 0.2s; outline: none;" title="Sembunyikan Sidebar">
                <i class="fa-solid fa-angles-left"></i>
            </button>
        </div>
        
        <ul class="sidebar-menu">
            @if(Auth::user()->hasRole('admin') || Auth::user()->can('view reports'))
            <li class="sidebar-menu-item {{ Request::routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i class="fa-solid fa-chart-line"></i> Dashboard
                </a>
            </li>
            @endif

            <li class="sidebar-menu-item {{ Request::routeIs('pos') ? 'active' : '' }}">
                <a href="{{ route('pos') }}">
                    <i class="fa-solid fa-cash-register"></i> Transaksi (POS)
                </a>
            </li>

            @can('manage products')
            <li class="sidebar-menu-item {{ Request::routeIs('products.*') ? 'active' : '' }}">
                <a href="{{ route('products.index') }}">
                    <i class="fa-solid fa-boxes-stacked"></i> Produk
                </a>
            </li>
            @endcan
            
            @can('manage categories')
            <li class="sidebar-menu-item {{ Request::routeIs('categories.*') ? 'active' : '' }}">
                <a href="{{ route('categories.index') }}">
                    <i class="fa-solid fa-tags"></i> Kategori
                </a>
            </li>
            @endcan

            @can('manage users')
            <li class="sidebar-menu-item {{ Request::routeIs('users.*') ? 'active' : '' }}">
                <a href="{{ route('users.index') }}">
                    <i class="fa-solid fa-users"></i> Staff / Kasir
                </a>
            </li>

            <li class="sidebar-menu-item {{ Request::routeIs('roles.*') ? 'active' : '' }}">
                <a href="{{ route('roles.index') }}">
                    <i class="fa-solid fa-user-shield"></i> Hak Akses (Role)
                </a>
            </li>
            @endcan

            @can('riwayat login')
            <li class="sidebar-menu-item {{ Request::routeIs('login-histories.*') ? 'active' : '' }}">
                <a href="{{ route('login-histories.index') }}">
                    <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Login
                </a>
            </li>
            @endcan

            @can('view reports')
            <li class="sidebar-menu-item {{ Request::routeIs('reports.*') ? 'active' : '' }}">
                <a href="{{ route('reports.index') }}">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Laporan Penjualan
                </a>
            </li>
            @endcan

            @if(Auth::user()->hasRole('admin') || Auth::user()->can('manage settings'))
            <li class="sidebar-menu-item {{ Request::routeIs('settings.*') ? 'active' : '' }}">
                <a href="{{ route('settings.index') }}">
                    <i class="fa-solid fa-gear"></i> Pengaturan Toko
                </a>
            </li>
            @endif

            <li class="sidebar-menu-item">
                <a href="{{ route('home') }}" target="_blank">
                    <i class="fa-solid fa-globe"></i> Lihat Landing Page
                </a>
            </li>
        </ul>

        <div class="sidebar-user">
            <div class="sidebar-user-info">
                <span class="sidebar-user-name">{{ Auth::user()->name }}</span>
                <span class="sidebar-user-role">{{ Auth::user()->roles->first()->name ?? 'Staff' }}</span>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="button" class="logout-btn" title="Keluar" onclick="confirmLogout(event)">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-wrapper">
        <header class="header">
            <div style="display: flex; align-items: center; gap: 16px;">
                <button type="button" id="sidebarToggle" class="btn btn-secondary" style="padding: 10px 14px; display: none; align-items: center; justify-content: center;" title="Buka Menu">
                    <i class="fa-solid fa-bars" style="font-size: 18px;"></i>
                </button>
                <div class="header-title">
                    @yield('header_title')
                </div>
            </div>
            <div class="header-right">
                <!-- Mobile Logout Button (Visible only on mobile) -->
                <button type="button" class="btn btn-secondary mobile-logout-btn" onclick="confirmLogout(event)" style="display: none; padding: 10px 14px; align-items: center; justify-content: center; color: var(--danger); border-color: rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.05);" title="Keluar">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>

                <div class="header-user-date-wrapper" style="display: flex; align-items: center; gap: 12px;">
                    <div class="header-user-badge">
                        <i class="fa-solid fa-user-tag" style="color: var(--accent);"></i>
                        <span>{{ Auth::user()->name }}</span>
                    </div>
                    <span class="badge badge-primary header-date-badge">
                        <i class="fa-regular fa-calendar"></i> &nbsp;<span id="header-date"></span>
                    </span>
                    <button type="button" class="btn btn-secondary" onclick="confirmLogout(event)" style="padding: 10px 14px; display: flex; align-items: center; justify-content: center; color: var(--danger); border-color: rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.05); gap: 6px; font-weight: 600;" title="Keluar dari sistem">
                        <i class="fa-solid fa-right-from-bracket"></i> <span>Keluar</span>
                    </button>
                </div>
            </div>
        </header>

        <main class="content-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul style="margin: 0; padding-left: 16px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        function updateDate() {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            document.getElementById('header-date').textContent = new Date().toLocaleDateString('id-ID', options);
        }
        setInterval(updateDate, 60000);
        updateDate();

        function confirmLogout(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Keluar',
                text: "Apakah Anda yakin ingin keluar dari sistem?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal',
                background: '#ffffff',
                color: '#0f172a'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        // Global SweetAlert Delete Form Confirmation Interceptor
        document.addEventListener('submit', function(e) {
            if (e.target && e.target.classList.contains('delete-form')) {
                e.preventDefault();
                const form = e.target;
                const message = form.dataset.message || 'Apakah Anda yakin ingin menghapus data ini?';
                
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    background: '#ffffff',
                    color: '#0f172a'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        });

        // Sidebar Collapsible Toggle for Tablet/Mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('active');
            });

            // Close sidebar when clicking outside on small screens or when collapsed
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 1024 || document.body.classList.contains('sidebar-collapsed')) {
                    if (sidebar && sidebarToggle && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                        sidebar.classList.remove('active');
                    }
                }
            });
        }

        // Sidebar Collapse/Expand Logic
        const btnCollapseSidebar = document.getElementById('btnCollapseSidebar');
        const bodyTag = document.body;

        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            bodyTag.classList.add('sidebar-collapsed');
            if (btnCollapseSidebar) {
                btnCollapseSidebar.innerHTML = '<i class="fa-solid fa-angles-right"></i>';
                btnCollapseSidebar.setAttribute('title', 'Tampilkan Sidebar');
            }
        }

        if (btnCollapseSidebar) {
            btnCollapseSidebar.addEventListener('click', function(e) {
                e.stopPropagation();
                if (bodyTag.classList.contains('sidebar-collapsed')) {
                    bodyTag.classList.remove('sidebar-collapsed');
                    localStorage.setItem('sidebar-collapsed', 'false');
                    btnCollapseSidebar.innerHTML = '<i class="fa-solid fa-angles-left"></i>';
                    btnCollapseSidebar.setAttribute('title', 'Sembunyikan Sidebar');
                    if (sidebar) sidebar.classList.remove('active');
                } else {
                    bodyTag.classList.add('sidebar-collapsed');
                    localStorage.setItem('sidebar-collapsed', 'true');
                    btnCollapseSidebar.innerHTML = '<i class="fa-solid fa-angles-right"></i>';
                    btnCollapseSidebar.setAttribute('title', 'Tampilkan Sidebar');
                }
            });
        }
    </script>
    @yield('scripts')
</body>
</html>
