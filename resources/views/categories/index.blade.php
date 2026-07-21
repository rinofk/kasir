@extends('layouts.app')

@section('title', 'Kategori Produk')
@section('header_title', 'Kelola Kategori Produk')

@section('content')
    <div class="card">
        <div class="card-header">
            <div style="display: flex; gap: 16px; align-items: center; width: 100%; justify-content: space-between;">
                <form action="{{ route('categories.index') }}" method="GET" style="display: flex; gap: 8px; flex-grow: 1; max-width: 400px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari kategori..." value="{{ $search }}">
                    <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <button onclick="openAddModal()" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah Kategori
                </button>
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Slug</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Produk</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td><strong>{{ $category->name }}</strong></td>
                                <td><code style="background: #f1f5f9; padding: 2px 6px; border-radius: 4px;">{{ $category->slug }}</code></td>
                                <td>{{ $category->description ?? '-' }}</td>
                                <td><span class="badge badge-primary">{{ $category->products_count }} Produk</span></td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <button onclick="openEditModal({{ json_encode($category) }})" class="btn btn-secondary" style="padding: 6px 10px;" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="delete-form" data-message="Apakah Anda yakin ingin menghapus kategori ini? Semua produk dalam kategori ini juga akan terhapus.">
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
                                <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 32px;">
                                    Kategori tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($categories->hasPages())
                <div style="padding: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: center;">
                    {{ $categories->appends(['search' => $search])->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Kategori Baru</h3>
                <button onclick="closeAddModal()" class="modal-close">&times;</button>
            </div>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_name" class="form-label">Nama Kategori</label>
                        <input type="text" id="add_name" name="name" class="form-control" required placeholder="Contoh: Sembako">
                    </div>
                    <div class="form-group">
                        <label for="add_description" class="form-label">Deskripsi</label>
                        <textarea id="add_description" name="description" class="form-control" rows="3" placeholder="Deskripsi singkat kategori..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeAddModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit Kategori</h3>
                <button onclick="closeEditModal()" class="modal-close">&times;</button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name" class="form-label">Nama Kategori</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description" class="form-label">Deskripsi</label>
                        <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
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
        function openEditModal(category) {
            const form = document.getElementById('editForm');
            form.action = `/categories/${category.id}`;
            document.getElementById('edit_name').value = category.name;
            document.getElementById('edit_description').value = category.description || '';
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
