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
            font-family: 'Courier New', Courier, monospace;
            display: block;
            padding: 30px 16px 80px 16px;
        }

        .receipt-paper {
            background: #fff;
            width: 216px; /* ~58mm pada 96dpi screen */
            margin: 0 auto;
            padding: 10px 8px;
            font-size: 11px;
            line-height: 1.4;
            color: #111;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 6px;
            font-family: 'Outfit', sans-serif;
        }

        .receipt-divider {
            border-top: 1px dashed #555;
            margin: 5px 0;
        }

        /* Tabel untuk item & total: kolom kiri fleksibel, kolom kanan lebar tetap */
        .r-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .r-table td {
            padding: 0;
            vertical-align: top;
        }
        .r-table .col-left {
            text-align: left;
        }
        .r-table .col-right {
            text-align: right;
            white-space: nowrap;
            width: 1%;          /* squeeze to content */
            padding-left: 4px;
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
        /* Eksplisit set ukuran kertas thermal 58mm */
        @page {
            size: 57mm auto;
            margin: 2mm 1mm;
        }

        @media print {
            html, body {
                background: none !important;
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 57mm !important;
                min-height: 0 !important;
                height: auto !important;
            }
            .receipt-paper {
                width: 57mm !important;
                margin: 0 !important;
                box-shadow: none !important;
                padding: 0 !important;
                font-size: 8pt !important;
            }
            .receipt-header {
                font-size: 8pt !important;
            }
            .r-table {
                font-size: 7.5pt !important;
                width: 100% !important;
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
            <h2 style="font-size: 13px; font-weight: 700; text-transform: uppercase; margin-bottom: 2px; font-family: 'Outfit', sans-serif;">
                {{ strtoupper(\App\Models\Setting::get('store_name', 'TOKO NINING')) }}
            </h2>
            <p style="font-size: 9px; color: #333; margin-bottom: 1px;">
                {{ \App\Models\Setting::get('store_address', 'Mentibar, Kecamatan Paloh, Kabupaten Sambas') }}
            </p>
            <p style="font-size: 9px; color: #333;">
                Telp: {{ \App\Models\Setting::get('store_phone', '0812-3456-7890') }}
            </p>
        </div>

        <div class="receipt-divider"></div>

        <!-- Info transaksi -->
        <table class="r-table" style="margin-bottom: 5px;">
            <tr>
                <td class="col-left"><strong>No. Invoice</strong></td>
                <td class="col-right">{{ $transaction->invoice_number }}</td>
            </tr>
            <tr>
                <td class="col-left"><strong>Waktu</strong></td>
                <td class="col-right">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="col-left"><strong>Kasir</strong></td>
                <td class="col-right">{{ $transaction->user->name }}</td>
            </tr>
        </table>

        <div class="receipt-divider"></div>

        <!-- Items -->
        <div style="margin-bottom: 5px;">
            @foreach($transaction->details as $detail)
                <div style="margin-bottom: 4px;">
                    {{-- Nama produk --}}
                    <div style="font-weight: 700;">
                        {{ $detail->product_id ? $detail->product->name : $detail->custom_name }}
                    </div>
                    {{-- qty x harga | subtotal --}}
                    <table class="r-table">
                        <tr>
                            <td class="col-left" style="color: #444;">
                                {{ (float) $detail->quantity }} x Rp{{ number_format($detail->price, 0, ',', '.') }}
                            </td>
                            <td class="col-right" style="font-weight: 700;">
                                Rp{{ number_format($detail->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach
        </div>

        <div class="receipt-divider"></div>

        <!-- Totals -->
        <table class="r-table" style="margin-bottom: 5px;">
            <tr>
                <td class="col-left">TOTAL</td>
                <td class="col-right"><strong>Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td class="col-left">TUNAI</td>
                <td class="col-right">Rp{{ number_format($transaction->payment_amount, 0, ',', '.') }}</td>
            </tr>
            <tr style="font-weight: 700;">
                <td class="col-left">KEMBALI</td>
                <td class="col-right">Rp{{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="receipt-divider"></div>

        <div style="text-align: center; font-size: 9px; margin-top: 6px; margin-bottom: 4px; font-family: 'Outfit', sans-serif;">
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
