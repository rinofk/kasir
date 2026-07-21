@extends('layouts.app')

@section('title', 'Produk')
@section('header_title', 'Kelola Produk & Inventoris')

@section('content')
    <div class="card">
        <div class="card-header">
            <div style="display: flex; gap: 16px; align-items: center; width: 100%; justify-content: space-between; flex-wrap: wrap;">
                <form action="{{ route('products.index') }}" method="GET" style="display: flex; gap: 8px; flex-grow: 1; max-width: 600px; flex-wrap: wrap;">
                    <input type="text" name="search" class="form-control" placeholder="Cari barcode, kode, nama..." value="{{ $search }}" style="width: 220px;">
                    <select name="category_id" class="form-control" style="width: 180px;">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
                </form>
                <button onclick="openAddModal()" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah Produk
                </button>
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode/Barcode</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga Beli</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td><code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-weight: 600;">{{ $product->code }}</code></td>
                                <td><strong>{{ $product->name }}</strong></td>
                                <td>{{ $product->category->name }}</td>
                                <td>Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                                <td><strong>Rp {{ number_format($product->selling_price, 0, ',', '.') }}</strong></td>
                                <td>
                                    @if($product->stock <= 5)
                                        <span class="badge badge-danger" style="font-size: 13px;">{{ $product->stock }} (Kritis)</span>
                                    @elseif($product->stock <= 15)
                                        <span class="badge badge-warning" style="font-size: 13px;">{{ $product->stock }} (Menipis)</span>
                                    @else
                                        <span class="badge badge-success" style="font-size: 13px;">{{ $product->stock }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
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
                                <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 32px;">
                                    Produk tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($products->hasPages())
                <div style="padding: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: center;">
                    {{ $products->appends(['search' => $search, 'category_id' => $categoryId])->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Produk Baru</h3>
                <button onclick="closeAddModal()" class="modal-close">&times;</button>
            </div>
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_code" class="form-label">Kode Produk / Barcode</label>
                        <input type="text" id="add_code" name="code" class="form-control" required placeholder="Contoh: BRS5K">
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
                            <input type="number" id="add_purchase_price" name="purchase_price" class="form-control" required min="0" placeholder="60000">
                        </div>
                        <div class="form-group">
                            <label for="add_selling_price" class="form-label">Harga Jual (Rp)</label>
                            <input type="number" id="add_selling_price" name="selling_price" class="form-control" required min="0" placeholder="68000">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add_stock" class="form-label">Jumlah Stok Awal</label>
                        <input type="number" id="add_stock" name="stock" class="form-control" required min="0" placeholder="50">
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
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_code" class="form-label">Kode Produk / Barcode</label>
                        <input type="text" id="edit_code" name="code" class="form-control" required>
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
                            <input type="number" id="edit_purchase_price" name="purchase_price" class="form-control" required min="0">
                        </div>
                        <div class="form-group">
                            <label for="edit_selling_price" class="form-label">Harga Jual (Rp)</label>
                            <input type="number" id="edit_selling_price" name="selling_price" class="form-control" required min="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_stock" class="form-label">Stok</label>
                        <input type="number" id="edit_stock" name="stock" class="form-control" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.add('active');
        }
        function closeAddModal() {
            document.getElementById('addModal').classList.remove('active');
        }
        function openEditModal(product) {
            const form = document.getElementById('editForm');
            form.action = `/products/${product.id}`;
            document.getElementById('edit_code').value = product.code;
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_category_id').value = product.category_id;
            document.getElementById('edit_purchase_price').value = Math.round(product.purchase_price);
            document.getElementById('edit_selling_price').value = Math.round(product.selling_price);
            document.getElementById('edit_stock').value = product.stock;
            document.getElementById('editModal').classList.add('active');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
@endsection
