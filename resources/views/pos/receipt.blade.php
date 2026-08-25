<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->invoice_number }}</title>
    <style>
        /* ===== RESET TOTAL ===== */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        /* ===== SCREEN ONLY ===== */
        body {
            background: #e5e7eb;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            padding: 24px 12px 80px;
        }

        .wrap {
            background: #fff;
            width: 226px;      /* ~60mm di 96dpi — preview layar */
            margin: 0 auto;
            padding: 10px 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,.15);
        }

        .center { text-align: center; }
        .bold   { font-weight: 700; }
        .small  { font-size: 10px; color: #444; }
        .divider { border-top: 1px dashed #555; margin: 5px 0; }

        /* Tabel dua kolom: kiri teks, kanan angka */
        table { width: 100%; border-collapse: collapse; }
        td    { padding: 1px 0; vertical-align: top; font-size: 11px; }
        .td-l { text-align: left;  }
        .td-r { text-align: right; white-space: nowrap; padding-left: 6px; }

        /* ===== TOMBOL LAYAR ===== */
        .no-print {
            position: fixed; bottom: 20px; right: 20px;
            display: flex; gap: 8px; z-index: 9999;
        }
        .btn {
            padding: 10px 18px; border: none; border-radius: 8px;
            font-size: 14px; font-weight: 600; cursor: pointer;
            display: flex; align-items: center; gap: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,.2);
        }
        .btn-print { background: #4f46e5; color: #fff; }
        .btn-close { background: #fff; color: #374151; }

        /* ===== PRINT ===== */
        @page {
            size: 58mm 210mm;  /* cocokkan dengan "Printer Paper(58 x 210mm)" di driver */
            margin: 0;         /* margin 0 — biarkan printer driver yg atur */
        }

        @media print {
            /* Reset body — hapus semua padding/height layar */
            html, body {
                background: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                font-size: 8pt !important;
                -webkit-print-color-adjust: exact;
                overflow: visible !important;
            }

            /* Sembunyikan tombol */
            .no-print { display: none !important; }

            /* Kotak struk: ikuti full lebar kertas, beri sedikit padding agar tidak mentok tepi */
            .wrap {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 1mm 2mm !important;  /* pengganti @page margin */
                box-shadow: none !important;
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
            }

            /* Tabel */
            table { width: 100% !important; }
            td    { font-size: 7.5pt !important; padding: 0 !important; }

            .small { font-size: 7pt !important; }
        }
    </style>
</head>
<body>

<div class="wrap">

    {{-- HEADER --}}
    <div class="center" style="margin-bottom:5px;">
        <div class="bold" style="font-size:13px; text-transform:uppercase; letter-spacing:.5px;">
            {{ strtoupper(\App\Models\Setting::get('store_name', 'TOKO NINING')) }}
        </div>
        <div class="small" style="margin-top:2px;">
            {{ \App\Models\Setting::get('store_address', 'Mentibar, Kecamatan Paloh, Kabupaten Sambas') }}
        </div>
        <div class="small">
            Telp: {{ \App\Models\Setting::get('store_phone', '0812-3456-7890') }}
        </div>
    </div>

    <div class="divider"></div>

    {{-- INFO TRANSAKSI --}}
    <table style="margin-bottom:4px;">
        <tr>
            <td class="td-l bold">No. Invoice</td>
            <td class="td-r">{{ $transaction->invoice_number }}</td>
        </tr>
        <tr>
            <td class="td-l bold">Waktu</td>
            <td class="td-r">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="td-l bold">Kasir</td>
            <td class="td-r">{{ $transaction->user->name }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    {{-- ITEM PRODUK --}}
    <div style="margin-bottom:4px;">
        @foreach($transaction->details as $detail)
        <div style="margin-bottom:3px;">
            {{-- Nama --}}
            <div class="bold">{{ $detail->product_id ? $detail->product->name : $detail->custom_name }}</div>
            {{-- qty × harga | subtotal --}}
            <table>
                <tr>
                    <td class="td-l small">
                        {{ (float)$detail->quantity }} x Rp{{ number_format($detail->price, 0, ',', '.') }}
                    </td>
                    <td class="td-r bold">
                        Rp{{ number_format($detail->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>
        @endforeach
    </div>

    <div class="divider"></div>

    {{-- TOTAL --}}
    <table style="margin-bottom:4px;">
        <tr>
            <td class="td-l bold">TOTAL</td>
            <td class="td-r bold">Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="td-l">TUNAI</td>
            <td class="td-r">Rp{{ number_format($transaction->payment_amount, 0, ',', '.') }}</td>
        </tr>
        <tr class="bold">
            <td class="td-l">KEMBALI</td>
            <td class="td-r">Rp{{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    {{-- FOOTER --}}
    <div class="center small" style="margin-top:5px; margin-bottom:4px;">
        <div class="bold">TERIMA KASIH</div>
        <div>Atas Kunjungan Anda</div>
    </div>

</div>

{{-- TOMBOL LAYAR --}}
<div class="no-print">
    <button onclick="window.print()" class="btn btn-print">&#x1F5A8; Cetak Struk</button>
    <button onclick="window.close()" class="btn btn-close">Tutup</button>
</div>

<script>
    window.addEventListener('load', function () {
        setTimeout(function () { window.print(); }, 500);
    });
</script>
</body>
</html>
