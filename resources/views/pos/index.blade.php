@extends('layouts.app')

@section('title', 'Transaksi POS')
@section('header_title', 'Kasir POS (Point of Sale)')

@section('content')
    <div class="pos-wrapper">
        
        <!-- Left Side: Catalog / Scanning Workspace -->
        <div class="pos-catalog">
            
            <!-- Search Bar (Focus target for barcode scanner) -->
            <div class="catalog-search-bar">
                <input type="text" id="productSearch" class="form-control" placeholder="Arahkan kursor ke sini & scan barcode..." style="flex-grow: 1; font-size: 16px; padding: 12px 16px;" autofocus>
                <button type="button" id="clearSearch" class="btn btn-secondary" style="padding: 10px 14px;"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <!-- Scanner Guide Visual -->
            <div style="flex-grow: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; background-color: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 40px; text-align: center; box-shadow: var(--shadow-sm); margin-top: 16px;">
                <div style="width: 140px; height: 140px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), #6366f1); display: flex; align-items: center; justify-content: center; margin-bottom: 24px; box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);">
                    <i class="fa-solid fa-barcode" style="font-size: 60px; color: #ffffff;"></i>
                </div>
                <h2 style="font-size: 24px; font-weight: 700; margin-bottom: 8px; color: var(--text-primary);">Toko Nining POS</h2>
                <p style="color: var(--text-secondary); max-width: 320px; font-size: 14px; line-height: 1.5; margin-bottom: 20px;">Sistem kasir otomatis aktif. Silakan arahkan pemindai barcode ke produk untuk memasukkan barang ke keranjang.</p>
                <span class="badge badge-success" style="padding: 6px 12px; font-size: 13px;">
                    <i class="fa-solid fa-circle-check"></i> Pemindai Siap Menerima Input
                </span>
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
@endsection

@section('scripts')
    <script>
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
                if (newQty > item.stock) {
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
                    cart: cart.map(item => ({ id: item.id, qty: item.qty })),
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
    </script>
@endsection
