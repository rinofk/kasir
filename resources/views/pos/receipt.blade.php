<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk #{{ $transaction->invoice_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ===== RESET ===== */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* ===== SCREEN & PRINT STYLES ===== */
        body {
            background-color: #e5e7eb;
            font-family: 'Outfit', 'Courier New', Courier, monospace, sans-serif;
            display: block;
            padding: 20px 10px 80px 10px;
        }

        .receipt-paper {
            background: #fff;
            width: 100%;
            max-width: 48mm; /* Area cetak aktual printer thermal 58mm adalah ~48mm / 384 dot */
            margin: 0 auto;
            padding: 8px 4px;
            font-size: 10px;
            line-height: 1.3;
            color: #000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 6px;
        }

        .receipt-divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .receipt-totals {
            font-size: 10.5px;
            line-height: 1.5;
        }

        /* ===== TOMBOL LAYAR ===== */
        .no-print {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 8px;
            z-index: 9999;
        }

        .btn-screen {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: opacity 0.2s;
        }
        .btn-screen:hover { opacity: 0.85; }
        .btn-print { background: #4f46e5; color: #fff; }
        .btn-close  { background: #fff; color: #374151; }

        /* ===== PRINT STYLES ===== */
        @page {
            size: 58mm auto;
            margin: 0mm !important; /* Margin 0 agar tidak menambah offset hardware printer */
        }

        @media print {
            html, body {
                background: #fff !important;
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                min-height: 0 !important;
                height: auto !important;
            }
            .receipt-paper {
                width: 48mm !important;
                max-width: 48mm !important;
                margin: 0 !important;
                padding: 2mm 1mm !important;
                box-shadow: none !important;
                font-size: 9.5px !important;
                color: #000 !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="receipt-paper">
        <div class="receipt-header">
            <h2 style="font-size: 13px; font-weight: normal; text-transform: uppercase; margin-bottom: 3px;">
                {{ strtoupper(\App\Models\Setting::get('store_name', 'TOKO NINING')) }}
            </h2>
            <p style="font-size: 10px; color: #333; margin-bottom: 1px;">
                {{ \App\Models\Setting::get('store_address', 'Mentibar, Kecamatan Paloh, Kabupaten Sambas') }}
            </p>
            <p style="font-size: 10px; color: #333;">
                Telp: {{ \App\Models\Setting::get('store_phone', '0812-3456-7890') }}
            </p>
        </div>

        <div class="receipt-divider"></div>

        <div style="font-size: 10px; margin-bottom: 6px; line-height: 1.5;">
            <div style="display: flex; justify-content: space-between;">
                <span>No. Invoice:</span>
                <span>{{ $transaction->invoice_number }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Waktu:</span>
                <span>{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Kasir:</span>
                <span>{{ $transaction->user->name }}</span>
            </div>
        </div>

        <div class="receipt-divider"></div>

        <!-- Items -->
        <div style="margin-bottom: 6px;">
            @foreach($transaction->details as $detail)
                <div style="margin-bottom: 8px; line-height: 1.4;">
                    <div style="font-size: 10.5px; font-weight: normal; color: #000;">
                        {{ $detail->product_id ? $detail->product->name : $detail->custom_name }}
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 10px; color: #333; margin-top: 1px;">
                        <span>Rp{{ number_format($detail->price, 0, ',', '.') }} x {{ (float) $detail->quantity }}</span>
                        <span style="color: #000; font-size: 10px;">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="receipt-divider"></div>

        <!-- Totals -->
        <div class="receipt-totals">
            <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 11px;">
                <span>TOTAL:</span>
                <strong>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>TUNAI:</span>
                <span>Rp {{ number_format($transaction->payment_amount, 0, ',', '.') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>KEMBALI:</span>
                <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="receipt-divider"></div>

        <div style="text-align: center; font-size: 10px; margin-top: 10px; margin-bottom: 4px;">
            <p style="font-weight: normal; margin-bottom: 2px;">TERIMA KASIH</p>
            <p style="color: #555;">Atas Kunjungan Anda</p>
        </div>
    </div>

    <!-- Tombol hanya muncul di layar, tidak ikut tercetak -->
    <div class="no-print">
        <button onclick="window.print()" class="btn-screen btn-print">
            <i class="fa-solid fa-print"></i> Cetak Struk
        </button>
        <button onclick="window.close()" class="btn-screen btn-close">
            Tutup
        </button>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 600);
        });
    </script>
</body>
</html>

