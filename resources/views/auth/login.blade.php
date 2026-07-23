<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ \App\Models\Setting::get('store_name', 'Toko Nining') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-wrapper">

    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-brand"><i class="fa-solid fa-store"></i> {{ \App\Models\Setting::get('store_name', 'Toko Nining') }}</h1>
            <p class="auth-subtitle">Masuk untuk mengelola transaksi toko kelontong Anda</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
                <input type="checkbox" id="remember" name="remember" style="cursor: pointer;">
                <label for="remember" class="form-label" style="margin-bottom: 0; cursor: pointer; user-select: none;">Ingat Saya</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">
                Masuk &nbsp;<i class="fa-solid fa-right-to-bracket"></i>
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px; padding-top: 16px; border-top: 1px dashed var(--border-color);">
            <a href="{{ route('home') }}" style="color: var(--text-secondary); text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; transition: color 0.2s;">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Landing Page
            </a>
        </div>
    </div>

</body>
</html>
