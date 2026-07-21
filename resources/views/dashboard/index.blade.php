@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard Ringkasan Toko')

@section('content')
    <!-- Stats Cards Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-label">Penjualan Hari Ini</span>
            <span class="stat-value">Rp {{ number_format($todaySales, 0, ',', '.') }}</span>
            <div class="stat-icon" style="color: var(--success); background-color: var(--success-light);">
                <i class="fa-solid fa-rupiah-sign"></i>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-label">Transaksi Hari Ini</span>
            <span class="stat-value">{{ $todayTransactionsCount }}</span>
            <div class="stat-icon" style="color: var(--accent); background-color: var(--accent-light);">
                <i class="fa-solid fa-receipt"></i>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-label">Total Produk</span>
            <span class="stat-value">{{ $totalProducts }}</span>
            <div class="stat-icon" style="color: var(--warning); background-color: var(--warning-light);">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>
        <div class="stat-card">
            <span class="stat-label">Kategori Produk</span>
            <span class="stat-value">{{ $totalCategories }}</span>
            <div class="stat-icon" style="color: #06b6d4; background-color: #ecfeff;">
                <i class="fa-solid fa-tags"></i>
            </div>
        </div>
    </div>

    <!-- Charts and Tables Layout -->
    <div class="dashboard-layout">
        <!-- Left Side: Chart and Recent Transactions -->
        <div class="layout-main">
            <!-- Chart Card -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fa-solid fa-chart-area"></i> Tren Grafik Penjualan Bulan Ini</span>
                </div>
                <div class="card-body">
                    <div style="position: relative; height:300px; width:100%">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Transaksi Terbaru</span>
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">Lihat Semua</a>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nomor Invoice</th>
                                    <th>Kasir</th>
                                    <th>Total Belanja</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $tx)
                                    <tr>
                                        <td><strong>{{ $tx->invoice_number }}</strong></td>
                                        <td>{{ $tx->user->name }}</td>
                                        <td>Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                        <td>{{ $tx->created_at->format('d M Y, H:i') }}</td>
                                        <td>
                                            <a href="{{ route('pos.receipt', $tx->id) }}" target="_blank" class="btn btn-secondary" style="padding: 4px 8px; font-size: 12px;" title="Cetak Struk">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 32px;">
                                            Belum ada transaksi hari ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Stock Warnings -->
        <div class="layout-side">
            <div class="card" style="border-color: rgba(239, 68, 68, 0.2);">
                <div class="card-header" style="background-color: var(--danger-light); color: var(--danger);">
                    <span class="card-title" style="color: var(--danger);"><i class="fa-solid fa-triangle-exclamation"></i> Peringatan Stok Tipis</span>
                </div>
                <div class="card-body" style="padding: 0;">
                    <ul style="list-style: none;">
                        @forelse($lowStockProducts as $p)
                            <li style="padding: 16px 24px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                                <div style="display: flex; flex-direction: column;">
                                    <span style="font-weight: 600; font-size: 14px;">{{ $p->name }}</span>
                                    <span style="font-size: 12px; color: var(--text-secondary);">Kode: {{ $p->code }} | Kategori: {{ $p->category->name }}</span>
                                </div>
                                <span class="badge badge-danger">Sisa: {{ $p->stock }}</span>
                            </li>
                        @empty
                            <li style="padding: 32px 24px; text-align: center; color: var(--success); font-weight: 500;">
                                <i class="fa-regular fa-circle-check" style="font-size: 24px; margin-bottom: 8px;"></i>
                                <div>Semua stok produk aman!</div>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Omset Penjualan (Rp)',
                    data: {!! json_encode($chartValues) !!},
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#4f46e5',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
@endsection
