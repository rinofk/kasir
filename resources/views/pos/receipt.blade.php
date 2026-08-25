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

        /* ===== SCREEN VIEW ===== */
        body {
            background-color: #e5e7eb;
            font-family: 'Outfit', monospace, sans-serif;
            display: block;  /* JANGAN pakai flex — menyebabkan height 3276mm saat print */
            padding: 30px 16px 80px 16px;
        }

        .receipt-paper {
            background: #fff;
            width: 210px; /* ~58mm pada 96dpi */
            margin: 0 auto; /* centering tanpa flexbox */
            padding: 12px 10px;
            font-size: 11px;
            line-height: 1.4;
            color: #111;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 8px;
        }

        .receipt-divider {
            border-top: 1px dashed #555;
            margin: 6px 0;
        }

        .receipt-totals {
            font-size: 11px;
            line-height: 1.6;
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
        /* Biarkan printer driver menentukan ukuran kertas (tidak set @page size) */
        @page {
            margin: 3mm 2mm;
        }

        @media print {
            html, body {
                background: none !important;
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
                width: auto !important;
                min-height: 0 !important;
                height: auto !important;
            }
            .receipt-paper {
                width: 100% !important;
                margin: 0 !important;
                box-shadow: none !important;
                padding: 2mm 3mm !important;
            }
            .receipt-paper * {
                word-break: break-word;
            }
            .flex-row {
                display: flex !important;
                justify-content: space-between !important;
            }
            .flex-row .flex-left {
                min-width: 0;
                flex: 1 1 auto;
                padding-right: 4px;
            }
            .flex-row .flex-right {
                flex: 0 0 auto;
                white-space: nowrap;
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
            <h2 style="font-size: 14px; font-weight: 700; text-transform: uppercase; margin-bottom: 3px;">
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
            <div class="flex-row" style="display: flex; justify-content: space-between;">
                <strong class="flex-left">No. Invoice:</strong>
                <span class="flex-right">{{ $transaction->invoice_number }}</span>
            </div>
            <div class="flex-row" style="display: flex; justify-content: space-between;">
                <strong class="flex-left">Waktu:</strong>
                <span class="flex-right">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex-row" style="display: flex; justify-content: space-between;">
                <strong class="flex-left">Kasir:</strong>
                <span class="flex-right">{{ $transaction->user->name }}</span>
            </div>
        </div>

        <div class="receipt-divider"></div>

        <!-- Items -->
        <div style="margin-bottom: 6px;">
            @foreach($transaction->details as $detail)
                <div style="margin-bottom: 8px; line-height: 1.4;">
                    <div style="font-size: 11px; font-weight: 600; color: #000;">
                        {{ $detail->product_id ? $detail->product->name : $detail->custom_name }}
                    </div>
                    <div class="flex-row" style="display: flex; justify-content: space-between; font-size: 10px; color: #333; margin-top: 1px;">
                        <span class="flex-left">Rp{{ number_format($detail->price, 0, ',', '.') }} x {{ (float) $detail->quantity }}</span>
                        <strong class="flex-right" style="color: #000; font-size: 11px;">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</strong>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="receipt-divider"></div>

        <!-- Totals -->
        <div class="receipt-totals">
            <div class="flex-row" style="display: flex; justify-content: space-between;">
                <span class="flex-left">TOTAL:</span>
                <strong class="flex-right">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</strong>
            </div>
            <div class="flex-row" style="display: flex; justify-content: space-between;">
                <span class="flex-left">TUNAI:</span>
                <span class="flex-right">Rp {{ number_format($transaction->payment_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex-row" style="display: flex; justify-content: space-between; font-weight: 700;">
                <span class="flex-left">KEMBALI:</span>
                <span class="flex-right">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="receipt-divider"></div>

        <div style="text-align: center; font-size: 10px; margin-top: 10px; margin-bottom: 4px;">
            <p style="font-weight: 700; margin-bottom: 2px;">TERIMA KASIH</p>
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

