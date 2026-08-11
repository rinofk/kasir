@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('header_title', 'Laporan Analisis Penjualan')

@section('content')
    @role('admin')
    <!-- Report Metrics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Total Omset Penjualan</span>
            <span class="stat-value">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
            <div class="stat-icon" style="color: var(--success); background-color: var(--success-light);">
                <i class="fa-solid fa-rupiah-sign"></i>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-label">Total Transaksi</span>
            <span class="stat-value">{{ $totalTransactions }}</span>
            <div class="stat-icon" style="color: var(--accent); background-color: var(--accent-light);">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>
        <div class="stat-card" style="border-color: rgba(16, 185, 129, 0.3);">
            <span class="stat-label" style="color: var(--success);">Estimasi Laba Kotor</span>
            <span class="stat-value" style="color: var(--success);">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
            <div class="stat-icon" style="color: #ffffff; background-color: var(--success);">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>
    </div>
    @endrole

    <!-- Filters Card -->
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-filter"></i> Filter Periode & Kasir</span>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="user_id" class="form-label">Nama Kasir</label>
                    <select id="user_id" name="user_id" class="form-control">
                        <option value="">Semua Kasir</option>
                        @foreach($cashiers as $c)
                            <option value="{{ $c->id }}" {{ $cashierId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary" style="flex-grow: 1;">
                        <i class="fa-solid fa-magnifying-glass"></i> Terapkan
                    </button>
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    @role('admin')
    <!-- Product Sales Statistics & Profit -->
    <div class="card">
        <div class="card-header" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="toggleCardBody('product-stats-body', 'product-stats-icon')">
            <span class="card-title"><i class="fa-solid fa-chart-pie"></i> Rekap Penjualan & Keuntungan Per Barang</span>
            <button type="button" class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px; height: 28px; width: 28px; display: flex; align-items: center; justify-content: center; border-radius: 4px; border: 1px solid var(--border-color); background: transparent;">
                <i id="product-stats-icon" class="fa-solid fa-chevron-up"></i>
            </button>
        </div>
        <div class="card-body" id="product-stats-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">No</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th style="text-align: right;">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'quantity', 'sort_order' => ($sortBy === 'quantity' && $sortOrder === 'desc') ? 'asc' : 'desc']) }}" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; justify-content: flex-end; gap: 4px; width: 100%;">
                                    Jumlah Terjual
                                    @if($sortBy === 'quantity')
                                        <i class="fa-solid fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fa-solid fa-sort" style="opacity: 0.4;"></i>
                                    @endif
                                </a>
                            </th>
                            <th style="text-align: right;">
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'profit', 'sort_order' => ($sortBy === 'profit' && $sortOrder === 'desc') ? 'asc' : 'desc']) }}" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; justify-content: flex-end; gap: 4px; width: 100%;">
                                    Total Keuntungan
                                    @if($sortBy === 'profit')
                                        <i class="fa-solid fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fa-solid fa-sort" style="opacity: 0.4;"></i>
                                    @endif
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($productStats as $stat)
                            <tr>
                                <td style="text-align: center; color: var(--text-secondary);">{{ $loop->iteration }}</td>
                                <td><code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-weight: 600;">{{ $stat->code }}</code></td>
                                <td><strong>{{ $stat->name }}</strong></td>
                                <td style="text-align: right;">{{ rtrim(rtrim(number_format($stat->total_quantity, 3, ',', '.'), '0'), ',') }} {{ $stat->unit }}</td>
                                <td style="text-align: right; color: var(--success); font-weight: 600;">Rp {{ number_format($stat->total_profit, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 32px;">
                                    Tidak ada data penjualan barang pada periode terpilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endrole

    <!-- Transactions List -->
    <div class="card">
        <div class="card-header" style="cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="toggleCardBody('transactions-list-body', 'transactions-list-icon')">
            <span class="card-title"><i class="fa-solid fa-list-check"></i> Riwayat Penjualan</span>
            <button type="button" class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px; height: 28px; width: 28px; display: flex; align-items: center; justify-content: center; border-radius: 4px; border: 1px solid var(--border-color); background: transparent;">
                <i id="transactions-list-icon" class="fa-solid fa-chevron-up"></i>
            </button>
        </div>
        <div class="card-body" id="transactions-list-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">No</th>
                            <th>Nomor Invoice</th>
                            <th>Kasir</th>
                            <th>Tanggal Penjualan</th>
                            <th>Items</th>
                            <th>Total Belanja</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                            <tr>
                                <td style="text-align: center; color: var(--text-secondary);">
                                    {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}
                                </td>
                                <td><code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-weight: 600;">{{ $tx->invoice_number }}</code></td>
                                <td>{{ $tx->user->name }}</td>
                                <td>{{ $tx->created_at->format('d M Y, H:i') }}</td>
                                <td>
                                    <div style="font-size: 13px; color: var(--text-secondary); max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        @foreach($tx->details as $d)
                                            {{ $d->product_id ? ($d->product->name ?? 'Produk Dihapus') : $d->custom_name }} ({{ (float) $d->quantity }}x),
                                        @endforeach
                                    </div>
                                </td>
                                <td><strong>Rp {{ number_format($tx->total_price, 0, ',', '.') }}</strong></td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="{{ route('reports.show', $tx->id) }}" class="btn btn-secondary" style="padding: 6px 10px;" title="Detail Transaksi">
                                            <i class="fa-solid fa-eye"></i> Detail
                                        </a>
                                        <a href="{{ route('pos.receipt', $tx->id) }}" target="_blank" class="btn btn-secondary" style="padding: 6px 10px;" title="Cetak Struk">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 32px;">
                                    Tidak ada riwayat transaksi pada periode terpilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($transactions->hasPages())
                <div style="padding: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: center;">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function toggleCardBody(bodyId, iconId) {
        const body = document.getElementById(bodyId);
        const icon = document.getElementById(iconId);
        if (body.style.display === 'none') {
            body.style.display = 'block';
            icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
        } else {
            body.style.display = 'none';
            icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
        }
    }
</script>
@endsection
