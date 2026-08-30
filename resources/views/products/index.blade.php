@extends('layouts.app')

@section('title', 'Produk')
@section('header_title', 'Kelola Produk & Inventoris')

@section('styles')
<style>
    .cat-tab.active {
        background-color: var(--accent) !important;
        color: #ffffff !important;
        border-color: var(--accent) !important;
        box-shadow: 0 4px 10px -2px rgba(79, 70, 229, 0.3);
    }
    .cat-tab:hover:not(.active) {
        background-color: var(--accent-light) !important;
        color: var(--accent) !important;
        border-color: var(--accent-light) !important;
    }
    .category-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-color);
        align-items: center;
        background: #fafafa;
    }
    #filter_category_select {
        width: 180px;
    }
    .clickable-product-detail {
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .clickable-product-detail:hover {
        opacity: 0.75;
        text-decoration: underline;
    }
    .col-show-mobile {
        display: none !important;
    }

    @media (max-width: 768px) {
        .category-tabs {
            display: none !important;
        }
        #filter_category_select {
            display: block !important;
            width: 100% !important;
        }
        .col-hide-mobile {
            display: none !important;
        }
        .col-show-mobile {
            display: table-cell !important;
        }
    }
    @media (min-width: 769px) {
        #filter_category_select {
            display: none !important;
        }
        .category-tabs {
            display: flex !important;
        }
    }

    /* Modal scroll overrides to prevent unreachable save buttons on mobile/small screens */
    .modal {
        overflow-y: auto !important;
        padding: 16px 8px !important;
    }
    .modal.active {
        display: flex !important;
        align-items: flex-start !important;
    }
    .modal-content {
        margin: 20px auto !important;
        max-height: none !important;
    }
</style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div style="display: flex; gap: 12px; align-items: center; width: 100%; justify-content: space-between; flex-wrap: wrap;">
                <form id="searchForm" action="{{ route('products.index') }}" method="GET" style="display: flex; gap: 8px; flex-grow: 1; max-width: 600px; align-items: center;">
                    <input type="hidden" name="category_id" id="filter_category_input" value="{{ $categoryId }}">
                    
                    <div style="position: relative; flex-grow: 1; display: flex; align-items: center;">
                        <input type="text" id="productSearch" name="search" class="form-control" placeholder="Cari barcode, kode, nama..." value="{{ $search }}" style="flex-grow: 1; padding-right: 64px;">
                        
                        <div style="position: absolute; right: 8px; display: flex; align-items: center; gap: 4px;">
                            <button type="button" id="clearSearch" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; display: {{ $search ? 'flex' : 'none' }}; align-items: center; justify-content: center; width: 24px; height: 24px; outline: none; font-size: 15px; padding: 0;" title="Hapus pencarian">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </button>
                            <button type="button" onclick="startCameraScanner('productSearch')" style="background: none; border: none; color: var(--accent); cursor: pointer; display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; outline: none; font-size: 16px; padding: 0;" title="Scan Barcode Kamera">
                                <i class="fa-solid fa-barcode"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Cari (Icon Only) -->
                    <button type="submit" class="btn btn-secondary" style="width: 42px; height: 42px; min-height: 42px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;" title="Cari">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>

                    <!-- Tombol Filter Kategori (Icon Only, Sebelah Icon Cari) -->
                    <button type="button" onclick="openCategoryFilterDialog()" class="btn {{ $categoryId ? 'btn-primary' : 'btn-secondary' }}" style="width: 42px; height: 42px; min-height: 42px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative;" title="Filter Kategori">
                        <i class="fa-solid fa-filter"></i>
                        @if($categoryId)
                            <span style="position: absolute; top: 2px; right: 2px; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; border: 1.5px solid #fff;"></span>
                        @endif
                    </button>
                </form>
                <button type="button" id="btnBulkPrintLabels" class="btn btn-secondary" style="display: none; align-items: center; gap: 8px;" onclick="printSelectedLabels()">
                    <i class="fa-solid fa-tags" style="color: var(--accent);"></i> Cetak Label (<span id="selectedCount">0</span>)
                </button>
                <button onclick="openAddModal()" class="btn btn-primary btn-desktop-add-product">
                    Tambah Produk
                </button>
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            <!-- Category Tabs Filter -->
            <div class="category-tabs">
                <span style="font-size: 13px; font-weight: 700; color: var(--text-secondary); margin-right: 8px;"><i class="fa-solid fa-filter"></i> Kategori:</span>
                <button type="button" class="cat-tab {{ !$categoryId ? 'active' : '' }}" data-id="" style="padding: 8px 16px; background-color: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 9999px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; color: var(--text-secondary); outline: none;">
                    Semua Kategori
                </button>
                @foreach($categories as $cat)
                    <button type="button" class="cat-tab {{ $categoryId == $cat->id ? 'active' : '' }}" data-id="{{ $cat->id }}" style="padding: 8px 16px; background-color: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 9999px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; color: var(--text-secondary); outline: none;">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
            <div class="table-responsive table-responsive-card table-responsive-products-desktop">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;"><input type="checkbox" id="selectAllProducts" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent);"></th>
                            <th style="width: 60px; text-align: center;">Foto</th>
                            <th>Barcode</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga Beli</th>
                            <th style="text-align: right;">Harga Jual</th>
                            <th>Stok</th>
                            <th style="width: 170px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td style="text-align: center;"><input type="checkbox" class="product-select-checkbox" value="{{ $product->id }}" onchange="updateSelectedCount()" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--accent);"></td>
                                <td data-label="Foto" style="text-align: center; vertical-align: middle;">
                                    @if($product->image && file_exists(public_path($product->image)))
                                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border-color);">
                                    @else
                                        <div style="width: 40px; height: 40px; border-radius: 4px; border: 1px dashed var(--border-color); background: var(--bg-secondary); display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                            <i class="fa-solid fa-box" style="font-size: 14px; color: var(--text-secondary); opacity: 0.5;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td data-label="Barcode">
                                    <span class="clickable-product-detail" onclick="openShowModal({{ json_encode($product) }})">
                                        <code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-weight: 600; color: var(--accent);">{{ $product->code }}</code>
                                    </span>
                                </td>
                                <td data-label="Nama Produk">
                                    <span class="clickable-product-detail" onclick="openShowModal({{ json_encode($product) }})">
                                        <strong>{{ $product->name }}</strong>
                                    </span>
                                </td>
                                <td data-label="Kategori">{{ $product->category->name }}</td>
                                <td data-label="Harga Beli">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                                <td data-label="Harga Jual" style="white-space: nowrap; text-align: right;"><strong>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</strong></td>
                                <td data-label="Stok">
                                    @if($product->stock <= 5)
                                        <span class="badge badge-danger" style="font-size: 13px;">{{ rtrim(rtrim(number_format($product->stock, 3, ',', '.'), '0'), ',') }} {{ $product->unit }} (Kritis)</span>
                                    @elseif($product->stock <= 15)
                                        <span class="badge badge-warning" style="font-size: 13px;">{{ rtrim(rtrim(number_format($product->stock, 3, ',', '.'), '0'), ',') }} {{ $product->unit }} (Menipis)</span>
                                    @else
                                        <span class="badge badge-success" style="font-size: 13px;">{{ rtrim(rtrim(number_format($product->stock, 3, ',', '.'), '0'), ',') }} {{ $product->unit }}</span>
                                    @endif
                                </td>
                                <td data-label="Aksi">
                                    <div style="display: flex; gap: 8px;">
                                        <button onclick="openShowModal({{ json_encode($product) }})" class="btn" style="padding: 6px 10px; background-color: rgba(79, 70, 229, 0.08); color: var(--accent); border: 1px solid rgba(79, 70, 229, 0.15); display: flex; align-items: center; justify-content: center;" title="Detail Produk">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </button>
                                        <a href="{{ route('products.print-labels', ['product_ids' => $product->id]) }}" class="btn btn-secondary" style="padding: 6px 10px; display: flex; align-items: center; justify-content: center;" title="Cetak Label Harga" target="_blank">
                                            <i class="fa-solid fa-barcode"></i>
                                        </a>
                                        <button onclick="openEditModal({{ json_encode($product) }})" class="btn btn-secondary" style="padding: 6px 10px;" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="delete-form" data-message="Apakah Anda yakin ingin menghapus produk '{{ $product->name }}' ini?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" style="padding: 6px 10px;" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; color: var(--text-secondary); padding: 32px;">
                                    Produk tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Product Cards Container (Matching User Screenshot) -->
            <div class="mobile-product-cards-container" style="padding: 12px;">
                @forelse($products as $product)
                    <div class="mp-card">
                        <!-- Top Section: Thumbnail & Title & Beli/Jual Prices -->
                        <div class="mp-card-header">
                            @if($product->image && file_exists(public_path($product->image)))
                                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="mp-card-thumb" onclick="openShowModal({{ json_encode($product) }})">
                            @else
                                <div class="mp-card-thumb-placeholder" onclick="openShowModal({{ json_encode($product) }})">
                                    <i class="fa-solid fa-box"></i>
                                </div>
                            @endif

                            <div class="mp-card-main-info">
                                <div class="mp-card-title" onclick="openShowModal({{ json_encode($product) }})">{{ $product->name }}</div>
                                <div class="mp-card-price-row">
                                    <span>Beli</span>
                                    <span class="price-val">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</span>
                                </div>
                                <div class="mp-card-price-row jual">
                                    <span>Jual</span>
                                    <span class="price-val">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mp-card-divider"></div>

                        <!-- 2-Column Key Value Metadata Grid -->
                        <div class="mp-card-meta-grid">
                            <div class="mp-meta-item">
                                <span class="mp-meta-label">Terdaftar</span>
                                <span class="mp-meta-value">{{ $product->created_at ? $product->created_at->format('d M Y') : '-' }}</span>
                            </div>
                            <div class="mp-meta-item">
                                <span class="mp-meta-label">Kategori</span>
                                <span class="mp-meta-value" style="color: var(--accent);">{{ $product->category->name }}</span>
                            </div>

                            <div class="mp-meta-item">
                                <span class="mp-meta-label">Stok</span>
                                <span class="mp-meta-value">
                                    @if($product->stock <= 0)
                                        <span style="color: var(--danger);">Habis</span>
                                    @else
                                        {{ rtrim(rtrim(number_format($product->stock, 3, ',', '.'), '0'), ',') }} {{ $product->unit }}
                                    @endif
                                </span>
                            </div>
                            <div class="mp-meta-item">
                                <span class="mp-meta-label">Barcode</span>
                                <span class="mp-meta-value"><code>{{ $product->code }}</code></span>
                            </div>

                            <div class="mp-meta-item">
                                <span class="mp-meta-label">Manajemen Stok</span>
                                <span class="mp-meta-value">Ya</span>
                            </div>
                            <div class="mp-meta-item">
                                <span class="mp-meta-label">Satuan</span>
                                <span class="mp-meta-value">{{ $product->unit ?? 'pcs' }}</span>
                            </div>
                        </div>

                        <!-- Action Buttons: Ubah (edit) & 3 Dots (More options) -->
                        <div class="mp-card-actions">
                            <button type="button" onclick="openEditModal({{ json_encode($product) }})" class="mp-btn-edit">
                                Ubah
                            </button>
                            <button type="button" onclick="showProductMobileMenu({{ json_encode($product) }})" class="mp-btn-more" title="Opsi Lainnya">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-secondary); padding: 40px 16px; background: #fff; border-radius: var(--radius-md); border: 1px dashed var(--border-color);">
                        <i class="fa-solid fa-box-open" style="font-size: 36px; margin-bottom: 12px; opacity: 0.5;"></i>
                        <p>Produk tidak ditemukan.</p>
                    </div>
                @endforelse
            </div>
            
            @if($products->hasPages())
                <div style="padding: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: center;">
                    {{ $products->appends(['search' => $search, 'category_id' => $categoryId])->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Mobile Fixed Bottom Bar (Red Action Button) -->
    <div class="mobile-fixed-bottom-bar">
        <button type="button" onclick="openAddModal()" class="btn-mobile-add-product">
            Tambah Produk
        </button>
    </div>

    <!-- Add Product Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Produk Baru</h3>
                <button onclick="closeAddModal()" class="modal-close">&times;</button>
            </div>
            <form id="addForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="add_method" name="_method" value="POST" disabled>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_code" class="form-label">Kode Produk / Barcode</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="text" id="add_code" name="code" class="form-control" required placeholder="Contoh: BRS5K" style="flex-grow: 1;">
                            <button type="button" id="clear_add_code" onclick="clearAddCodeInput()" class="btn btn-secondary" style="padding: 10px 14px; display: none; color: var(--danger); border-color: rgba(239, 68, 68, 0.2); background: rgba(239, 68, 68, 0.05);" title="Bersihkan">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                            <button type="button" onclick="startCameraScanner('add_code')" class="btn btn-secondary" style="padding: 10px 14px;" title="Scan menggunakan kamera HP">
                                <i class="fa-solid fa-camera"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add_name" class="form-label">Nama Produk</label>
                        <input type="text" id="add_name" name="name" class="form-control" required placeholder="Contoh: Beras Pandan Wangi 5kg">
                    </div>
                    <div class="form-group">
                        <label for="add_category_id" class="form-label">Kategori</label>
                        <select id="add_category_id" name="category_id" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="add_purchase_price" class="form-label">Harga Beli (Rp)</label>
                            <input type="text" inputmode="numeric" id="add_purchase_price" name="purchase_price" class="form-control" required placeholder="60.000">
                        </div>
                        <div class="form-group">
                            <label for="add_selling_price" class="form-label">Harga Jual (Rp)</label>
                            <input type="text" inputmode="numeric" id="add_selling_price" name="selling_price" class="form-control" required placeholder="68.000">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="add_stock" class="form-label">Jumlah Stok</label>
                            <input type="text" inputmode="numeric" id="add_stock" name="stock" class="form-control" placeholder="Contoh: 50" value="">
                        </div>
                        <div class="form-group">
                            <label for="add_unit" class="form-label">Satuan</label>
                            <input type="text" id="add_unit" name="unit" class="form-control" required placeholder="Contoh: pcs" value="pcs">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add_image" class="form-label">Foto Produk (Opsional)</label>
                        <div id="add_image_preview_container" style="margin-bottom: 12px; display: none;">
                            <p style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">Foto Saat Ini:</p>
                            <img id="add_image_preview" src="" alt="Preview" style="max-width: 100px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border-color);">
                        </div>
                        <input type="file" id="add_image" name="image" class="form-control" accept="image/*" capture="environment">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeAddModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit Produk</h3>
                <button onclick="closeEditModal()" class="modal-close">&times;</button>
            </div>
            <form id="editForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_code" class="form-label">Kode Produk / Barcode</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="text" id="edit_code" name="code" class="form-control" required style="flex-grow: 1;">
                            <button type="button" onclick="startCameraScanner('edit_code')" class="btn btn-secondary" style="padding: 10px 14px;" title="Scan menggunakan kamera HP">
                                <i class="fa-solid fa-camera"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_name" class="form-label">Nama Produk</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_category_id" class="form-label">Kategori</label>
                        <select id="edit_category_id" name="category_id" class="form-control" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="edit_purchase_price" class="form-label">Harga Beli (Rp)</label>
                            <input type="text" inputmode="numeric" id="edit_purchase_price" name="purchase_price" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_selling_price" class="form-label">Harga Jual (Rp)</label>
                            <input type="text" inputmode="numeric" id="edit_selling_price" name="selling_price" class="form-control" required>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="edit_stock" class="form-label">Jumlah Stok</label>
                            <input type="text" inputmode="numeric" id="edit_stock" name="stock" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_unit" class="form-label">Satuan</label>
                            <input type="text" id="edit_unit" name="unit" class="form-control" required placeholder="Contoh: pcs">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_image" class="form-label">Ganti Foto Produk (Opsional)</label>
                        <div id="edit_image_preview_container" style="margin-bottom: 12px; display: none;">
                            <p style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">Foto Saat Ini:</p>
                            <img id="edit_image_preview" src="" alt="Preview" style="max-width: 100px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border-color);">
                        </div>
                        <input type="file" id="edit_image" name="image" class="form-control" accept="image/*" capture="environment">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Show Product Modal -->
    <div id="showModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fa-solid fa-circle-info" style="color: var(--accent);"></i> Detail Produk</h3>
                <button onclick="closeShowModal()" class="modal-close">&times;</button>
            </div>
            <div class="modal-body" style="padding-top: 16px;">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 16px; margin-bottom: 24px;">
                    <div style="width: 150px; height: 150px; border-radius: var(--radius-md); border: 1px solid var(--border-color); overflow: hidden; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); cursor: pointer;" title="Klik untuk memperbesar gambar">
                        <img id="show_image" src="" alt="Foto Produk" style="width: 100%; height: 100%; object-fit: cover; display: none;" onclick="openImagePreviewModal(this.src)">
                        <i id="show_image_placeholder" class="fa-solid fa-box" style="font-size: 48px; color: var(--text-secondary); opacity: 0.5;"></i>
                    </div>
                    <div style="text-align: center;">
                        <h4 id="show_name" style="font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;"></h4>
                        <code id="show_code" style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 13px;"></code>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px; border-top: 1px solid var(--border-color); padding-top: 16px;">
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span style="color: var(--text-secondary);">Kategori</span>
                        <strong id="show_category" style="color: var(--text-primary);"></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span style="color: var(--text-secondary);">Satuan</span>
                        <strong id="show_unit" style="color: var(--text-primary);"></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span style="color: var(--text-secondary);">Stok Tersedia</span>
                        <strong id="show_stock" style="color: var(--text-primary);"></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; border-top: 1px dashed var(--border-color); padding-top: 12px;">
                        <span style="color: var(--text-secondary);">Harga Beli</span>
                        <strong id="show_purchase_price" style="color: var(--text-primary);"></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span style="color: var(--text-secondary);">Harga Jual</span>
                        <strong id="show_selling_price" style="color: var(--accent); font-weight: 700;"></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; background: rgba(34, 197, 94, 0.08); padding: 8px 12px; border-radius: 6px;">
                        <span style="color: var(--success); font-weight: 600;">Keuntungan (Profit)</span>
                        <strong id="show_profit" style="color: var(--success); font-weight: 700;"></strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding-top: 16px; display: flex; flex-direction: column; gap: 8px;">
                <div style="display: flex; gap: 8px; width: 100%;">
                    <button onclick="editProductFromDetail()" class="btn btn-primary" style="flex-grow: 1; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 16px;">
                        <i class="fa-solid fa-pen-to-square"></i> Edit
                    </button>
                    <form id="show_delete_form" method="POST" class="delete-form" style="margin: 0; flex-grow: 1;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 16px;">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
                <button onclick="closeShowModal()" class="btn btn-secondary" style="width: 100%; padding: 10px 16px;">Tutup</button>
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

    <!-- Image Preview Modal (Lightbox) -->
    <div id="imagePreviewModal" class="modal" onclick="closeImagePreviewModal()" style="background: rgba(0, 0, 0, 0.85); z-index: 1100;">
        <div style="position: absolute; top: 20px; right: 20px; color: #fff; font-size: 30px; cursor: pointer; font-weight: bold;">&times;</div>
        <div style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; padding: 20px;">
            <img id="lightbox_image" src="" alt="Full View" style="max-width: 90%; max-height: 90%; object-fit: contain; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); cursor: zoom-out;">
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode/html5-qrcode.min.js"></script>
    <script>
        let html5QrcodeScanner = null;
        let activeTargetInputId = null;
        let addModalCurrentStock = 0;

        function formatFloatToIndonesian(val) {
            if (val === undefined || val === null || val === '') return '';
            let num = parseFloat(val);
            if (isNaN(num)) return '';
            let str = String(num);
            let parts = str.split('.');
            let integerPart = parseInt(parts[0]).toLocaleString('id-ID');
            if (parts.length > 1) {
                return integerPart + ',' + parts[1];
            }
            return integerPart;
        }

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

        function startCameraScanner(targetInputId) {
            activeTargetInputId = targetInputId;
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
            
            if (activeTargetInputId) {
                const inputEl = document.getElementById(activeTargetInputId);
                inputEl.value = decodedText.trim();
                
                // Dispatch change event programmatically so event listeners run
                const event = new Event('change', { bubbles: true });
                inputEl.dispatchEvent(event);

                if (activeTargetInputId === 'productSearch') {
                    const searchForm = document.getElementById('searchForm');
                    if (searchForm) searchForm.submit();
                }
            }
            
            closeCameraScanner();
            
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: `Barcode terbaca: ${decodedText}`,
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true
            });
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

        function checkProductCode(code) {
            code = code.trim();
            if (!code) {
                resetAddFormToStore();
                return;
            }

            fetch(`/products/search-by-code/${encodeURIComponent(code)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const product = data.product;
                        // Autofill form
                        document.getElementById('add_name').value = product.name;
                        document.getElementById('add_category_id').value = product.category_id;
                        document.getElementById('add_purchase_price').value = Math.round(product.purchase_price).toLocaleString('id-ID');
                        document.getElementById('add_selling_price').value = Math.round(product.selling_price).toLocaleString('id-ID');
                        let stockVal = parseFloat(product.stock);
                        addModalCurrentStock = isNaN(stockVal) ? 0 : stockVal;
                        document.getElementById('add_stock').placeholder = 'Stok saat ini: ' + formatFloatToIndonesian(product.stock);
                        document.getElementById('add_stock').value = '';
                        document.getElementById('add_unit').value = product.unit || 'pcs';

                         const addPreviewContainer = document.getElementById('add_image_preview_container');
                         const addPreviewImg = document.getElementById('add_image_preview');
                         if (product.image) {
                             addPreviewImg.src = `/${product.image}`;
                             addPreviewContainer.style.display = 'block';
                         } else {
                             addPreviewContainer.style.display = 'none';
                         }

                        // Transform form to Update
                        const form = document.getElementById('addForm');
                        form.action = `/products/${product.id}`;
                        
                        const methodInput = document.getElementById('add_method');
                        methodInput.value = 'PUT';
                        methodInput.disabled = false;

                        // Update UI header/buttons
                        document.querySelector('#addModal .modal-title').innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Edit Produk (Sudah Tersimpan)';
                        document.querySelector('#addModal button[type="submit"]').textContent = 'Simpan Perubahan';
                        
                        // Show info badge if not exists
                        let infoAlert = document.getElementById('add_code_alert');
                        if (!infoAlert) {
                            infoAlert = document.createElement('div');
                            infoAlert.id = 'add_code_alert';
                            infoAlert.className = 'alert alert-info';
                            infoAlert.style.marginTop = '8px';
                            infoAlert.style.padding = '8px 12px';
                            infoAlert.style.fontSize = '13px';
                            infoAlert.style.borderRadius = 'var(--radius-sm)';
                            infoAlert.style.background = 'rgba(79, 70, 229, 0.08)';
                            infoAlert.style.color = 'var(--accent)';
                            infoAlert.style.border = '1px solid rgba(79, 70, 229, 0.15)';
                            infoAlert.innerHTML = '<i class="fa-solid fa-circle-info"></i> Produk dengan barcode ini sudah terdaftar. Mengisi data otomatis untuk diedit.';
                            document.getElementById('add_code').parentNode.parentNode.appendChild(infoAlert);
                        }
                    } else {
                        resetAddFormToStore();
                        fetchExternalProductInfo(code);
                    }
                })
                .catch(err => {
                    console.error('Error checking product code:', err);
                });
        }

        function fetchExternalProductInfo(code) {
            const nameInput = document.getElementById('add_name');
            if (!nameInput) return;

            nameInput.placeholder = "Mencari produk di database internet...";
            nameInput.disabled = true;

            // Step 1: Try specialized Indonesian Product API (ariph007 database with 50K+ products)
            fetch(`https://api-products.alpha-projects.cloud/api/v1/products-barcode?barcode=${encodeURIComponent(code)}&generateBarcode=false`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.data) {
                        const product = data.data;
                        const externalProduct = {
                            product_name: product.name,
                            image_url: product.image
                        };
                        populateExternalProductData(externalProduct);
                    } else {
                        // Fallback to Open Food Facts Indonesian endpoint
                        fallbackToOpenFoodFacts(code);
                    }
                })
                .catch(err => {
                    // Fallback to Open Food Facts on error
                    fallbackToOpenFoodFacts(code);
                });
        }

        function fallbackToOpenFoodFacts(code) {
            // Step 2: Try Indonesian Food Facts
            fetch(`https://id.openfoodfacts.org/api/v2/product/${encodeURIComponent(code)}.json`)
                .then(response => response.json())
                .then(data => {
                    if ((data.status === 1 || data.status === "1") && data.product) {
                        populateExternalProductData(data.product);
                    } else {
                        // Step 3: Fallback to World Food Facts
                        fetch(`https://world.openfoodfacts.org/api/v2/product/${encodeURIComponent(code)}.json`)
                            .then(response => response.json())
                            .then(worldData => {
                                if ((worldData.status === 1 || worldData.status === "1") && worldData.product) {
                                    populateExternalProductData(worldData.product);
                                } else {
                                    // Step 4: Fallback to Open Products Facts (Medicines, Cosmetics, Household items)
                                    fetch(`https://world.openproductsfacts.org/api/v2/product/${encodeURIComponent(code)}.json`)
                                        .then(response => response.json())
                                        .then(prodData => {
                                            if ((prodData.status === 1 || prodData.status === "1") && prodData.product) {
                                                populateExternalProductData(prodData.product);
                                            } else {
                                                showExternalProductNotFound();
                                            }
                                        })
                                        .catch(err => {
                                            showExternalProductNotFound();
                                        });
                                }
                            })
                            .catch(err => {
                                showExternalProductNotFound();
                            });
                    }
                })
                .catch(err => {
                    // Try world food facts directly on initial error
                    fetch(`https://world.openfoodfacts.org/api/v2/product/${encodeURIComponent(code)}.json`)
                        .then(response => response.json())
                        .then(worldData => {
                            if ((worldData.status === 1 || worldData.status === "1") && worldData.product) {
                                populateExternalProductData(worldData.product);
                            } else {
                                // Fallback to Open Products Facts on world food facts error/not found
                                fetch(`https://world.openproductsfacts.org/api/v2/product/${encodeURIComponent(code)}.json`)
                                    .then(response => response.json())
                                    .then(prodData => {
                                        if ((prodData.status === 1 || prodData.status === "1") && prodData.product) {
                                            populateExternalProductData(prodData.product);
                                        } else {
                                            showExternalProductNotFound();
                                        }
                                    })
                                    .catch(prodErr => {
                                        showExternalProductNotFound();
                                    });
                            }
                        })
                        .catch(worldErr => {
                            showExternalProductNotFound();
                        });
                });
        }

        function populateExternalProductData(product) {
            const nameInput = document.getElementById('add_name');
            if (nameInput) {
                nameInput.disabled = false;
                nameInput.placeholder = "Contoh: Beras Pandan Wangi 5Kg";

                const brand = product.brands ? product.brands.trim() : '';
                const pName = product.product_name || product.product_name_id || product.product_name_en || product.generic_name || '';
                let fullName = pName;
                if (brand && pName && !pName.toLowerCase().includes(brand.toLowerCase())) {
                    fullName = brand + ' ' + pName;
                } else if (!fullName && brand) {
                    fullName = brand;
                }

                if (fullName) {
                    nameInput.value = fullName;
                }
            }

            // Autofill UOM/Unit if available
            if (product.uom) {
                const addUnitInput = document.getElementById('add_unit');
                if (addUnitInput) {
                    addUnitInput.value = product.uom.toLowerCase();
                }
            }

            const imageUrl = product.image_front_url || product.image_url || product.image_small_url || product.image_front_small_url;
            if (imageUrl) {
                const addPreviewContainer = document.getElementById('add_image_preview_container');
                const addPreviewImg = document.getElementById('add_image_preview');
                if (addPreviewContainer && addPreviewImg) {
                    addPreviewImg.src = imageUrl;
                    addPreviewContainer.style.display = 'block';

                    const previewTitle = addPreviewContainer.querySelector('p');
                    if (previewTitle) {
                        previewTitle.textContent = "Foto Produk (Internet):";
                    }
                }

                let hiddenImgInput = document.getElementById('add_fetched_image_url');
                if (!hiddenImgInput) {
                    hiddenImgInput = document.createElement('input');
                    hiddenImgInput.type = 'hidden';
                    hiddenImgInput.id = 'add_fetched_image_url';
                    hiddenImgInput.name = 'fetched_image_url';
                    document.getElementById('addForm').appendChild(hiddenImgInput);
                }
                hiddenImgInput.value = imageUrl;
            }

            let infoAlert = document.getElementById('add_code_alert');
            if (infoAlert) infoAlert.remove();

            infoAlert = document.createElement('div');
            infoAlert.id = 'add_code_alert';
            infoAlert.className = 'alert alert-info';
            infoAlert.style.marginTop = '8px';
            infoAlert.style.padding = '8px 12px';
            infoAlert.style.fontSize = '13px';
            infoAlert.style.borderRadius = 'var(--radius-sm)';
            infoAlert.style.background = 'rgba(34, 197, 94, 0.08)';
            infoAlert.style.color = 'var(--success)';
            infoAlert.style.border = '1px solid rgba(34, 197, 94, 0.15)';
            infoAlert.innerHTML = '<i class="fa-solid fa-circle-check"></i> Produk ditemukan di database internet! Mengisi nama & foto otomatis.';
            document.getElementById('add_code').parentNode.parentNode.appendChild(infoAlert);
        }

        function showExternalProductNotFound() {
            const nameInput = document.getElementById('add_name');
            if (nameInput) {
                nameInput.disabled = false;
                nameInput.placeholder = "Contoh: Beras Pandan Wangi 5Kg";
            }

            let infoAlert = document.getElementById('add_code_alert');
            if (infoAlert) infoAlert.remove();

            infoAlert = document.createElement('div');
            infoAlert.id = 'add_code_alert';
            infoAlert.className = 'alert alert-warning';
            infoAlert.style.marginTop = '8px';
            infoAlert.style.padding = '8px 12px';
            infoAlert.style.fontSize = '13px';
            infoAlert.style.borderRadius = 'var(--radius-sm)';
            infoAlert.style.background = 'rgba(239, 68, 68, 0.08)';
            infoAlert.style.color = 'var(--danger)';
            infoAlert.style.border = '1px solid rgba(239, 68, 68, 0.15)';
            infoAlert.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Barcode tidak terdaftar di database lokal maupun internet. Silakan ketik nama manual.';
            document.getElementById('add_code').parentNode.parentNode.appendChild(infoAlert);
        }

        function resetAddFormToStore() {
            // Revert form to Store (POST)
            const form = document.getElementById('addForm');
            if (form) {
                form.action = "{{ route('products.store') }}";
            }
            
            const methodInput = document.getElementById('add_method');
            if (methodInput) {
                methodInput.value = 'POST';
                methodInput.disabled = true;
            }

            const addUnitInput = document.getElementById('add_unit');
            if (addUnitInput) {
                addUnitInput.value = 'pcs';
            }

            addModalCurrentStock = 0;
            const addStockInput = document.getElementById('add_stock');
            if (addStockInput) {
                addStockInput.placeholder = 'Contoh: 50';
                addStockInput.value = '';
            }

            const title = document.querySelector('#addModal .modal-title');
            if (title) {
                title.innerHTML = 'Tambah Produk Baru';
            }

            const submitBtn = document.querySelector('#addModal button[type="submit"]');
            if (submitBtn) {
                submitBtn.textContent = 'Simpan';
            }

            const infoAlert = document.getElementById('add_code_alert');
            if (infoAlert) {
                infoAlert.remove();
            }

             const addImageInput = document.getElementById('add_image');
             if (addImageInput) {
                 addImageInput.value = '';
             }

             const addPreviewContainer = document.getElementById('add_image_preview_container');
             if (addPreviewContainer) {
                 addPreviewContainer.style.display = 'none';
                 const previewTitle = addPreviewContainer.querySelector('p');
                 if (previewTitle) {
                     previewTitle.textContent = "Foto Produk Preview:";
                 }
             }
             const addPreviewImg = document.getElementById('add_image_preview');
             if (addPreviewImg) {
                 addPreviewImg.src = '';
             }
             const hiddenImgInput = document.getElementById('add_fetched_image_url');
             if (hiddenImgInput) {
                 hiddenImgInput.value = '';
             }
             const clearAddCodeBtn = document.getElementById('clear_add_code');
             const addCodeEl = document.getElementById('add_code');
             if (clearAddCodeBtn) {
                 clearAddCodeBtn.style.display = (addCodeEl && addCodeEl.value) ? 'inline-block' : 'none';
             }
        }


        function openAddModal() {
            resetAddFormToStore();
            const form = document.getElementById('addForm');
            if (form) {
                form.reset();
            }
            document.getElementById('addModal').classList.add('active');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.remove('active');
            resetAddFormToStore();
            const form = document.getElementById('addForm');
            if (form) {
                form.reset();
            }
        }

        function openEditModal(product) {
            const form = document.getElementById('editForm');
            form.action = `/products/${product.id}`;
            document.getElementById('edit_code').value = product.code;
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_category_id').value = product.category_id;
            document.getElementById('edit_purchase_price').value = Math.round(product.purchase_price).toLocaleString('id-ID');
            document.getElementById('edit_selling_price').value = Math.round(product.selling_price).toLocaleString('id-ID');
            document.getElementById('edit_stock').value = formatFloatToIndonesian(product.stock);
            document.getElementById('edit_unit').value = product.unit || 'pcs';

             const previewContainer = document.getElementById('edit_image_preview_container');
             const previewImg = document.getElementById('edit_image_preview');
             if (product.image) {
                 previewImg.src = `/${product.image}`;
                 previewContainer.style.display = 'block';
             } else {
                 previewContainer.style.display = 'none';
             }

             const editImageInput = document.getElementById('edit_image');
             if (editImageInput) {
                 editImageInput.value = '';
             }
             document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        let currentDetailProduct = null;

        function openShowModal(product) {
            currentDetailProduct = product;
            document.getElementById('show_name').textContent = product.name;
            document.getElementById('show_code').textContent = product.code;
            
            const categoryName = product.category ? product.category.name : 'N/A';
            document.getElementById('show_category').textContent = categoryName;
            
            document.getElementById('show_unit').textContent = product.unit || 'pcs';
            document.getElementById('show_stock').textContent = formatFloatToIndonesian(product.stock) + ' ' + (product.unit || 'pcs');
            
            const purchase = Math.round(product.purchase_price);
            const selling = Math.round(product.selling_price);
            const profit = selling - purchase;
            
            document.getElementById('show_purchase_price').textContent = 'Rp ' + purchase.toLocaleString('id-ID');
            document.getElementById('show_selling_price').textContent = 'Rp ' + selling.toLocaleString('id-ID');
            document.getElementById('show_profit').textContent = 'Rp ' + profit.toLocaleString('id-ID');
            
            const imgEl = document.getElementById('show_image');
            const placeholderEl = document.getElementById('show_image_placeholder');
            if (product.image) {
                imgEl.src = `/${product.image}`;
                imgEl.style.display = 'block';
                placeholderEl.style.display = 'none';
            } else {
                imgEl.style.display = 'none';
                placeholderEl.style.display = 'block';
            }
            
            // Set dynamic delete form action and message
            const showDeleteForm = document.getElementById('show_delete_form');
            if (showDeleteForm) {
                showDeleteForm.action = `/products/${product.id}`;
                showDeleteForm.dataset.message = `Apakah Anda yakin ingin menghapus produk '${product.name}' ini?`;
            }
            
            document.getElementById('showModal').classList.add('active');
        }

        function closeShowModal() {
            document.getElementById('showModal').classList.remove('active');
            currentDetailProduct = null;
        }

        function editProductFromDetail() {
            if (!currentDetailProduct) return;
            const product = currentDetailProduct;
            closeShowModal();
            openEditModal(product);
        }

        function openImagePreviewModal(src) {
            document.getElementById('lightbox_image').src = src;
            document.getElementById('imagePreviewModal').classList.add('active');
        }

        function closeImagePreviewModal() {
            document.getElementById('imagePreviewModal').classList.remove('active');
        }

        // Add event listeners on load
        document.addEventListener('DOMContentLoaded', function() {
            const addCodeInput = document.getElementById('add_code');
            if (addCodeInput) {
                addCodeInput.addEventListener('change', function() {
                    checkProductCode(this.value);
                });
                addCodeInput.addEventListener('keyup', function(e) {
                    if (e.key === 'Enter') {
                        checkProductCode(this.value);
                    }
                });
                addCodeInput.addEventListener('blur', function() {
                    checkProductCode(this.value);
                });
            }

            // Category Tab filter listeners
            document.querySelectorAll('.cat-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const categoryId = this.dataset.id;
                    const selectEl = document.getElementById('filter_category_select');
                    if (selectEl) {
                        selectEl.value = categoryId;
                    }
                    document.getElementById('searchForm').submit();
                });
            });

            // Auto-submit on mobile dropdown change
            const selectEl = document.getElementById('filter_category_select');
            if (selectEl) {
                selectEl.addEventListener('change', function() {
                    document.getElementById('searchForm').submit();
                });
            }

            // Clear search text listener
            const clearSearchBtn = document.getElementById('clearSearch');
            const productSearchInput = document.getElementById('productSearch');
            if (clearSearchBtn && productSearchInput) {
                clearSearchBtn.addEventListener('click', function() {
                    productSearchInput.value = '';
                    this.style.display = 'none';
                    document.getElementById('searchForm').submit();
                });
                productSearchInput.addEventListener('input', function() {
                    clearSearchBtn.style.display = this.value.trim().length > 0 ? 'flex' : 'none';
                });
            }

            // Clear add barcode input listener
            const addCodeEl = document.getElementById('add_code');
            const clearAddCodeBtn = document.getElementById('clear_add_code');
            if (addCodeEl && clearAddCodeBtn) {
                addCodeEl.addEventListener('input', function() {
                    clearAddCodeBtn.style.display = this.value ? 'inline-block' : 'none';
                });
                addCodeEl.addEventListener('change', function() {
                    clearAddCodeBtn.style.display = this.value ? 'inline-block' : 'none';
                });
            }

            // Live number formatting helper for Indonesian float (thousands: ., decimals: ,)
            const formatIndonesianNumber = function(value) {
                let clean = value.replace(/[^0-9.,]/g, '');
                let lastDot = clean.lastIndexOf('.');
                let lastComma = clean.lastIndexOf(',');
                let decimalSep = null;
                let decimalIdx = -1;
                
                if (lastComma > -1 && lastComma > lastDot) {
                    decimalSep = ',';
                    decimalIdx = lastComma;
                } else if (lastDot > -1 && lastDot > lastComma) {
                    let parts = clean.split('.');
                    if (parts.length === 2 && parts[1].length !== 3) {
                        decimalSep = '.';
                        decimalIdx = lastDot;
                    }
                }
                
                let integerPart = '';
                let decimalPart = '';
                
                if (decimalIdx > -1) {
                    integerPart = clean.slice(0, decimalIdx).replace(/[^0-9]/g, '');
                    decimalPart = clean.slice(decimalIdx + 1).replace(/[^0-9]/g, '').slice(0, 3);
                } else {
                    integerPart = clean.replace(/[^0-9]/g, '');
                }
                
                if (integerPart !== '') {
                    integerPart = parseInt(integerPart).toLocaleString('id-ID');
                }
                
                if (decimalIdx > -1) {
                    return integerPart + ',' + decimalPart;
                }
                return integerPart;
            };

            // Number formatting for purchase and selling price inputs
            const priceInputs = [
                document.getElementById('add_purchase_price'),
                document.getElementById('add_selling_price'),
                document.getElementById('edit_purchase_price'),
                document.getElementById('edit_selling_price')
            ];

            priceInputs.forEach(input => {
                if (input) {
                    input.addEventListener('input', function() {
                        let cleanVal = this.value.replace(/[^0-9]/g, '');
                        if (cleanVal !== '') {
                            this.value = parseInt(cleanVal).toLocaleString('id-ID');
                        } else {
                            this.value = '';
                        }
                    });
                }
            });

            // Live formatting for stock inputs (supporting decimal)
            const stockInputs = [
                document.getElementById('add_stock'),
                document.getElementById('edit_stock')
            ];

            stockInputs.forEach(input => {
                if (input) {
                    input.addEventListener('input', function() {
                        this.value = formatIndonesianNumber(this.value);
                    });
                }
            });

            // Strip dot separators before submitting forms
            const stripFormatting = function(form) {
                form.querySelectorAll('input[name="purchase_price"], input[name="selling_price"]').forEach(input => {
                    input.value = input.value.replace(/[^0-9]/g, '');
                });

                // For addForm: calculate final stock (currentStock + typedStock)
                if (form.id === 'addForm') {
                    const addStockInput = document.getElementById('add_stock');
                    if (addStockInput) {
                        let typedVal = addStockInput.value.trim();
                        let parsedTyped = 0;
                        if (typedVal !== '') {
                            parsedTyped = parseFloat(typedVal.replace(/\./g, '').replace(/,/g, '.'));
                            if (isNaN(parsedTyped)) parsedTyped = 0;
                        }
                        let finalVal = addModalCurrentStock + parsedTyped;
                        addStockInput.value = formatFloatToIndonesian(finalVal);
                    }
                }

                form.querySelectorAll('input[name="stock"]').forEach(input => {
                    let val = input.value.trim();
                    if (val !== '') {
                        // Remove all dots (thousands separator) and convert comma to dot (decimal separator)
                        input.value = val.replace(/\./g, '').replace(/,/g, '.');
                    }
                });
            };

            const addForm = document.getElementById('addForm');
            if (addForm) {
                addForm.addEventListener('submit', function() {
                    stripFormatting(this);
                });
            }

            const editForm = document.getElementById('editForm');
            if (editForm) {
                editForm.addEventListener('submit', function() {
                    stripFormatting(this);
                });
            }
        });

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
                closeCameraScanner();
            }
        }

        // Logic for Bulk Price Label Printing
        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('.product-select-checkbox:checked');
            const count = checkboxes.length;
            const bulkBtn = document.getElementById('btnBulkPrintLabels');
            const selectedCountSpan = document.getElementById('selectedCount');

            if (selectedCountSpan) {
                selectedCountSpan.textContent = count;
            }

            if (bulkBtn) {
                if (count > 0) {
                    bulkBtn.style.display = 'inline-flex';
                } else {
                    bulkBtn.style.display = 'none';
                }
            }
        }

        // Toggle Select All
        const selectAll = document.getElementById('selectAllProducts');
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.product-select-checkbox');
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                updateSelectedCount();
            });
        }

        function printSelectedLabels() {
            const checkedBoxes = document.querySelectorAll('.product-select-checkbox:checked');
            const ids = Array.from(checkedBoxes).map(cb => cb.value);
            
            if (ids.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Produk',
                    text: 'Silakan pilih produk terlebih dahulu.',
                    confirmButtonColor: '#4f46e5'
                });
                return;
            }
            
            const url = "{{ route('products.print-labels') }}?product_ids=" + ids.join(',');
            window.open(url, '_blank');
        }

        function clearAddCodeInput() {
            const addCodeEl = document.getElementById('add_code');
            if (addCodeEl) {
                addCodeEl.value = '';
                const event = new Event('change', { bubbles: true });
                addCodeEl.dispatchEvent(event);
                addCodeEl.focus();
            }
            resetAddFormToStore();
            const infoAlert = document.getElementById('add_code_alert');
            if (infoAlert) infoAlert.remove();
        }

        function showProductMobileMenu(product) {
            Swal.fire({
                title: product.name,
                text: `Kode: ${product.code} | Stok: ${product.stock} ${product.unit || 'pcs'}`,
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: 'Tutup',
                cancelButtonColor: '#64748b',
                html: `
                    <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 16px;">
                        <button type="button" onclick="Swal.close(); openShowModal(${JSON.stringify(product).replace(/"/g, '&quot;')});" class="btn btn-secondary" style="width: 100%; padding: 12px; justify-content: flex-start; gap: 10px; font-weight: 600;">
                            <i class="fa-solid fa-circle-info" style="color: var(--accent);"></i> Detail Produk Lengkap
                        </button>
                        <a href="/products/print-labels?product_ids=${product.id}" target="_blank" onclick="Swal.close();" class="btn btn-secondary" style="width: 100%; padding: 12px; justify-content: flex-start; gap: 10px; text-decoration: none; color: var(--text-primary); font-weight: 600;">
                            <i class="fa-solid fa-barcode" style="color: var(--accent);"></i> Cetak Label Barcode
                        </a>
                        <button type="button" onclick="Swal.close(); confirmDeleteProduct(${product.id}, '${product.name.replace(/'/g, "\\'")}');" class="btn btn-danger" style="width: 100%; padding: 12px; justify-content: flex-start; gap: 10px; font-weight: 600;">
                            <i class="fa-solid fa-trash"></i> Hapus Produk
                        </button>
                    </div>
                `
            });
        }

        function confirmDeleteProduct(productId, productName) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus produk '${productName}' ini?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/products/${productId}`;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';
                    form.innerHTML = `
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="_method" value="DELETE">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function openCategoryFilterDialog() {
            const categories = @json($categories);
            const currentCatId = "{{ $categoryId }}";
            
            let optionsHtml = `
                <div style="display: flex; flex-direction: column; gap: 8px; max-height: 320px; overflow-y: auto; text-align: left; padding: 4px;">
                    <button type="button" onclick="selectCategoryFilter('')" class="btn ${!currentCatId ? 'btn-primary' : 'btn-secondary'}" style="width: 100%; justify-content: space-between; padding: 12px 16px; font-weight: 600;">
                        <span>Semua Kategori</span>
                        ${!currentCatId ? '<i class="fa-solid fa-check"></i>' : ''}
                    </button>
            `;
            
            categories.forEach(cat => {
                const isSelected = String(cat.id) === String(currentCatId);
                optionsHtml += `
                    <button type="button" onclick="selectCategoryFilter('${cat.id}')" class="btn ${isSelected ? 'btn-primary' : 'btn-secondary'}" style="width: 100%; justify-content: space-between; padding: 12px 16px; font-weight: 600;">
                        <span>${cat.name}</span>
                        ${isSelected ? '<i class="fa-solid fa-check"></i>' : ''}
                    </button>
                `;
            });
            
            optionsHtml += `</div>`;

            Swal.fire({
                title: 'Pilih Kategori',
                html: optionsHtml,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Tutup',
                cancelButtonColor: '#64748b'
            });
        }

        function selectCategoryFilter(catId) {
            Swal.close();
            const input = document.getElementById('filter_category_input');
            if (input) {
                input.value = catId;
            }
            document.getElementById('searchForm').submit();
        }

        // Connect category tabs pill buttons
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.cat-tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    const catId = this.getAttribute('data-id');
                    selectCategoryFilter(catId);
                });
            });
        });
    </script>
@endsection
