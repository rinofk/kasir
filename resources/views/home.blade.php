<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $storeName }} - Informasi Toko, Rekening Bank & Stok Gas Elpiji</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            --hero-bg: radial-gradient(circle at 10% 20%, #1e1b4b 0%, #0f172a 90%);
        }

        html {
            scroll-behavior: smooth;
        }

        html, body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            min-height: 100vh !important;
            display: flex !important;
            flex-direction: column !important;
            background-color: #f8fafc !important;
            color: #0f172a !important;
            font-family: 'Outfit', sans-serif !important;
        }

        #loginModal {
            display: none !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background: rgba(15, 23, 42, 0.75) !important;
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            z-index: 999999 !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
        }

        #loginModal.active {
            display: flex !important;
        }

        .landing-header {
            width: 100% !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 9999 !important;
            background: rgba(15, 23, 42, 0.92) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-sizing: border-box !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo {
            font-size: 22px;
            font-weight: 700;
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links {
            display: flex;
            gap: 24px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: #ffffff;
        }

        .hero-section {
            width: 100% !important;
            box-sizing: border-box !important;
            background: var(--hero-bg);
            color: #ffffff;
            padding: 135px 24px 65px 24px !important;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .section-container {
            width: 100% !important;
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 24px;
            box-sizing: border-box !important;
            scroll-margin-top: 80px;
        }

        .hero-container {
            max-width: 850px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #34d399;
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .hero-title {
            font-size: 42px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 16px;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 32px;
            max-width: 650px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .section-container {
            width: 100% !important;
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 24px;
            box-sizing: border-box !important;
        }

        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .section-tag {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #4f46e5;
            margin-bottom: 8px;
            display: block;
        }

        .section-title {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 8px 0;
        }

        .section-subtitle {
            font-size: 15px;
            color: #64748b;
            margin: 0;
        }

        /* Bank Account Cards */
        .bank-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 24px;
        }

        .bank-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            padding: 28px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
        }

        .bank-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.1);
        }

        .bank-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .bank-badge {
            font-size: 18px;
            font-weight: 800;
            padding: 6px 14px;
            border-radius: 8px;
            letter-spacing: 0.5px;
        }

        .bank-number-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .bank-number {
            font-family: monospace;
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: 1px;
        }

        .copy-btn {
            background: #4f46e5;
            color: #ffffff;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            outline: none;
        }

        .copy-btn:hover {
            background: #4338ca;
            transform: scale(1.05);
        }

        .copy-btn:active {
            transform: scale(0.95);
        }

        .copy-btn:hover {
            background: #4338ca;
        }

        /* Gas Stock Section */
        .gas-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px;
        }

        .gas-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }

        .gas-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .gas-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 16px;
        }

        .gas-name {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 6px 0;
        }

        .gas-price {
            font-size: 20px;
            font-weight: 800;
            color: #4f46e5;
            margin-bottom: 16px;
        }

        .gas-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 16px;
            border-top: 1px dashed #e2e8f0;
            margin-top: auto;
        }

        .landing-footer {
            width: 100% !important;
            box-sizing: border-box !important;
            background: #0f172a;
            color: #94a3b8;
            padding: 40px 24px 24px 24px;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            margin-top: auto;
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 30px; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="landing-header">
        <div class="header-container">
            <a href="{{ route('home') }}" class="brand-logo">
                <i class="fa-solid fa-store" style="color: #6366f1;"></i> {{ $storeName }}
            </a>

            <ul class="nav-links">
                <li><a href="#rekening">Rekening Bank</a></li>
                <li><a href="#gas-elpiji">Stok Gas Elpiji</a></li>
                <li><a href="#katalog">Katalog Produk</a></li>
                <li><a href="#kontak">Kontak & Lokasi</a></li>
            </ul>

            <div style="display: flex; gap: 12px; align-items: center;">
                @if(Auth::check())
                    <a href="{{ Auth::user()->hasRole('admin') ? route('dashboard') : route('pos') }}" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px;">
                        <i class="fa-solid fa-gauge"></i> Masuk Aplikasi
                    </a>
                @else
                    <button type="button" onclick="openLoginModal()" class="btn btn-secondary" style="padding: 8px 16px; font-size: 13px; background: rgba(255, 255, 255, 0.1); color: #ffffff; border-color: rgba(255, 255, 255, 0.2); cursor: pointer;">
                        <i class="fa-solid fa-right-to-bracket"></i> Login
                    </button>
                @endif
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-container">
            <div class="status-badge">
                <i class="fa-solid fa-circle" style="font-size: 8px;"></i> TOKO BUKA 06.00 s/d 21.00
            </div>

            <h1 class="hero-title">Selamat Datang di {{ $storeName }}</h1>
            <p class="hero-subtitle">
                Menyediakan kebutuhan pokok sehari-hari, sembako, makanan & minuman, serta pasokan <strong>Gas Elpiji 3kg, 5.5kg & 12kg</strong> lengkap dengan opsi pembayaran transfer bank (BRI, BNI, BCA).
            </p>

            <div class="hero-actions">
                <a href="#rekening" class="btn btn-primary" style="padding: 12px 24px; font-size: 15px;">
                    <i class="fa-solid fa-building-columns"></i> Lihat Rekening Bank
                </a>
                <a href="#gas-elpiji" class="btn btn-secondary" style="padding: 12px 24px; font-size: 15px; background: rgba(255, 255, 255, 0.15); color: #ffffff; border-color: rgba(255, 255, 255, 0.3);">
                    <i class="fa-solid fa-fire-flame-simple" style="color: #f87171;"></i> Cek Stok Gas Elpiji
                </a>
                @if($storePhone)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $storePhone) }}?text=Halo%20{{ urlencode($storeName) }},%20saya%20ingin%20bertanya%20informasi%20produk" target="_blank" class="btn btn-success" style="padding: 12px 24px; font-size: 15px;">
                        <i class="fa-brands fa-whatsapp"></i> Chat WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!-- Section 1: Rekening Bank Pembayaran -->
    <section id="rekening" class="section-container">
        <div class="section-header">
            <span class="section-tag">INFORMASI PEMBAYARAN</span>
            <h2 class="section-title">Nomor Rekening Bank Transfer</h2>
            <p class="section-subtitle">Gunakan nomor rekening resmi berikut untuk pembayaran transfer bank (BRI, BNI, BCA)</p>
        </div>

        <div class="bank-grid">
            @foreach($bankAccounts as $bank)
                @php
                    $rawDigits = preg_replace('/[^0-9]/', '', $bank['number']);
                    $formattedDisplay = $rawDigits ? implode('-', str_split($rawDigits, 4)) : '-';
                @endphp
                <div class="bank-card">
                    <div class="bank-card-header">
                        <div class="bank-badge" style="background: {{ $bank['bgColor'] }}; color: {{ $bank['color'] }};">
                            <i class="fa-solid {{ $bank['icon'] }}"></i> {{ $bank['name'] }}
                        </div>
                        <span style="font-size: 12px; color: #64748b; font-weight: 600;">{{ $bank['fullName'] }}</span>
                    </div>

                    <div style="font-size: 12px; color: #64748b; margin-bottom: 6px; font-weight: 600;">Nomor Rekening:</div>
                    
                    <div class="bank-number-box">
                        <span class="bank-number" id="num-{{ $bank['name'] }}">{{ $formattedDisplay }}</span>
                        <button type="button" class="copy-btn" onclick="copyToClipboard('{{ $rawDigits }}', '{{ $bank['name'] }}')" title="Salin Nomor Rekening {{ $bank['name'] }}">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: #475569; margin-top: 8px;">
                        <span>Atas Nama (A/N):</span>
                        <strong style="color: #0f172a;">{{ $bank['holder'] }}</strong>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Section 2: Informasi Stok Gas Elpiji -->
    <section id="gas-elpiji" style="width: 100% !important; box-sizing: border-box !important; background: #ffffff; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0; scroll-margin-top: 80px;">
        <div class="section-container">
            <div class="section-header">
                <span class="section-tag" style="color: #ef4444;">KONTROL PASOKAN ENERGI</span>
                <h2 class="section-title">Informasi Stok Gas Elpiji</h2>
                <p class="section-subtitle">Pantau ketersediaan stok tabung gas elpiji 3kg Melon, Bright Gas 5.5kg, dan 12kg secara live</p>
            </div>

            <div class="gas-grid">
                @forelse($gasProducts as $gas)
                    @php
                        $isAvailable = $gas->stock > 0;
                        $isCritical = $gas->stock > 0 && $gas->stock <= 5;
                    @endphp
                    <div class="gas-card">
                        <div>
                            <div class="gas-icon-wrapper" style="{{ $isAvailable ? 'background: rgba(16, 185, 129, 0.1); color: #10b981;' : 'background: rgba(239, 68, 68, 0.1); color: #ef4444;' }}">
                                <i class="fa-solid fa-fire-flame-simple"></i>
                            </div>
                            <h3 class="gas-name">{{ $gas->name }}</h3>
                            <div class="gas-price">Rp {{ number_format($gas->selling_price, 0, ',', '.') }}</div>
                        </div>

                        <div class="gas-meta">
                            <div>
                                <span style="font-size: 12px; color: #64748b; display: block;">Sisa Stok:</span>
                                @if($gas->stock <= 0)
                                    <span class="badge badge-danger" style="font-size: 13px; padding: 4px 10px;">Stok Habis</span>
                                @elseif($isCritical)
                                    <span class="badge badge-warning" style="font-size: 13px; padding: 4px 10px;">{{ $gas->stock }} Tabung (Menipis)</span>
                                @else
                                    <span class="badge badge-success" style="font-size: 13px; padding: 4px 10px;">{{ $gas->stock }} Tabung Tersedia</span>
                                @endif
                            </div>

                            @if($storePhone && $isAvailable)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $storePhone) }}?text=Halo%20{{ urlencode($storeName) }},%20saya%20ingin%20memesan%20{{ urlencode($gas->name) }}" target="_blank" class="btn btn-secondary" style="font-size: 12px; padding: 6px 12px;">
                                    <i class="fa-brands fa-whatsapp" style="color: #10b981;"></i> Pesan
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; color: #64748b; padding: 40px;">
                        Belum ada data stok gas elpiji terdaftar.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Section 3: Katalog Produk Highlight -->
    <section id="katalog" class="section-container">
        <div class="section-header">
            <span class="section-tag">KATALOG BARANG</span>
            <h2 class="section-title">Produk & Inventoris Toko</h2>
            <p class="section-subtitle">Cari barang kebutuhan sehari-hari yang tersedia di toko kami</p>
        </div>

        <form action="{{ route('home') }}#katalog" method="GET" style="max-width: 600px; margin: 0 auto 32px auto; display: flex; gap: 8px;">
            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Cari nama barang atau barcode..." style="font-size: 15px; padding: 12px 16px;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 20px;">
                <i class="fa-solid fa-magnifying-glass"></i> Cari
            </button>
            @if(request('search'))
                <a href="{{ route('home') }}#katalog" class="btn btn-secondary" style="padding: 12px 16px;" title="Reset">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
            @forelse($catalogProducts as $prod)
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 11px; color: #64748b; margin-bottom: 8px;">
                            <span style="font-family: monospace; font-weight: 600;">{{ $prod->code }}</span>
                            <span style="background: #e0e7ff; color: #4f46e5; padding: 2px 6px; border-radius: 4px; font-weight: 600;">{{ $prod->category->name ?? 'Umum' }}</span>
                        </div>
                        <h4 style="font-size: 15px; font-weight: 600; color: #0f172a; margin: 0 0 12px 0;">{{ $prod->name }}</h4>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px dashed #e2e8f0; padding-top: 12px; margin-top: auto;">
                        <strong style="font-size: 15px; color: #4f46e5;">Rp {{ number_format($prod->selling_price, 0, ',', '.') }}</strong>
                        <span style="font-size: 12px; color: #64748b;">
                            @if($prod->stock <= 0)
                                <span style="color: #ef4444; font-weight: 700;">Habis</span>
                            @else
                                Stok: <strong>{{ $prod->stock }}</strong>
                            @endif
                        </span>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; color: #64748b; padding: 40px;">
                    Produk tidak ditemukan.
                </div>
            @endforelse
        </div>
    </section>

    <!-- Footer -->
    <footer id="kontak" class="landing-footer">
        <div style="max-width: 1200px; margin: 0 auto;">
            <h3 style="font-size: 20px; font-weight: 700; color: #ffffff; margin-bottom: 12px;">{{ $storeName }}</h3>
            <p style="font-size: 14px; max-width: 500px; margin: 0 auto 20px auto; color: #94a3b8; line-height: 1.6;">
                {{ $storeAddress }}<br>
                @if($storePhone)
                    Telp / WhatsApp: <strong style="color: #ffffff;">{{ $storePhone }}</strong>
                @endif
            </p>

            <div style="border-top: 1px solid rgba(255, 255, 255, 0.1); padding-top: 20px; font-size: 13px; color: #64748b;">
                &copy; {{ date('Y') }} {{ $storeName }}. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- Modal Login Staff / Admin -->
    <div class="modal-overlay" id="loginModal">
        <div class="modal-container" style="max-width: 420px; width: 92%; background: #ffffff; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35); overflow: hidden; position: relative; border: 1px solid rgba(255, 255, 255, 0.2);">
            <!-- Modal Header -->
            <div style="background: var(--hero-bg); color: #ffffff; padding: 24px 24px 20px 24px; text-align: center; position: relative;">
                <button type="button" onclick="closeLoginModal()" style="position: absolute; top: 16px; right: 16px; background: rgba(255, 255, 255, 0.15); border: none; color: #ffffff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s;" title="Tutup">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <div style="width: 52px; height: 52px; background: rgba(99, 102, 241, 0.25); border-radius: 14px; color: #818cf8; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px auto; font-size: 24px; border: 1px solid rgba(129, 140, 248, 0.3);">
                    <i class="fa-solid fa-store"></i>
                </div>
                <h3 style="font-size: 20px; font-weight: 700; margin: 0 0 4px 0; color: #ffffff;">{{ $storeName }}</h3>
                <p style="font-size: 13px; color: #94a3b8; margin: 0;">Masuk untuk mengelola transaksi & toko</p>
            </div>

            <!-- Modal Body / Form -->
            <div style="padding: 24px;">
                @if($errors->any())
                    <div class="alert alert-danger" style="margin-bottom: 16px; font-size: 13px; padding: 10px 14px; border-radius: 8px; background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;">
                        @foreach ($errors->all() as $error)
                            <div><i class="fa-solid fa-triangle-exclamation"></i> {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label for="modal_email" class="form-label" style="font-weight: 600; font-size: 13px; color: #334155;">Email Kasir / Admin</label>
                        <input type="email" id="modal_email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus style="font-size: 14px; padding: 11px 14px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label for="modal_password" class="form-label" style="font-weight: 600; font-size: 13px; color: #334155;">Password</label>
                        <input type="password" id="modal_password" name="password" class="form-control" placeholder="••••••••" required style="font-size: 14px; padding: 11px 14px;">
                    </div>

                    <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
                        <input type="checkbox" id="modal_remember" name="remember" style="cursor: pointer; width: 16px; height: 16px;">
                        <label for="modal_remember" class="form-label" style="margin-bottom: 0; cursor: pointer; user-select: none; font-size: 13px; color: #475569;">Ingat Saya</label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px; font-size: 15px; font-weight: 600; justify-content: center; border-radius: 8px;">
                        Masuk &nbsp;<i class="fa-solid fa-right-to-bracket"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openLoginModal() {
            const modal = document.getElementById('loginModal');
            if (modal) {
                modal.classList.add('active');
                setTimeout(() => {
                    const emailInput = document.getElementById('modal_email');
                    if (emailInput) emailInput.focus();
                }, 150);
            }
        }

        function closeLoginModal() {
            const modal = document.getElementById('loginModal');
            if (modal) modal.classList.remove('active');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLoginModal();
            }
        });

        const loginModalElem = document.getElementById('loginModal');
        if (loginModalElem) {
            loginModalElem.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeLoginModal();
                }
            });
        }

        function copyToClipboard(rawText, bankName) {
            const cleanDigits = String(rawText).replace(/[^0-9]/g, '');

            function showToast() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `No. Rekening ${bankName} (${cleanDigits}) berhasil disalin!`,
                    showConfirmButton: false,
                    timer: 2200,
                    timerProgressBar: true
                });
            }

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(cleanDigits).then(showToast).catch(function() {
                    fallbackCopy(cleanDigits, showToast);
                });
            } else {
                fallbackCopy(cleanDigits, showToast);
            }
        }

        function fallbackCopy(text, callback) {
            const tempInput = document.createElement('input');
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            if (callback) callback();
        }
    </script>

    @if($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                openLoginModal();
            });
        </script>
    @endif
</body>
</html>
