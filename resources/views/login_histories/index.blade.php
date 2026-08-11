@extends('layouts.app')

@section('title', 'Riwayat Login')
@section('header_title', 'Riwayat Login / Audit Log')

@section('content')
    <div class="card">
        <div class="card-header">
            <div style="display: flex; gap: 16px; align-items: center; width: 100%; justify-content: space-between; flex-wrap: wrap;">
                <form action="{{ route('login-histories.index') }}" method="GET" style="display: flex; gap: 12px; flex-grow: 1; max-width: 700px; align-items: center; flex-wrap: wrap;">
                    <div style="flex-grow: 1; min-width: 200px;">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama staff atau email..." value="{{ $search }}">
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; min-width: 200px;">
                        <label for="date_filter" style="font-size: 13px; font-weight: 600; color: var(--text-secondary); white-space: nowrap;">Tanggal:</label>
                        <input type="date" id="date_filter" name="date" class="form-control" value="{{ $date }}">
                    </div>
                    <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-filter"></i> Filter</button>
                    @if($search || $date)
                        <a href="{{ route('login-histories.index') }}" class="btn btn-secondary" style="padding: 10px 14px;">Reset</a>
                    @endif
                </form>
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">No</th>
                            <th>Nama Staff</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th style="text-align: center;">Total Login</th>
                            <th>Login Terakhir</th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr style="cursor: pointer;" onclick="window.location='{{ route('login-histories.show-user', $user->id) }}{{ $date ? '?date='.$date : '' }}'">
                                <td style="text-align: center; color: var(--text-secondary);">
                                    {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), #6366f1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                            <i class="fa-solid fa-user" style="color: #fff; font-size: 14px;"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                            @if($user->id === Auth::id())
                                                <span style="font-size: 11px; color: var(--accent); margin-left: 4px;">(Anda)</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="color: var(--text-secondary);">{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge badge-primary" style="text-transform: capitalize;">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td style="text-align: center;">
                                    <span style="display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--accent), #6366f1); color: #fff; font-weight: 700; font-size: 15px; width: 42px; height: 42px; border-radius: 50%; box-shadow: 0 4px 10px -2px rgba(79,70,229,0.35);">
                                        {{ $user->total_logins }}
                                    </span>
                                </td>
                                <td>
                                    @if($user->last_login)
                                        <span style="font-size: 13px;">
                                            <i class="fa-regular fa-clock" style="color: var(--accent); margin-right: 4px;"></i>
                                            {{ \Carbon\Carbon::parse($user->last_login)->translatedFormat('d M Y, H:i') }} WIB
                                        </span>
                                    @else
                                        <span style="color: var(--text-secondary);">-</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('login-histories.show-user', $user->id) }}{{ $date ? '?date='.$date : '' }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;" title="Lihat Detail Login">
                                        <i class="fa-solid fa-eye" style="color: var(--accent);"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 32px;">
                                    <i class="fa-solid fa-inbox" style="font-size: 32px; margin-bottom: 12px; display: block; color: #cbd5e1;"></i>
                                    Riwayat login tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div style="padding: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: center;">
                    {{ $users->appends(['search' => $search, 'date' => $date])->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
