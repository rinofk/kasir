@extends('layouts.app')

@section('title', 'Hak Akses & Role')
@section('header_title', 'Kelola Hak Akses (Role & Permission)')

@section('content')
    <div class="card">
        <div class="card-header">
            <span class="card-title"><i class="fa-solid fa-user-shield"></i> Daftar Role / Tingkat Jabatan</span>
            <button onclick="openAddModal()" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Tambah Role Baru
            </button>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Role</th>
                            <th>Hak Akses / Permissions</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                            <tr>
                                <td>
                                    <strong style="text-transform: uppercase;">{{ $role->name }}</strong>
                                    @if($role->name === 'admin')
                                        <span class="badge badge-danger" style="margin-left: 8px;">Sistem Utama</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                        @forelse($role->permissions as $perm)
                                            <span class="badge badge-primary" style="font-size: 11px;">{{ $perm->name }}</span>
                                        @empty
                                            <span class="badge badge-warning" style="font-size: 11px;">Tidak ada hak akses</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <button onclick="openEditModal({{ json_encode($role) }}, {{ json_encode($role->permissions->pluck('name')) }})" class="btn btn-secondary" style="padding: 6px 10px;" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        @if($role->name !== 'admin')
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="delete-form" data-message="Apakah Anda yakin ingin menghapus role '{{ $role->name }}' ini?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" style="padding: 6px 10px;" title="Hapus">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-secondary" style="padding: 6px 10px; opacity: 0.5; cursor: not-allowed;" title="Role utama sistem tidak bisa dihapus" disabled>
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Role Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content" style="max-width: 550px;">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Role Baru</h3>
                <button onclick="closeAddModal()" class="modal-close">&times;</button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_name" class="form-label">Nama Role (Gunakan huruf kecil)</label>
                        <input type="text" id="add_name" name="name" class="form-control" required placeholder="Contoh: supervisor">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Centang Hak Akses (Permissions)</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 10px; background: #f8fafc; padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                            @foreach($permissions as $perm)
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_add_{{ $perm->id }}" style="cursor: pointer; width: 16px; height: 16px;">
                                    <label for="perm_add_{{ $perm->id }}" style="cursor: pointer; font-size: 13px; font-weight: 500;">{{ $perm->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeAddModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Role Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content" style="max-width: 550px;">
            <div class="modal-header">
                <h3 class="modal-title">Edit Role</h3>
                <button onclick="closeEditModal()" class="modal-close">&times;</button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name" class="form-label">Nama Role</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Hak Akses (Permissions)</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 10px; background: #f8fafc; padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                            @foreach($permissions as $perm)
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" id="perm_edit_{{ $perm->id }}" class="edit-perm-checkbox" style="cursor: pointer; width: 16px; height: 16px;">
                                    <label for="perm_edit_{{ $perm->id }}" style="cursor: pointer; font-size: 13px; font-weight: 500;">{{ $perm->name }}</label>
                                </div>
                            @endforeach
                        </div>
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
        function openEditModal(role, assignedPermissions) {
            const form = document.getElementById('editForm');
            form.action = `/roles/${role.id}`;
            document.getElementById('edit_name').value = role.name;
            
            if (role.name === 'admin') {
                document.getElementById('edit_name').disabled = true;
            } else {
                document.getElementById('edit_name').disabled = false;
            }

            const checkboxes = document.querySelectorAll('.edit-perm-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = assignedPermissions.includes(cb.value);
            });

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
