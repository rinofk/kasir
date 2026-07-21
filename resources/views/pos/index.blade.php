@extends('layouts.app')

@section('title', 'Transaksi POS')
@section('header_title', 'Kasir POS (Point of Sale)')

@section('content')
    <div class="pos-wrapper">
        
        <!-- Left Side: Catalog / Scanning Workspace -->
        <div class="pos-catalog">
            
            <!-- Search Bar (Focus target for barcode scanner) -->
            <div class="catalog-search-bar" style="display: flex; gap: 8px; width: 100%;">
                <input type="text" id="productSearch" class="form-control" placeholder="Arahkan kursor ke sini & scan barcode..." style="flex-grow: 1; font-size: 16px; padding: 12px 16px;" autofocus>
                <button type="button" id="clearSearch" class="btn btn-secondary" style="padding: 10px 14px;" title="Bersihkan"><i class="fa-solid fa-xmark"></i></button>
                <button type="button" onclick="openManualItemModal()" class="btn btn-secondary" style="padding: 10px 14px; display: flex; align-items: center; gap: 6px; white-space: nowrap;" title="Tambah Barang Manual">
                    <i class="fa-solid fa-keyboard"></i> <span>Barang Manual</span>
                </button>
            </div>

            <!-- Scanner Guide Visual -->
            <div style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 40px; text-align: center; box-shadow: var(--shadow-sm); margin-top: 16px;">
                <button type="button" onclick="startCameraScanner()" style="border: none; background: none; padding: 0; cursor: pointer; outline: none; transition: transform 0.2s;" onmousedown="this.style.transform='scale(0.95)'" onmouseup="this.style.transform='scale(1)'">
                    <div style="width: 140px; height: 140px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), #6366f1); display: flex; align-items: center; justify-content: center; margin-bottom: 24px; box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4); position: relative;">
                        <i class="fa-solid fa-barcode" style="font-size: 50px; color: #ffffff;"></i>
                        <i class="fa-solid fa-camera" style="font-size: 18px; color: #ffffff; position: absolute; bottom: 8px; right: 8px; background: var(--success); border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border: 3px solid var(--bg-secondary);"></i>
                    </div>
                </button>
                <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 8px; color: var(--text-primary);">Toko Nining POS</h2>
                <p style="color: var(--text-secondary); max-width: 320px; font-size: 14px; line-height: 1.5; margin-bottom: 20px;">
                    Scan menggunakan scanner hardware Anda, atau <strong>klik logo bulat di atas</strong> untuk scan menggunakan kamera HP.
                </p>
                <button type="button" onclick="startCameraScanner()" class="btn btn-primary" style="display: flex; align-items: center; gap: 8px; justify-content: center; width: 100%; max-width: 250px; padding: 12px;">
                    <i class="fa-solid fa-camera"></i> Buka Kamera Scanner
                </button>
            </div>

            <!-- Hidden Products Catalog Grid (Required for Vanilla JS lookup) -->
            <div id="catalogGrid" style="display: none;">
                @foreach($products as $prod)
                    <div class="product-card {{ $prod->stock <= 0 ? 'out-of-stock' : '' }}" 
                         data-id="{{ $prod->id }}"
                         data-code="{{ $prod->code }}"
                         data-name="{{ $prod->name }}"
                         data-price="{{ (float) $prod->selling_price }}"
                         data-stock="{{ $prod->stock }}"
                         data-category="{{ $prod->category_id }}">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Side: Invoice Cart -->
        <div class="pos-cart">
            <div class="pos-cart-header">
                <h3 style="font-size: 16px; font-weight: 700;"><i class="fa-solid fa-shopping-cart"></i> Keranjang Belanja</h3>
                <button type="button" onclick="clearCart()" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px; color: var(--danger); border-color: rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.05);">
                    <i class="fa-solid fa-trash-can"></i> Kosongkan
                </button>
            </div>

            <div class="pos-cart-body" id="cartContainer">
                <div style="margin: auto; text-align: center; color: var(--text-secondary);" id="emptyCartMessage">
                    <i class="fa-solid fa-basket-shopping" style="font-size: 40px; margin-bottom: 12px; color: #cbd5e1;"></i>
                    <p>Keranjang masih kosong.<br>Pilih produk di sebelah kiri.</p>
                </div>
            </div>

            <div class="pos-cart-footer">
                <div class="pos-total-row">
                    <span>Subtotal</span>
                    <span id="txtSubtotal">Rp 0</span>
                </div>
                <div class="pos-total-row grand-total">
                    <span>Total Belanja</span>
                    <span id="txtTotal">Rp 0</span>
                </div>

                <div class="form-group" style="margin-bottom: 0; margin-top: 8px;">
                    <label for="paymentAmount" class="form-label">Uang Tunai Pembayaran (Rp)</label>
                    <input type="text" id="paymentAmount" inputmode="numeric" class="form-control" placeholder="Masukkan jumlah uang..." style="font-size: 18px; font-weight: 700; padding: 12px;" disabled>
                </div>

                <div class="pos-total-row" style="margin-top: 4px;">
                    <span style="font-weight: 600;">Kembalian</span>
                    <span id="txtChange" style="font-size: 18px; font-weight: 700; color: var(--success);">Rp 0</span>
                </div>

                <button type="button" id="btnCheckout" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 16px;" disabled>
                    <i class="fa-solid fa-cash-register"></i> &nbsp; Bayar Sekarang & Selesaikan
                </button>
            </div>
        </div>

    </div>

    <!-- Success Checkout Modal -->
    <div id="checkoutSuccessModal" class="modal">
        <div class="modal-content" style="max-width: 420px; text-align: center;">
            <div class="modal-body" style="padding: 40px 32px;">
                <i class="fa-regular fa-circle-check" style="font-size: 64px; color: var(--success); margin-bottom: 20px;"></i>
                <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 8px;">Transaksi Berhasil!</h2>
                <p style="color: var(--text-secondary); margin-bottom: 24px; font-size: 14px;">Nomor Invoice: <strong id="successInvoiceNum" style="color: var(--text-primary);"></strong></p>
                
                <div style="background-color: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 16px; margin-bottom: 32px; display: flex; flex-direction: column; gap: 8px;">
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span style="color: var(--text-secondary);">Total Belanja:</span>
                        <strong id="successTotal">Rp 0</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span style="color: var(--text-secondary);">Bayar:</span>
                        <span id="successPayment">Rp 0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; border-top: 1px dashed var(--border-color); padding-top: 8px;">
                        <span style="color: var(--text-secondary); font-weight: 600;">Kembalian:</span>
                        <strong id="successChange" style="color: var(--success);">Rp 0</strong>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <a href="#" id="btnPrintReceipt" target="_blank" onclick="closeSuccessModal()" class="btn btn-primary" style="padding: 12px;">
                        <i class="fa-solid fa-print"></i> Cetak Struk Belanja
                    </a>
                    <button type="button" onclick="closeSuccessModal()" class="btn btn-secondary" style="padding: 12px;">
                        Transaksi Baru
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Camera Scanner Modal -->
    <div id="cameraScannerModal" class="modal">
        <div class="modal-content" style="max-width: 500px; padding: 20px;">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-camera"></i> Scan Barcode Kamera</h3>
                <button type="button" onclick="closeCameraScanner()" class="modal-close">&times;</button>
            </div>
            <div class="modal-body" style="padding: 16px 0 0 0;">
                <div id="reader" style="width: 100%; background: #000; border-radius: var(--radius-md); overflow: hidden;"></div>
                <div style="text-align: center; margin-top: 16px;">
                    <p style="font-size: 13px; color: var(--text-secondary); margin-bottom: 12px;">Posisikan barcode produk di dalam kotak pemindai kamera</p>
                    <button type="button" onclick="closeCameraScanner()" class="btn btn-secondary" style="width: 100%; padding: 12px;">Tutup Kamera</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Manual Item Modal -->
    <div id="manualItemModal" class="modal">
        <div class="modal-content" style="max-width: 450px; padding: 20px;">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-keyboard"></i> Tambah Barang Manual</h3>
                <button type="button" onclick="closeManualItemModal()" class="modal-close">&times;</button>
            </div>
            <form id="manualItemForm" onsubmit="addManualItemToCart(event)">
                <div class="modal-body" style="padding: 16px 0 0 0;">
                    <div class="form-group">
                        <label for="manual_name" class="form-label">Nama Barang</label>
                        <input type="text" id="manual_name" class="form-control" required placeholder="Contoh: Roti Tawar Nining" value="Barang Manual">
                    </div>
                    <div class="form-group">
                        <label for="manual_price" class="form-label">Harga Jual (Rp)</label>
                        <input type="text" id="manual_price" inputmode="numeric" class="form-control" required placeholder="Masukkan harga jual..." style="font-weight: bold; font-size: 16px;">
                    </div>
                    <div class="form-group">
                        <label for="manual_qty" class="form-label">Jumlah (Qty)</label>
                        <input type="number" id="manual_qty" class="form-control" required min="1" value="1">
                    </div>
                </div>
                <div class="modal-footer" style="padding: 16px 0 0 0; display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" onclick="closeManualItemModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Masukkan Keranjang</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode/html5-qrcode.min.js"></script>
    <script>
        let html5QrcodeScanner = null;

        function playBeep() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);

                oscillator.type = 'sine';
                oscillator.frequency.value = 1000;
                gainNode.gain.setValueAtTime(0.05, audioCtx.currentTime);

                oscillator.start();
                oscillator.stop(audioCtx.currentTime + 0.1);
            } catch (e) {
                console.error("Gagal memutar beep:", e);
            }
        }

        function startCameraScanner() {
            document.getElementById('cameraScannerModal').classList.add('active');
            
            html5QrcodeScanner = new Html5Qrcode("reader");
            
            const config = { 
                fps: 15, 
                qrbox: function(width, height) {
                    const minSize = Math.min(width, height);
                    const boxWidth = Math.floor(minSize * 0.85);
                    const boxHeight = Math.floor(boxWidth * 0.45);
                    return { width: boxWidth, height: boxHeight };
                }
            };

            html5QrcodeScanner.start(
                { facingMode: "environment" },
                config,
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.error("Gagal membuka kamera:", err);
                alert("Gagal mengakses kamera. Silakan periksa izin akses kamera.");
                closeCameraScanner();
            });
        }

        function onScanSuccess(decodedText, decodedResult) {
            playBeep();
            
            const query = decodedText.trim().toLowerCase();
            const card = Array.from(document.querySelectorAll('.product-card')).find(c => c.dataset.code.toLowerCase() === query);
            
            if (card) {
                if (card.classList.contains('out-of-stock')) {
                    Swal.fire({
                        title: 'Stok Habis',
                        text: `Produk '${card.dataset.name}' sedang kosong.`,
                        icon: 'error',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    const id = parseInt(card.dataset.id);
                    const code = card.dataset.code;
                    const name = card.dataset.name;
                    const price = parseFloat(card.dataset.price);
                    const stock = parseInt(card.dataset.stock);

                    addToCart(id, code, name, price, stock);
                    
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: `Ditambahkan: ${name}`,
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                }
            } else {
                Swal.fire({
                    title: 'Tidak Ditemukan',
                    text: `Barcode '${decodedText}' tidak terdaftar di sistem.`,
                    icon: 'warning',
                    timer: 2000,
                    showConfirmButton: false
                });
            }

            closeCameraScanner();
        }

        function onScanFailure(error) {
            // Ignore scan failures during capture
        }

        function closeCameraScanner() {
            if (html5QrcodeScanner) {
                try {
                    if (html5QrcodeScanner.isScanning) {
                        html5QrcodeScanner.stop().then(() => {
                            html5QrcodeScanner = null;
                            document.getElementById('cameraScannerModal').classList.remove('active');
                        }).catch(err => {
                            console.error("Gagal stop scanner:", err);
                            html5QrcodeScanner = null;
                            document.getElementById('cameraScannerModal').classList.remove('active');
                        });
                    } else {
                        html5QrcodeScanner = null;
                        document.getElementById('cameraScannerModal').classList.remove('active');
                    }
                } catch (e) {
                    console.error("Gagal menghentikan scanner secara aman:", e);
                    html5QrcodeScanner = null;
                    document.getElementById('cameraScannerModal').classList.remove('active');
                }
            } else {
                document.getElementById('cameraScannerModal').classList.remove('active');
            }
        }
        let cart = [];

        const catalogGrid = document.getElementById('catalogGrid');
        const cartContainer = document.getElementById('cartContainer');
        const emptyCartMessage = document.getElementById('emptyCartMessage');
        const txtSubtotal = document.getElementById('txtSubtotal');
        const txtTotal = document.getElementById('txtTotal');
        const paymentAmountInput = document.getElementById('paymentAmount');
        const txtChange = document.getElementById('txtChange');
        const btnCheckout = document.getElementById('btnCheckout');
        const productSearch = document.getElementById('productSearch');
        const clearSearch = document.getElementById('clearSearch');

        function formatRupiah(number) {
            return 'Rp ' + number.toLocaleString('id-ID');
        }

        catalogGrid.addEventListener('click', function(e) {
            const card = e.target.closest('.product-card');
            if (!card || card.classList.contains('out-of-stock')) return;

            const id = parseInt(card.dataset.id);
            const code = card.dataset.code;
            const name = card.dataset.name;
            const price = parseFloat(card.dataset.price);
            const stock = parseInt(card.dataset.stock);

            addToCart(id, code, name, price, stock);
        });

        function addToCart(id, code, name, price, stock) {
            const existingItem = cart.find(item => item.id === id);

            if (existingItem) {
                if (existingItem.qty >= stock) {
                    alert(`Stok tidak mencukupi. Sisa stok: ${stock}`);
                    return;
                }
                existingItem.qty += 1;
            } else {
                if (stock <= 0) {
                    alert("Produk habis.");
                    return;
                }
                cart.push({ id, code, name, price, qty: 1, stock });
            }

            renderCart();
        }

        function updateQty(id, delta) {
            const itemIndex = cart.findIndex(item => item.id === id);
            if (itemIndex === -1) return;

            const item = cart[itemIndex];
            const newQty = item.qty + delta;

            if (newQty <= 0) {
                cart.splice(itemIndex, 1);
            } else {
                const isManual = typeof item.id === 'string' && item.id.startsWith('manual_');
                if (!isManual && newQty > item.stock) {
                    alert(`Stok tidak mencukupi. Sisa stok: ${item.stock}`);
                    return;
                }
                item.qty = newQty;
            }

            renderCart();
        }

        function removeItem(id) {
            cart = cart.filter(item => item.id !== id);
            renderCart();
        }

        function clearCart() {
            if (cart.length === 0) return;
            if (confirm("Kosongkan keranjang belanja?")) {
                cart = [];
                renderCart();
            }
        }

        function renderCart() {
            if (cart.length === 0) {
                cartContainer.innerHTML = '';
                cartContainer.appendChild(emptyCartMessage);
                emptyCartMessage.style.display = 'block';
                paymentAmountInput.value = '';
                paymentAmountInput.disabled = true;
                txtChange.textContent = 'Rp 0';
                btnCheckout.disabled = true;
                txtSubtotal.textContent = 'Rp 0';
                txtTotal.textContent = 'Rp 0';
                return;
            }

            emptyCartMessage.style.display = 'none';
            
            let cartHtml = '';
            let grandTotal = 0;

            cart.forEach(item => {
                const subtotal = item.price * item.qty;
                grandTotal += subtotal;

                cartHtml += `
                    <div class="pos-cart-item">
                        <div class="pos-cart-item-info">
                            <div class="pos-cart-item-name">${item.name}</div>
                            <div class="pos-cart-item-price">${formatRupiah(item.price)}</div>
                        </div>
                        <div class="pos-cart-item-qty">
                            <button type="button" onclick="updateQty(${item.id}, -1)" class="qty-btn">-</button>
                            <span style="font-weight: 600; min-width: 20px; text-align: center;">${item.qty}</span>
                            <button type="button" onclick="updateQty(${item.id}, 1)" class="qty-btn">+</button>
                        </div>
                        <div class="pos-cart-item-subtotal">
                            ${formatRupiah(subtotal)}
                        </div>
                        <button type="button" onclick="removeItem(${item.id})" style="background: none; border: none; color: var(--danger); cursor: pointer; margin-left: 12px; font-size: 14px;">
                            <i class="fa-regular fa-trash-can"></i>
                        </button>
                    </div>
                `;
            });

            cartContainer.innerHTML = cartHtml;
            txtSubtotal.textContent = formatRupiah(grandTotal);
            txtTotal.textContent = formatRupiah(grandTotal);

            paymentAmountInput.disabled = false;
            calculateChange(grandTotal);
        }

        function calculateChange(total) {
            const cleanVal = paymentAmountInput.value.replace(/[^0-9]/g, '');
            const cash = parseFloat(cleanVal) || 0;
            const change = cash - total;

            if (change >= 0) {
                txtChange.textContent = formatRupiah(change);
                txtChange.style.color = 'var(--success)';
                btnCheckout.disabled = false;
            } else {
                txtChange.textContent = '-' + formatRupiah(Math.abs(change));
                txtChange.style.color = 'var(--danger)';
                btnCheckout.disabled = true;
            }
        }

        paymentAmountInput.addEventListener('input', function() {
            let cleanVal = this.value.replace(/[^0-9]/g, '');
            if (cleanVal !== '') {
                this.value = parseInt(cleanVal).toLocaleString('id-ID');
            } else {
                this.value = '';
            }

            let total = 0;
            cart.forEach(item => total += item.price * item.qty);
            calculateChange(total);
        });

        btnCheckout.addEventListener('click', function() {
            if (cart.length === 0) return;

            const cleanVal = paymentAmountInput.value.replace(/[^0-9]/g, '');
            const cash = parseFloat(cleanVal) || 0;
            let total = 0;
            cart.forEach(item => total += item.price * item.qty);

            if (cash < total) {
                alert("Uang pembayaran tidak mencukupi!");
                return;
            }

            btnCheckout.disabled = true;
            btnCheckout.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';

            fetch('/pos', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    cart: cart.map(item => ({ 
                        id: item.id, 
                        qty: item.qty,
                        name: item.name,
                        price: item.price
                    })),
                    payment_amount: cash
                })
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(res => {
                btnCheckout.disabled = false;
                btnCheckout.innerHTML = '<i class="fa-solid fa-cash-register"></i> &nbsp; Bayar Sekarang & Selesaikan';

                if (res.status === 200 && res.body.success) {
                    cart.forEach(item => {
                        const card = document.querySelector(`.product-card[data-id="${item.id}"]`);
                        if (card) {
                            let newStock = parseInt(card.dataset.stock) - item.qty;
                            card.dataset.stock = newStock;
                            const stockNumSpan = card.querySelector('.stock-num');
                            if (stockNumSpan) {
                                stockNumSpan.textContent = newStock;
                            }
                            if (newStock <= 0) {
                                card.classList.add('out-of-stock');
                                const stockSpan = card.querySelector('.product-card-stock');
                                if (stockSpan) stockSpan.outerHTML = '<span class="product-card-stock" style="color: var(--danger); font-weight: bold;">Habis</span>';
                            }
                        }
                    });

                    document.getElementById('successInvoiceNum').textContent = res.body.invoice_number;
                    document.getElementById('successTotal').textContent = formatRupiah(res.body.total_price);
                    document.getElementById('successPayment').textContent = formatRupiah(res.body.payment_amount);
                    document.getElementById('successChange').textContent = formatRupiah(res.body.change_amount);
                    document.getElementById('btnPrintReceipt').href = `/pos/receipt/${res.body.transaction_id}`;

                    document.getElementById('checkoutSuccessModal').classList.add('active');
                    
                    cart = [];
                    renderCart();
                } else {
                    alert(res.body.message || "Terjadi kesalahan saat memproses transaksi.");
                }
            })
            .catch(err => {
                btnCheckout.disabled = false;
                btnCheckout.innerHTML = '<i class="fa-solid fa-cash-register"></i> &nbsp; Bayar Sekarang & Selesaikan';
                console.error(err);
                alert("Terjadi kesalahan jaringan.");
            });
        });

        function closeSuccessModal() {
            document.getElementById('checkoutSuccessModal').classList.remove('active');
        }

        productSearch.addEventListener('input', function() {
            filterCatalog();
            
            const query = this.value.trim().toLowerCase();
            if (query.length >= 3) {
                const card = Array.from(document.querySelectorAll('.product-card')).find(c => c.dataset.code.toLowerCase() === query);
                if (card) {
                    if (card.classList.contains('out-of-stock')) {
                        return;
                    }
                    const id = parseInt(card.dataset.id);
                    const code = card.dataset.code;
                    const name = card.dataset.name;
                    const price = parseFloat(card.dataset.price);
                    const stock = parseInt(card.dataset.stock);

                    addToCart(id, code, name, price, stock);
                    
                    this.value = '';
                    filterCatalog();
                }
            }
        });

        productSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = this.value.trim().toLowerCase();
                if (!query) return;

                const card = Array.from(document.querySelectorAll('.product-card')).find(c => c.dataset.code.toLowerCase() === query);
                if (card) {
                    if (card.classList.contains('out-of-stock')) {
                        alert("Produk habis!");
                        return;
                    }
                    const id = parseInt(card.dataset.id);
                    const code = card.dataset.code;
                    const name = card.dataset.name;
                    const price = parseFloat(card.dataset.price);
                    const stock = parseInt(card.dataset.stock);

                    addToCart(id, code, name, price, stock);
                    this.value = '';
                    filterCatalog();
                } else {
                    alert("Produk dengan kode tersebut tidak ditemukan.");
                }
            }
        });

        clearSearch.addEventListener('click', function() {
            productSearch.value = '';
            filterCatalog();
        });

        const catChips = document.querySelectorAll('.cat-chip');
        catChips.forEach(chip => {
            chip.addEventListener('click', function() {
                catChips.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                filterCatalog();
            });
        });

        function filterCatalog() {
            const query = productSearch.value.toLowerCase().trim();
            const activeChip = document.querySelector('.cat-chip.active');
            const categoryId = activeChip ? activeChip.dataset.categoryId : 'all';

            const cards = document.querySelectorAll('.product-card');

            cards.forEach(card => {
                const name = card.dataset.name.toLowerCase();
                const code = card.dataset.code.toLowerCase();
                const cat = card.dataset.category;

                const matchQuery = name.includes(query) || code.includes(query);
                const matchCategory = categoryId === 'all' || cat === categoryId;

                if (matchQuery && matchCategory) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Manual Items Support
        function openManualItemModal() {
            document.getElementById('manual_name').value = 'Barang Manual';
            document.getElementById('manual_price').value = '';
            document.getElementById('manual_qty').value = '1';
            document.getElementById('manualItemModal').classList.add('active');
            
            setTimeout(() => {
                document.getElementById('manual_price').focus();
            }, 100);
        }

        function closeManualItemModal() {
            document.getElementById('manualItemModal').classList.remove('active');
        }

        function addManualItemToCart(e) {
            e.preventDefault();
            const name = document.getElementById('manual_name').value.trim() || 'Barang Manual';
            const priceVal = document.getElementById('manual_price').value.replace(/[^0-9]/g, '');
            const price = parseFloat(priceVal) || 0;
            const qty = parseInt(document.getElementById('manual_qty').value) || 1;

            if (price <= 0) {
                alert("Harga barang harus lebih besar dari 0.");
                return;
            }

            const id = 'manual_' + Date.now();
            
            cart.push({
                id: id,
                code: 'MANUAL',
                name: name,
                price: price,
                qty: qty,
                stock: 999999
            });

            renderCart();
            closeManualItemModal();
            
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `Ditambahkan: ${name}`,
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
            });
        }

        document.getElementById('manual_price').addEventListener('input', function() {
            let cleanVal = this.value.replace(/[^0-9]/g, '');
            if (cleanVal !== '') {
                this.value = parseInt(cleanVal).toLocaleString('id-ID');
            } else {
                this.value = '';
            }
        });
    </script>
@endsection
