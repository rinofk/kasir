@extends('layouts.app')

@section('title', 'Pengaturan Toko')
@section('header_title', 'Pengaturan Toko & Informasi Struk')

@section('content')
    <div style="max-width: 900px; margin: 0 auto;">
        
        <div class="card" style="box-shadow: var(--shadow-md); border-radius: var(--radius-md);">
            <div class="card-header" style="background: linear-gradient(to right, #f8fafc, #ffffff); padding: 20px 24px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: var(--accent-light); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div>
                        <h3 class="card-title" style="font-size: 18px; margin: 0;">Pengaturan Identitas Toko</h3>
                        <p style="font-size: 13px; color: var(--text-secondary); margin-top: 2px;">
                            Ubah nama toko, alamat, dan nomor kontak yang akan ditampilkan di header aplikasi dan cetakan struk belanja.
                        </p>
                    </div>
                </div>
            </div>

            <div class="card-body" style="padding: 28px;">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">
                        
                        <!-- Left Column: Form Controls -->
                        <div style="display: flex; flex-direction: column; gap: 20px;">
                            
                            <!-- Nama Toko -->
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="store_name" class="form-label" style="font-weight: 600; display: flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid fa-shop" style="color: var(--accent);"></i> Nama Toko <span style="color: var(--danger);">*</span>
                                </label>
                                <input type="text" 
                                       id="store_name" 
                                       name="store_name" 
                                       class="form-control @error('store_name') is-invalid @enderror" 
                                       value="{{ old('store_name', $storeName) }}" 
                                       placeholder="Contoh: Toko Nining" 
                                       required 
                                       style="font-size: 15px; padding: 12px 14px;">
                                @error('store_name')
                                    <span style="color: var(--danger); font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Nomor HP / Telepon -->
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="store_phone" class="form-label" style="font-weight: 600; display: flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid fa-phone" style="color: var(--accent);"></i> Nomor HP / WhatsApp / Telepon <span style="color: var(--danger);">*</span>
                                </label>
                                <input type="text" 
                                       id="store_phone" 
                                       name="store_phone" 
                                       class="form-control @error('store_phone') is-invalid @enderror" 
                                       value="{{ old('store_phone', $storePhone) }}" 
                                       placeholder="Contoh: 0812-3456-7890" 
                                       required 
                                       style="font-size: 15px; padding: 12px 14px;">
                                @error('store_phone')
                                    <span style="color: var(--danger); font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Alamat Toko -->
                            <div class="form-group" style="margin-bottom: 0;">
                                <label for="store_address" class="form-label" style="font-weight: 600; display: flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid fa-location-dot" style="color: var(--accent);"></i> Alamat Lengkap Toko <span style="color: var(--danger);">*</span>
                                </label>
                                <textarea id="store_address" 
                                          name="store_address" 
                                          rows="4" 
                                          class="form-control @error('store_address') is-invalid @enderror" 
                                          placeholder="Masukkan alamat toko secara lengkap..." 
                                          required 
                                          style="font-size: 14px; padding: 12px 14px; resize: vertical; line-height: 1.5;">{{ old('store_address', $storeAddress) }}</textarea>
                                @error('store_address')
                                    <span style="color: var(--danger); font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                        <!-- Right Column: Live Receipt Preview Box -->
                        <div>
                            <label class="form-label" style="font-weight: 600; display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                                <i class="fa-solid fa-receipt" style="color: var(--accent);"></i> Live Preview Header Struk
                            </label>
                            
                            <div style="background-color: #ffffff; border: 2px dashed var(--border-color); border-radius: var(--radius-md); padding: 20px; text-align: center; box-shadow: var(--shadow-sm); font-family: monospace;">
                                <div style="border-bottom: 1px dashed #cbd5e1; padding-bottom: 12px; margin-bottom: 12px;">
                                    <h4 id="previewStoreName" style="font-size: 16px; margin: 0 0 4px 0; font-weight: 700; text-transform: uppercase; color: #000;">
                                        {{ strtoupper($storeName) }}
                                    </h4>
                                    <p id="previewStoreAddress" style="font-size: 11px; margin: 0 0 4px 0; color: #475569; white-space: pre-line;">
                                        {{ $storeAddress }}
                                    </p>
                                    <p id="previewStorePhone" style="font-size: 11px; margin: 0; color: #475569;">
                                        Telp: {{ $storePhone }}
                                    </p>
                                </div>
                                <div style="font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">
                                    --- Contoh Tampilan Cetak Struk ---
                                </div>
                            </div>

                            <div style="margin-top: 16px; background: var(--bg-primary); border-radius: var(--radius-sm); padding: 12px 16px; border: 1px solid var(--border-color); font-size: 13px; color: var(--text-secondary); display: flex; gap: 10px; align-items: flex-start;">
                                <i class="fa-solid fa-circle-info" style="color: var(--accent); margin-top: 2px; font-size: 16px;"></i>
                                <span>Informasi toko ini akan digunakan secara otomatis saat kasir melakukan pencetakan struk belanja transaksi POS.</span>
                            </div>
                        </div>

                    </div>

                    <div style="margin-top: 32px; padding-top: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 12px;">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary" style="padding: 10px 20px;">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 24px; font-weight: 600;">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        // Live preview interaction
        const inputName = document.getElementById('store_name');
        const inputPhone = document.getElementById('store_phone');
        const inputAddress = document.getElementById('store_address');

        const previewName = document.getElementById('previewStoreName');
        const previewPhone = document.getElementById('previewStorePhone');
        const previewAddress = document.getElementById('previewStoreAddress');

        inputName.addEventListener('input', function() {
            previewName.textContent = (this.value.trim() || 'NAMA TOKO').toUpperCase();
        });

        inputPhone.addEventListener('input', function() {
            previewPhone.textContent = 'Telp: ' + (this.value.trim() || '-');
        });

        inputAddress.addEventListener('input', function() {
            previewAddress.textContent = this.value.trim() || 'Alamat Toko';
        });
    </script>
@endsection
