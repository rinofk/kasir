@extends('layouts.app')

@section('title', 'Detail Login - ' . $user->name)
@section('header_title', 'Detail Riwayat Login')

@section('content')
    {{-- Breadcrumb --}}
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px; font-size: 14px; color: var(--text-secondary);">
        <a href="{{ route('login-histories.index') }}" style="color: var(--accent); text-decoration: none; font-weight: 600;">
            <i class="fa-solid fa-arrow-left"></i> Riwayat Login
        </a>
        <span>/</span>
        <span>{{ $user->name }}</span>
    </div>

    {{-- User Profile Card --}}
    <div class="card" style="margin-bottom: 20px;">
        <div class="card-body">
            <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), #6366f1); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 8px 20px -4px rgba(79,70,229,0.4);">
                    <i class="fa-solid fa-user" style="color: #fff; font-size: 24px;"></i>
                </div>
                <div style="flex-grow: 1;">
                    <h2 style="font-size: 20px; font-weight: 700; margin: 0 0 4px 0;">
                        {{ $user->name }}
                        @if($user->id === Auth::id())
                            <span style="font-size: 13px; color: var(--accent); font-weight: 400; margin-left: 6px;">(Anda)</span>
                        @endif
                    </h2>
                    <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
                        <span style="font-size: 14px; color: var(--text-secondary);"><i class="fa-solid fa-envelope" style="margin-right: 6px;"></i>{{ $user->email }}</span>
                        @foreach($user->roles as $role)
                            <span class="badge badge-primary" style="text-transform: capitalize; font-size: 13px;">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </div>
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div style="text-align: center; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 14px 24px;">
                        <div style="font-size: 28px; font-weight: 800; color: var(--accent);">{{ $histories->total() }}</div>
                        <div style="font-size: 12px; color: var(--text-secondary); font-weight: 600; margin-top: 2px;">
                            @if($date)
                                Login pada {{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}
                            @else
                                Total Login
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card">
        <div class="card-header">
            <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; justify-content: space-between;">
                <form action="{{ route('login-histories.show-user', $user->id) }}" method="GET" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <label for="date_filter" style="font-size: 13px; font-weight: 600; color: var(--text-secondary); white-space: nowrap;">Filter Tanggal:</label>
                        <input type="date" id="date_filter" name="date" class="form-control" value="{{ $date }}">
                    </div>
                    <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-filter"></i> Filter</button>
                    @if($date)
                        <a href="{{ route('login-histories.show-user', $user->id) }}" class="btn btn-secondary" style="padding: 10px 14px;">Reset</a>
                    @endif
                </form>
                <a href="{{ route('login-histories.index') }}" class="btn btn-secondary" style="display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-users"></i> Semua Akun
                </a>
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive table-responsive-card">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">No</th>
                            <th>Alamat IP</th>
                            <th>Perangkat / User Agent</th>
                            <th>Waktu Login</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                            <tr>
                                <td data-label="No" style="text-align: center; color: var(--text-secondary);">
                                    {{ ($histories->currentPage() - 1) * $histories->perPage() + $loop->iteration }}
                                </td>
                                <td data-label="Alamat IP">
                                    <span class="badge badge-secondary" style="font-size: 13px; font-family: monospace;">{{ $history->ip_address ?? 'N/A' }}</span>
                                </td>
                                <td data-label="Perangkat" style="max-width: 420px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $history->user_agent }}">
                                    <span style="font-size: 13px; color: var(--text-secondary);">
                                        <i class="fa-solid fa-laptop-code" style="margin-right: 6px;"></i>{{ $history->user_agent }}
                                    </span>
                                </td>
                                <td data-label="Waktu Login">
                                    <span style="font-size: 13px;">
                                        <i class="fa-regular fa-clock" style="color: var(--accent); margin-right: 4px;"></i>
                                        {{ $history->login_at ? $history->login_at->translatedFormat('d M Y, H:i:s') : 'N/A' }} WIB
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-secondary); padding: 32px;">
                                    <i class="fa-solid fa-inbox" style="font-size: 32px; margin-bottom: 12px; display: block; color: #cbd5e1;"></i>
                                    Tidak ada riwayat login
                                    @if($date)
                                        pada tanggal {{ \Carbon\Carbon::parse($date)->translatedFormat('d M Y') }}
                                    @endif
                                    untuk akun ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($histories->hasPages())
                <div style="padding: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: center;">
                    {{ $histories->appends(['date' => $date])->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
