<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ \App\Models\Setting::get('store_name', 'Toko Nining') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-store"></i> {{ \App\Models\Setting::get('store_name', 'Toko Nining') }}
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
            <div class="header-title">
                @yield('header_title')
            </div>
            <div class="header-right">
                <span class="badge badge-primary">
                    <i class="fa-regular fa-calendar"></i> &nbsp;<span id="header-date"></span>
                </span>
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
    </script>
    @yield('scripts')
</body>
</html>
