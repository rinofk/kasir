<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk #{{ $transaction->invoice_number }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
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
    </style>
</head>
<body>

    <div class="receipt-paper">
        <div class="receipt-header">
            <h2 style="font-size: 16px; margin: 0 0 4px 0; font-weight: 700; text-transform: uppercase;">TOKO NINING</h2>
            <p style="font-size: 11px; margin: 0 0 2px 0; color: #333;">Mentibar, Kecamatan Paloh, Kabupaten Sambas</p>
            <p style="font-size: 11px; margin: 0; color: #333;">Telp: 0812-3456-7890</p>
        </div>

        <div class="receipt-divider"></div>

        <div style="font-size: 11px; margin-bottom: 8px; line-height: 1.4;">
            <div><strong>No. Invoice:</strong> {{ $transaction->invoice_number }}</div>
            <div><strong>Waktu:</strong> {{ $transaction->created_at->format('d/m/Y H:i') }}</div>
            <div><strong>Kasir:</strong> {{ $transaction->user->name }}</div>
        </div>

        <div class="receipt-divider"></div>

        <!-- Items Table -->
        <div style="margin-bottom: 8px;">
            @foreach($transaction->details as $detail)
                <div class="receipt-item-row">
                    <span style="font-weight: 600; width: 65%;">{{ $detail->product_id ? $detail->product->name : $detail->custom_name }}</span>
                    <span style="width: 35%; text-align: right;">{{ $detail->quantity }}x {{ number_format($detail->price, 0, ',', '.') }}</span>
                </div>
                <div style="font-size: 11px; text-align: right; margin-bottom: 6px; padding-right: 2px;">
                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
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
