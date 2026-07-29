<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk #{{ $transaction->invoice_number }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ file_exists(public_path('css/app.css')) ? filemtime(public_path('css/app.css')) : time() }}">
    <style>
        body {
            background-color: #f1f5f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }
        .receipt-paper {
            font-family: 'Outfit', sans-serif !important;
        }
    </style>
</head>
<body>

    <div class="receipt-paper">
        <div class="receipt-header">
            <h2 style="font-size: 16px; margin: 0 0 4px 0; font-weight: 700; text-transform: uppercase;">{{ strtoupper(\App\Models\Setting::get('store_name', 'TOKO NINING')) }}</h2>
            <p style="font-size: 11px; margin: 0 0 2px 0; color: #333;">{{ \App\Models\Setting::get('store_address', 'Mentibar, Kecamatan Paloh, Kabupaten Sambas') }}</p>
            <p style="font-size: 11px; margin: 0; color: #333;">Telp: {{ \App\Models\Setting::get('store_phone', '0812-3456-7890') }}</p>
        </div>

        <div class="receipt-divider"></div>

        <div style="font-size: 11px; margin-bottom: 8px; line-height: 1.4; display: flex; flex-direction: column; gap: 2px;">
            <div style="display: flex; justify-content: space-between;">
                <strong>No. Invoice:</strong>
                <span>{{ $transaction->invoice_number }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <strong>Waktu:</strong>
                <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <strong>Kasir:</strong>
                <span>{{ $transaction->user->name }}</span>
            </div>
        </div>

        <div class="receipt-divider"></div>

        <!-- Items Table -->
        <div style="margin-bottom: 8px;">
            @foreach($transaction->details as $detail)
                <div style="margin-bottom: 10px; line-height: 1.4;">
                    <!-- Item Name (top line) -->
                    <div style="font-size: 12px; font-weight: 500; color: #000;">
                        {{ $detail->product_id ? $detail->product->name : $detail->custom_name }}
                    </div>
                    <!-- Details & Subtotal (bottom line) -->
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 11px; color: #333; margin-top: 1px;">
                        <span>Rp{{ number_format($detail->price, 0, ',', '.') }} x {{ $detail->quantity }}</span>
                        <strong style="font-weight: 700; color: #000; font-size: 12px;">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</strong>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="receipt-divider"></div>

        <!-- Financial Totals -->
        <div class="receipt-totals" style="font-size: 12px; line-height: 1.5;">
            <div style="display: flex; justify-content: space-between;">
                <span>TOTAL:</span>
                <strong>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>TUNAI:</span>
                <span>Rp {{ number_format($transaction->payment_amount, 0, ',', '.') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-weight: 700;">
                <span>KEMBALI:</span>
                <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="receipt-divider"></div>

        <div style="text-align: center; font-size: 11px; margin-top: 15px;">
            <p style="margin: 0 0 4px 0; font-weight: 600;">TERIMA KASIH</p>
            <p style="margin: 0; color: #555;">Atas Kunjungan Anda</p>
        </div>
    </div>

    <!-- Floating Actions for Screen view -->
    <div class="no-print" style="position: fixed; bottom: 20px; right: 20px; display: flex; gap: 8px; z-index: 10000;">
        <button onclick="window.print()" class="btn btn-primary" style="box-shadow: var(--shadow-lg);">
            <i class="fa-solid fa-print"></i> Cetak Struk
        </button>
        <button onclick="window.close()" class="btn btn-secondary" style="box-shadow: var(--shadow-lg);">
            Tutup
        </button>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
