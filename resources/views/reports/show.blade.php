@extends('layouts.app')

@section('title', 'Detail Transaksi #' . $transaction->invoice_number)
@section('header_title', 'Detail Transaksi Penjualan')

@section('content')
    <div style="margin-bottom: 24px;">
        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Laporan
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
        <!-- Left: Items list -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-cart-shopping"></i> Daftar Barang Belanja</span>
            </div>
            <div class="card-body" style="padding: 0;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode Produk</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th style="text-align: right;">Harga Satuan</th>
                                <th style="text-align: center;">Jumlah Qty</th>
                                <th style="text-align: right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->details as $detail)
                                <tr>
                                    <td><code>{{ $detail->product->code ?? 'N/A' }}</code></td>
                                    <td><strong>{{ $detail->product->name ?? 'Produk Telah Dihapus' }}</strong></td>
                                    <td>{{ $detail->product->category->name ?? 'N/A' }}</td>
                                    <td style="text-align: right;">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                                    <td style="text-align: center;">{{ $detail->quantity }}</td>
                                    <td style="text-align: right;"><strong>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Summary and print -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="card">
                <div class="card-header">
                    <span class="card-title"><i class="fa-solid fa-receipt"></i> Rincian Invoice</span>
                </div>
                <div class="card-body" style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                        <span style="color: var(--text-secondary);">No. Invoice</span>
                        <strong>{{ $transaction->invoice_number }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                        <span style="color: var(--text-secondary);">Tanggal</span>
                        <span>{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                        <span style="color: var(--text-secondary);">Nama Kasir</span>
                        <span>{{ $transaction->user->name }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                        <span style="color: var(--text-secondary);">Total Belanja</span>
                        <strong>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                        <span style="color: var(--text-secondary);">Uang Diterima</span>
                        <span>Rp {{ number_format($transaction->payment_amount, 0, ',', '.') }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-secondary); font-weight: 600;">Uang Kembalian</span>
                        <strong style="color: var(--success); font-size: 16px;">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div style="padding: 16px 24px; border-top: 1px solid var(--border-color); background: #f8fafc; display: flex;">
                    <a href="{{ route('pos.receipt', $transaction->id) }}" target="_blank" class="btn btn-primary" style="width: 100%;">
                        <i class="fa-solid fa-print"></i> Cetak Struk / Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
