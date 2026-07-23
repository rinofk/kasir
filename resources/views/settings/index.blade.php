@extends('layouts.app')

@section('title', 'Pengaturan Toko')
@section('header_title', 'Pengaturan Toko & Sistem POS')

@section('content')
    <div style="max-width: 1100px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
        
        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- CARD 1: Identitas & Logo Toko -->
            <div class="card" style="box-shadow: var(--shadow-sm); border-radius: var(--radius-md); margin-bottom: 24px;">
                <div class="card-header" style="background: #ffffff; padding: 20px 24px; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: var(--accent-light); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <div>
                            <h3 class="card-title" style="font-size: 16px; margin: 0; font-weight: 700;">Identitas & Branding Toko</h3>
                            <p style="font-size: 13px; color: var(--text-secondary); margin: 2px 0 0 0;">
                                Nama toko, kontak, alamat, dan logo yang akan ditampilkan pada landing page, struk belanja, dan aplikasi.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body" style="padding: 24px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <!-- Nama Toko -->
                        <div>
                            <label for="store_name" class="form-label" style="font-weight: 600;">
                                <i class="fa-solid fa-shop" style="color: var(--accent);"></i> Nama Toko <span style="color: var(--danger);">*</span>
                            </label>
                            <input type="text" id="store_name" name="store_name" class="form-control" value="{{ old('store_name', $storeName) }}" required style="font-size: 14px; padding: 10px 14px;">
                            @error('store_name')
                                <span style="color: var(--danger); font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Nomor HP / WhatsApp -->
                        <div>
                            <label for="store_phone" class="form-label" style="font-weight: 600;">
                                <i class="fa-solid fa-phone" style="color: var(--accent);"></i> Nomor HP / WhatsApp <span style="color: var(--danger);">*</span>
                            </label>
                            <input type="text" id="store_phone" name="store_phone" class="form-control" value="{{ old('store_phone', $storePhone) }}" required style="font-size: 14px; padding: 10px 14px;">
                            @error('store_phone')
                                <span style="color: var(--danger); font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Alamat Toko -->
                    <div style="margin-bottom: 20px;">
                        <label for="store_address" class="form-label" style="font-weight: 600;">
                            <i class="fa-solid fa-location-dot" style="color: var(--accent);"></i> Alamat Lengkap Toko <span style="color: var(--danger);">*</span>
                        </label>
                        <textarea id="store_address" name="store_address" rows="2" class="form-control" required style="font-size: 14px; padding: 10px 14px; resize: vertical;">{{ old('store_address', $storeAddress) }}</textarea>
                        @error('store_address')
                            <span style="color: var(--danger); font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Section Logo, Icon & Favicon Grid (3 columns) -->
                    <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 20px;">
                        <h4 style="font-size: 14px; font-weight: 700; color: var(--text-primary); margin: 0 0 16px 0; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-icons" style="color: var(--accent);"></i> Logo, Ikon & Favicon Browser
                        </h4>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                            <!-- Ikon Simbol -->
                            <div style="background: #ffffff; padding: 14px; border-radius: 8px; border: 1px solid var(--border-color);">
                                <label for="store_icon" style="font-size: 12px; font-weight: 700; color: var(--text-primary); display: block; margin-bottom: 8px;">Pilih Ikon Simbol Toko</label>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    <select id="store_icon" name="store_icon" class="form-control" style="font-size: 13px; padding: 8px 10px; flex-grow: 1;" onchange="updateIconPreview(this.value)">
                                        <option value="fa-store" {{ old('store_icon', $storeIcon) == 'fa-store' ? 'selected' : '' }}>🏪 Bangunan Toko</option>
                                        <option value="fa-shop" {{ old('store_icon', $storeIcon) == 'fa-shop' ? 'selected' : '' }}>🛒 Toko Belanja</option>
                                        <option value="fa-cart-shopping" {{ old('store_icon', $storeIcon) == 'fa-cart-shopping' ? 'selected' : '' }}>🛒 Keranjang</option>
                                        <option value="fa-basket-shopping" {{ old('store_icon', $storeIcon) == 'fa-basket-shopping' ? 'selected' : '' }}>🧺 Keranjang Ritel</option>
                                        <option value="fa-building-storefront" {{ old('store_icon', $storeIcon) == 'fa-building-storefront' ? 'selected' : '' }}>🏬 Ritel Super</option>
                                        <option value="fa-bag-shopping" {{ old('store_icon', $storeIcon) == 'fa-bag-shopping' ? 'selected' : '' }}>🛍️ Tas Belanja</option>
                                        <option value="fa-boxes-packing" {{ old('store_icon', $storeIcon) == 'fa-boxes-packing' ? 'selected' : '' }}>📦 Stok Paket</option>
                                    </select>
                                    <div id="iconPreviewBox" style="width: 38px; height: 38px; background: #f1f5f9; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: var(--accent); flex-shrink: 0;">
                                        <i class="fa-solid {{ $storeIcon }}"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Logo Gambar -->
                            <div style="background: #ffffff; padding: 14px; border-radius: 8px; border: 1px solid var(--border-color);">
                                <label for="store_logo" style="font-size: 12px; font-weight: 700; color: var(--text-primary); display: block; margin-bottom: 8px;">Upload Logo Gambar (PNG/JPG/WEBP)</label>
                                <input type="file" id="store_logo" name="store_logo" accept="image/*" class="form-control" style="font-size: 12px; padding: 6px 8px;">
                                @if($storeLogo && file_exists(public_path($storeLogo)))
                                    <div style="margin-top: 8px; display: flex; align-items: center; gap: 8px; font-size: 12px;">
                                        <img src="{{ asset($storeLogo) }}" alt="Logo" style="height: 28px; object-fit: contain;">
                                        <label style="color: var(--danger); cursor: pointer; margin: 0; display: flex; align-items: center; gap: 4px;">
                                            <input type="checkbox" name="remove_store_logo" value="1"> Hapus
                                        </label>
                                    </div>
                                @endif
                                @error('store_logo')
                                    <span style="color: var(--danger); font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Upload Favicon -->
                            <div style="background: #ffffff; padding: 14px; border-radius: 8px; border: 1px solid var(--border-color);">
                                <label for="store_favicon" style="font-size: 12px; font-weight: 700; color: var(--text-primary); display: block; margin-bottom: 8px;">Upload Favicon Browser (.ICO/PNG)</label>
                                <input type="file" id="store_favicon" name="store_favicon" accept="image/*" class="form-control" style="font-size: 12px; padding: 6px 8px;">
                                @if($storeFavicon && file_exists(public_path($storeFavicon)))
                                    <div style="margin-top: 8px; display: flex; align-items: center; gap: 8px; font-size: 12px;">
                                        <img src="{{ asset($storeFavicon) }}" alt="Favicon" style="width: 20px; height: 20px; object-fit: contain;">
                                        <span style="color: var(--text-secondary);">Aktif</span>
                                        <label style="color: var(--danger); cursor: pointer; margin-left: auto; display: flex; align-items: center; gap: 4px;">
                                            <input type="checkbox" name="remove_store_favicon" value="1"> Hapus
                                        </label>
                                    </div>
                                @endif
                                @error('store_favicon')
                                    <span style="color: var(--danger); font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD 2: Rekening Bank Pembayaran (3 Column Grid) -->
            <div class="card" style="box-shadow: var(--shadow-sm); border-radius: var(--radius-md); margin-bottom: 24px;">
                <div class="card-header" style="background: #ffffff; padding: 20px 24px; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(79, 70, 229, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                        <div>
                            <h3 class="card-title" style="font-size: 16px; margin: 0; font-weight: 700;">Rekening Bank Pembayaran</h3>
                            <p style="font-size: 13px; color: var(--text-secondary); margin: 2px 0 0 0;">
                                Nomor rekening bank yang ditampilkan pada landing page publik untuk transfer pembayaran pembeli.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body" style="padding: 24px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                        
                        <!-- Bank BRI -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; font-weight: 700; color: #00529C; font-size: 14px;">
                                <i class="fa-solid fa-credit-card"></i> BANK BRI
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                <div>
                                    <label style="font-size: 11px; font-weight: 600; color: var(--text-secondary);">No. Rekening BRI</label>
                                    <input type="text" name="bank_bri_number" class="form-control" value="{{ old('bank_bri_number', $bankBriNumber) }}" placeholder="1234-0100-0123-530" style="font-size: 13px; padding: 8px 10px;">
                                </div>
                                <div>
                                    <label style="font-size: 11px; font-weight: 600; color: var(--text-secondary);">Atas Nama (A/N)</label>
                                    <input type="text" name="bank_bri_holder" class="form-control" value="{{ old('bank_bri_holder', $bankBriHolder) }}" placeholder="Nama Pemilik" style="font-size: 13px; padding: 8px 10px;">
                                </div>
                            </div>
                        </div>

                        <!-- Bank BNI -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; font-weight: 700; color: #F15A24; font-size: 14px;">
                                <i class="fa-solid fa-credit-card"></i> BANK BNI
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                <div>
                                    <label style="font-size: 11px; font-weight: 600; color: var(--text-secondary);">No. Rekening BNI</label>
                                    <input type="text" name="bank_bni_number" class="form-control" value="{{ old('bank_bni_number', $bankBniNumber) }}" placeholder="0987-6543-21" style="font-size: 13px; padding: 8px 10px;">
                                </div>
                                <div>
                                    <label style="font-size: 11px; font-weight: 600; color: var(--text-secondary);">Atas Nama (A/N)</label>
                                    <input type="text" name="bank_bni_holder" class="form-control" value="{{ old('bank_bni_holder', $bankBniHolder) }}" placeholder="Nama Pemilik" style="font-size: 13px; padding: 8px 10px;">
                                </div>
                            </div>
                        </div>

                        <!-- Bank BCA -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; font-weight: 700; color: #0060AF; font-size: 14px;">
                                <i class="fa-solid fa-credit-card"></i> BANK BCA
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                <div>
                                    <label style="font-size: 11px; font-weight: 600; color: var(--text-secondary);">No. Rekening BCA</label>
                                    <input type="text" name="bank_bca_number" class="form-control" value="{{ old('bank_bca_number', $bankBcaNumber) }}" placeholder="8880-1234-56" style="font-size: 13px; padding: 8px 10px;">
                                </div>
                                <div>
                                    <label style="font-size: 11px; font-weight: 600; color: var(--text-secondary);">Atas Nama (A/N)</label>
                                    <input type="text" name="bank_bca_holder" class="form-control" value="{{ old('bank_bca_holder', $bankBcaHolder) }}" placeholder="Nama Pemilik" style="font-size: 13px; padding: 8px 10px;">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- CARD 3: Sistem POS & Preview Struk (2 Columns) -->
            <div class="card" style="box-shadow: var(--shadow-sm); border-radius: var(--radius-md); margin-bottom: 24px;">
                <div class="card-header" style="background: #ffffff; padding: 20px 24px; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(79, 70, 229, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            <i class="fa-solid fa-cash-register"></i>
                        </div>
                        <div>
                            <h3 class="card-title" style="font-size: 16px; margin: 0; font-weight: 700;">Pengaturan Validasi Transaksi & Cetak Struk</h3>
                            <p style="font-size: 13px; color: var(--text-secondary); margin: 2px 0 0 0;">
                                Atur batasan stok barang pada transaksi kasir dan pantau live preview cetakan struk.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body" style="padding: 24px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">
                        <!-- Left: Validasi Stok -->
                        <div>
                            <label for="stock_validation" class="form-label" style="font-weight: 600;">
                                <i class="fa-solid fa-boxes-stacked" style="color: var(--accent);"></i> Kontrol Validasi Stok Produk <span style="color: var(--danger);">*</span>
                            </label>
                            <select id="stock_validation" name="stock_validation" class="form-control" style="font-size: 14px; padding: 10px 14px; font-weight: 600;">
                                <option value="1" {{ old('stock_validation', $stockValidation) == '1' ? 'selected' : '' }}>
                                    ✅ Aktif - Transaksi dibatasi sisa stok (Stok 0 tidak bisa dijual)
                                </option>
                                <option value="0" {{ old('stock_validation', $stockValidation) == '0' ? 'selected' : '' }}>
                                    🚫 Nonaktif - Transaksi bisa dilanjutkan meskipun stok kosong (0 / Minus)
                                </option>
                            </select>
                            <small style="color: var(--text-secondary); display: block; margin-top: 8px; font-size: 12px; line-height: 1.5;">
                                Bila diset <strong>Nonaktif</strong>, kasir dapat tetap memproses transaksi produk yang stoknya habis di sistem tanpa terblokir.
                            </small>
                        </div>

                        <!-- Right: Live Preview Struk -->
                        <div>
                            <label class="form-label" style="font-weight: 600; display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                                <i class="fa-solid fa-receipt" style="color: var(--accent);"></i> Live Preview Header Struk
                            </label>

                            <div style="background-color: #ffffff; border: 2px dashed var(--border-color); border-radius: var(--radius-md); padding: 16px; text-align: center; font-family: monospace;">
                                <div style="border-bottom: 1px dashed #cbd5e1; padding-bottom: 10px; margin-bottom: 10px;">
                                    <h4 id="previewStoreName" style="font-size: 15px; margin: 0 0 4px 0; font-weight: 700; text-transform: uppercase; color: #000;">
                                        {{ strtoupper($storeName) }}
                                    </h4>
                                    <p id="previewStoreAddress" style="font-size: 11px; margin: 0 0 4px 0; color: #475569; white-space: pre-line;">
                                        {{ $storeAddress }}
                                    </p>
                                    <p id="previewStorePhone" style="font-size: 11px; margin: 0; color: #475569;">
                                        Telp: {{ $storePhone }}
                                    </p>
                                </div>
                                <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase;">
                                    --- Struk Pembayaran Kasir ---
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 8px; margin-bottom: 40px;">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="padding: 12px 24px;">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary" style="padding: 12px 32px; font-weight: 600; font-size: 15px;">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Pengaturan
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        function updateIconPreview(iconClass) {
            const box = document.getElementById('iconPreviewBox');
            if (box) {
                box.innerHTML = `<i class="fa-solid ${iconClass}"></i>`;
            }
        }

        // Live preview interaction
        const inputName = document.getElementById('store_name');
        const inputPhone = document.getElementById('store_phone');
        const inputAddress = document.getElementById('store_address');

        const previewName = document.getElementById('previewStoreName');
        const previewPhone = document.getElementById('previewStorePhone');
        const previewAddress = document.getElementById('previewStoreAddress');

        if (inputName && previewName) {
            inputName.addEventListener('input', function() {
                previewName.textContent = (this.value.trim() || 'NAMA TOKO').toUpperCase();
            });
        }

        if (inputPhone && previewPhone) {
            inputPhone.addEventListener('input', function() {
                previewPhone.textContent = 'Telp: ' + (this.value.trim() || '-');
            });
        }

        if (inputAddress && previewAddress) {
            inputAddress.addEventListener('input', function() {
                previewAddress.textContent = this.value.trim() || 'Alamat Toko';
            });
        }
    </script>
@endsection
