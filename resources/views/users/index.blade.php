@extends('layouts.app')

@section('title', 'Staff & Kasir')
@section('header_title', 'Kelola Staff & Kasir')

@section('content')
    <div class="card">
        <div class="card-header">
            <div style="display: flex; gap: 16px; align-items: center; width: 100%; justify-content: space-between;">
                <form action="{{ route('users.index') }}" method="GET" style="display: flex; gap: 8px; flex-grow: 1; max-width: 400px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ $search }}">
                    <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
                <button onclick="openAddModal()" class="btn btn-primary">
                    <i class="fa-solid fa-user-plus"></i> Tambah Staff
                </button>
            </div>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Hak Akses / Role</th>
                            <th>Status</th>
                            <th>Tanggal Bergabung</th>
                            <th style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td><strong>{{ $user->name }}</strong> @if($user->id === Auth::id()) <span style="font-weight: normal; color: var(--text-secondary);">(Anda)</span> @endif</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        @if($role->name === 'admin')
                                            <span class="badge badge-danger">Administrator</span>
                                        @elseif($role->name === 'kasir')
                                            <span class="badge badge-success">Kasir</span>
                                        @else
                                            <span class="badge badge-primary">{{ ucfirst($role->name) }}</span>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('d M Y') }}</td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <button onclick="openEditModal({{ json_encode($user) }}, '{{ $user->roles->first()->name ?? '' }}')" class="btn btn-secondary" style="padding: 6px 10px;" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        @if($user->id !== Auth::id())
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="delete-form" data-message="Apakah Anda yakin ingin menghapus akun staff '{{ $user->name }}' ini?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" style="padding: 6px 10px;" title="Hapus">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-secondary" style="padding: 6px 10px; opacity: 0.5; cursor: not-allowed;" title="Tidak bisa menghapus diri sendiri" disabled>
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary); padding: 32px;">
                                    User tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
                <div style="padding: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: center;">
                    {{ $users->appends(['search' => $search])->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Staff Baru</h3>
                <button onclick="closeAddModal()" class="modal-close">&times;</button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_name" class="form-label">Nama Lengkap</label>
                        <input type="text" id="add_name" name="name" class="form-control" required placeholder="Contoh: Rino">
                    </div>
                    <div class="form-group">
                        <label for="add_email" class="form-label">Email</label>
                        <input type="email" id="add_email" name="email" class="form-control" required placeholder="staff@gmail.com">
                    </div>
                    <div class="form-group">
                        <label for="add_password" class="form-label">Password</label>
                        <input type="password" id="add_password" name="password" class="form-control" required placeholder="Minimal 8 karakter">
                    </div>
                    <div class="form-group">
                        <label for="add_role" class="form-label">Hak Akses / Role</label>
                        <select id="add_role" name="role" class="form-control" required>
                            <option value="">Pilih Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">
                                    @if($role->name === 'admin') Administrator
                                    @elseif($role->name === 'kasir') Kasir
                                    @else {{ ucfirst($role->name) }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="add_is_active" class="form-label">Status Akun</label>
                        <select id="add_is_active" name="is_active" class="form-control" required>
                            <option value="1" selected>Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeAddModal()" class="btn btn-secondary">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit Staff</h3>
                <button onclick="closeEditModal()" class="modal-close">&times;</button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name" class="form-label">Nama Lengkap</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" id="edit_email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_password" class="form-label">Password Baru (Kosongkan jika tidak diubah)</label>
                        <input type="password" id="edit_password" name="password" class="form-control" placeholder="Minimal 8 karakter">
                    </div>
                    <div class="form-group">
                        <label for="edit_role" class="form-label">Hak Akses / Role</label>
                        <select id="edit_role" name="role" class="form-control" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">
                                    @if($role->name === 'admin') Administrator
                                    @elseif($role->name === 'kasir') Kasir
                                    @else {{ ucfirst($role->name) }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_is_active" class="form-label">Status Akun</label>
                        <select id="edit_is_active" name="is_active" class="form-control" required>
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
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
        function openEditModal(user, currentRoleName) {
            const form = document.getElementById('editForm');
            form.action = `/users/${user.id}`;
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_password').value = '';
            document.getElementById('edit_role').value = currentRoleName;
            document.getElementById('edit_is_active').value = user.is_active;
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
