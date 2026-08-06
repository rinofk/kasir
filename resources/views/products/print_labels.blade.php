<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Harga</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        :root {
            --bg-primary: #f8fafc;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --accent: #4f46e5;
            --border-color: #cbd5e1;
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Outfit', sans-serif;
            margin: 0;
            padding: 20px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print-bar {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 24px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--accent);
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #4338ca;
        }

        .btn-secondary {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background-color: #e2e8f0;
        }

        .labels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(7.5cm, 1fr));
            gap: 16px;
            padding: 4px;
        }

        /* Shelftag Label Design */
        .shelftag-container {
            position: relative;
            width: 7.5cm;
            height: 4.2cm;
            box-sizing: border-box;
            background-color: #ffffff;
            border: 1.5px solid #000000;
            border-radius: 6px;
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        /* Cut helper dashed line around tags for printing */
        .shelftag-container::before {
            content: '';
            position: absolute;
            top: -4px;
            left: -4px;
            right: -4px;
            bottom: -4px;
            border: 1px dashed #cbd5e1;
            pointer-events: none;
            border-radius: 8px;
        }

        .shelftag-top-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2px;
        }

        .currency-symbol {
            font-size: 22px;
            font-weight: 500;
            color: #000000;
            line-height: 1;
            margin-top: 6px;
        }

        .price-value {
            font-size: 46px;
            font-weight: 500;
            color: #000000;
            line-height: 1;
            letter-spacing: -1.5px;
        }

        .product-name {
            font-size: 12px;
            font-weight: 700;
            color: #000000;
            line-height: 1.35;
            margin: 4px 0 6px 0;
            height: 32px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: uppercase;
        }

        .shelftag-bottom-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
        }

        .barcode-container {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            width: 4.5cm;
        }

        .barcode-svg {
            max-width: 100%;
            height: 38px;
        }

        .print-date-container {
            text-align: right;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }

        .date-label {
            font-size: 7px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 1px;
            letter-spacing: 0.2px;
        }

        .date-value {
            font-size: 9px;
            font-weight: 700;
            color: #000000;
            white-space: nowrap;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
                margin: 0.5cm;
            }

            .no-print-bar {
                display: none !important;
            }

            .labels-grid {
                display: grid;
                grid-template-columns: repeat(3, 7.5cm);
                gap: 12px;
                padding: 0;
            }

            .shelftag-container {
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            .shelftag-container::before {
                border-color: #94a3b8;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <button onclick="window.close();" class="btn btn-secondary">
                <i class="fa-solid fa-xmark"></i> Tutup
            </button>
            <span style="font-weight: 700; font-size: 16px;">Pratinjau Cetak Label Harga ({{ $products->count() }} Produk)</span>
        </div>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fa-solid fa-print"></i> Cetak Label Sekarang
        </button>
    </div>

    <div class="labels-grid">
        @foreach($products as $product)
            <div class="shelftag-container">
                <div class="shelftag-top-row">
                    <div class="currency-symbol">Rp</div>
                    <div class="price-value">{{ number_format($product->selling_price, 0, ',', '.') }}</div>
                </div>
                <div class="product-name">
                    {{ $product->name }}
                </div>
                <div class="shelftag-bottom-row">
                    <div class="barcode-container">
                        <svg class="barcode-svg" 
                             data-code="{{ $product->code }}"></svg>
                    </div>
                    <div class="print-date-container">
                        <div class="date-label">Tgl Cetak</div>
                        <div class="date-value">{{ date('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const barcodes = document.querySelectorAll('.barcode-svg');
            barcodes.forEach(function(svg) {
                const code = svg.getAttribute('data-code');
                try {
                    JsBarcode(svg, code, {
                        format: "CODE128",
                        lineColor: "#000",
                        width: 1.6,
                        height: 38,
                        displayValue: true,
                        fontSize: 10,
                        fontOptions: "bold",
                        margin: 0
                    });
                } catch (err) {
                    console.error("Gagal men-generate barcode untuk kode:", code, err);
                }
            });
        });
    </script>
</body>
</html>
