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
            background-color: #d1d5db;
            font-family: 'Courier Prime', 'Courier New', monospace;
            display: block;
            padding: 30px 16px 100px 16px;
        }

        .receipt-paper {
            background: #fff;
            width: 230px;
            margin: 0 auto;
            padding: 14px 12px 16px 12px;
            font-size: 11px;
            line-height: 1.5;
            color: #000;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2), 0 8px 24px rgba(0,0,0,0.1);
        }

        .store-name {
            font-size: 15px;
            font-weight: 700;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }

        .store-info {
            text-align: center;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }

        .divider-dash  { border-top: 1px dashed #000; margin: 5px 0; }
        .divider-equal {
            font-size: 10px;
            color: #000;
            margin: 4px 0;
            overflow: hidden;
            white-space: nowrap;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            line-height: 1.6;
        }

        .item-row { margin-bottom: 5px; }
        .item-name { font-size: 11px; font-weight: 700; word-break: break-word; }
        .item-detail {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #333;
            padding-left: 4px;
        }
        .item-subtotal { font-weight: 700; color: #000; }

        .total-row {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            line-height: 1.7;
        }
        .total-row.grand {
            font-size: 13px;
            font-weight: 700;
        }
        .total-row.kembalian { font-weight: 700; }

        .receipt-footer {
            text-align: center;
            font-size: 10px;
            margin-top: 8px;
            line-height: 1.6;
        }
        .receipt-footer .thanks {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        /* ===== TOMBOL LAYAR ===== */
        .no-print {
            position: fixed;
            bottom: 24px;
            right: 24px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            z-index: 9999;
        }
        .btn-screen {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.25);
            transition: opacity 0.2s;
        }
        .btn-screen:hover { opacity: 0.9; }
        .btn-print { background: #1d4ed8; color: #fff; }
        .btn-close  { background: #fff; color: #374151; border: 1px solid #d1d5db; }

        /* ===== PRINT STYLES ===== */
        @page { margin: 2mm 1mm; }

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
                padding: 0 2px !important;
                box-shadow: none !important;
            }
            .no-print { display: none !important; }
        }
    </style>

</head>
<body>

<div class="receipt-paper">

    {{-- HEADER --}}
    <div class="store-name">
        {{ \App\Models\Setting::get('store_name', 'TOKO NINING') }}
    </div>
    <div class="store-info">
        <div>{{ \App\Models\Setting::get('store_address', 'Mentibar, Kec. Paloh, Kab. Sambas') }}</div>
        <div>Telp: {{ \App\Models\Setting::get('store_phone', '0812-3456-7890') }}</div>
    </div>

    <div class="divider-equal">================================</div>

    {{-- INFO TRANSAKSI --}}
    <div class="info-row">
        <span>No. Invoice</span>
        <span>{{ $transaction->invoice_number }}</span>
    </div>
    <div class="info-row">
        <span>Tanggal</span>
        <span>{{ $transaction->created_at->format('d/m/Y') }}</span>
    </div>
    <div class="info-row">
        <span>Jam</span>
        <span>{{ $transaction->created_at->format('H:i') }}</span>
    </div>
    <div class="info-row">
        <span>Kasir</span>
        <span>{{ $transaction->user->name }}</span>
    </div>

    <div class="divider-equal">================================</div>

    {{-- HEADER KOLOM --}}
    <div style="display:flex; justify-content:space-between; font-size:10px; font-weight:700; margin-bottom:2px;">
        <span>NAMA ITEM</span>
        <span>SUBTOTAL</span>
    </div>
    <div class="divider-dash"></div>

    {{-- DAFTAR ITEM --}}
    @foreach($transaction->details as $detail)
    <div class="item-row">
        <div class="item-name">
            {{ $detail->product_id ? $detail->product->name : $detail->custom_name }}
        </div>
        <div class="item-detail">
            <span>{{ (float)$detail->quantity }} x Rp{{ number_format($detail->price, 0, ',', '.') }}</span>
            <span class="item-subtotal">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</span>
        </div>
    </div>
    @endforeach

    <div class="divider-equal">================================</div>

    {{-- TOTALS --}}
    <div class="total-row grand">
        <span>TOTAL</span>
        <span>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
    </div>
    <div class="divider-dash"></div>
    <div class="total-row">
        <span>Tunai</span>
        <span>Rp {{ number_format($transaction->payment_amount, 0, ',', '.') }}</span>
    </div>
    <div class="total-row kembalian">
        <span>Kembali</span>
        <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
    </div>

    <div class="divider-equal">================================</div>

    {{-- FOOTER --}}
    <div class="receipt-footer">
        <div class="thanks">*** TERIMA KASIH ***</div>
        <div style="margin-top:3px; color:#555;">Atas kunjungan dan kepercayaan Anda</div>
        <div style="margin-top:6px; font-size:9px; color:#888;">
            Dicetak: {{ $transaction->created_at->format('d/m/Y H:i:s') }}
        </div>
    </div>

</div>

{{-- Tombol hanya tampil di layar --}}
<div class="no-print">
    <button onclick="window.print()" class="btn-screen btn-print">
        🖨️ &nbsp;Cetak Struk
    </button>
    <button onclick="window.close()" class="btn-screen btn-close">
        ✕ &nbsp;Tutup
    </button>
</div>

<script>
    window.addEventListener('load', function() {
        setTimeout(function() { window.print(); }, 700);
    });
</script>
</body>
</html>
