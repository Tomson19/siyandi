@extends('layouts.bootstrap')

@section('content')
<style>
  body{
    margin-top: -70px;
  }
</style>
    <div class="container-fluid py-4">
        {{-- HEADER --}}
        <div class="row mb-3">
            <div class="col">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-gradient text-white d-flex align-items-center justify-content-center me-3"
                        style="width: 42px; height: 42px;">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                    <div>
                        <h1 class="h5 mb-1">Manajemen User</h1>
                    </div>
                </div>
            </div>
        </div>

        {{-- NOTIF --}}
        @if (session('status') === 'user-updated')
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                Data user berhasil diperbarui.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- RINGKASAN JUMLAH --}}
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total User</div>
                            <div class="h5 mb-0">{{ $totalUsers }}</div>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2">
                            <i class="bi bi-people fs-3 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total Pemohon</div>
                            <div class="h5 mb-0">{{ $totalPemohon }}</div>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-2">
                            <i class="bi bi-person-badge fs-3 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total Admin</div>
                            <div class="h5 mb-0">{{ $totalAdmin }}</div>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-2">
                            <i class="bi bi-shield-lock fs-3 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- PENCARIAN + FILTER --}}
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body py-3">
                <form method="GET" action="{{ route('admin.super.users.index') }}" class="row gy-2 gx-2 align-items-center">
                    {{-- Search --}}
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text"
                                name="q"
                                class="form-control"
                                placeholder="Cari nama atau email..."
                                value="{{ $search }}">
                        </div>
                    </div>

                    {{-- Filter Tipe User --}}
                    <div class="col-md-3">
                        <select name="type" class="form-select form-select-sm">
                            <option value="">Semua Tipe User</option>
                            <option value="admin" {{ ($type ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="pemohon" {{ ($type ?? '') === 'pemohon' ? 'selected' : '' }}>Pemohon</option>
                        </select>
                    </div>

                    {{-- Filter Status Email --}}
                    <div class="col-md-3">
                        <select name="email_status" class="form-select form-select-sm">
                            <option value="">Semua Status Email</option>
                            <option value="verified" {{ ($emailStatus ?? '') === 'verified' ? 'selected' : '' }}>
                                Terverifikasi
                            </option>
                            <option value="unverified" {{ ($emailStatus ?? '') === 'unverified' ? 'selected' : '' }}>
                                Belum Verifikasi
                            </option>
                        </select>
                    </div>

                    {{-- Tombol --}}
                    <div class="col-md-2 text-md-end">
                        <div class="d-flex justify-content-md-end gap-2">
                            <button class="btn btn-sm btn-outline-secondary" type="submit">
                                <i class="bi bi-funnel me-1"></i> Filter
                            </button>
                            <a href="{{ route('admin.super.users.index') }}" class="btn btn-sm btn-outline-light border">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABEL USER --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
    <thead class="table-light">
        <tr>
            <th class="text-center" style="width:70px;">NO</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th class="text-center" style="width:90px;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($users as $index => $user)
            <tr>
                {{-- NO --}}
                <td class="text-center">
                    {{ $users->firstItem() + $index }}
                </td>

                {{-- NAMA --}}
                <td>
                    <strong>{{ $user->name }}</strong>
                </td>

                {{-- EMAIL + STATUS --}}
                <td>
                    <div>{{ $user->email }}</div>

                    @if ($user->pending_email)
                        <div class="small text-warning">
                            pending: {{ $user->pending_email }}
                        </div>
                    @endif

                    @if ($user->email_verified_at)
                        <div class="small text-success">
                            <i class="bi bi-shield-check"></i> Terverifikasi
                        </div>
                    @else
                        <div class="small text-muted">
                            <i class="bi bi-exclamation-triangle"></i> Belum verifikasi
                        </div>
                    @endif
                </td>

                {{-- ROLE --}}
                <td>
                    @php
                        $roleNames = $user->roles->pluck('name')->toArray();
                    @endphp
                    @forelse ($roleNames as $r)
                        <span class="badge bg-secondary me-1 text-uppercase small">
                            {{ $r }}
                        </span>
                    @empty
                        <span class="text-muted small">-</span>
                    @endforelse
                </td>

                {{-- AKSI --}}
                <td class="text-center">
                    <button type="button"
                        class="btn btn-sm btn-outline-primary btn-edit-user"
                        data-bs-toggle="modal"
                        data-bs-target="#editUserModal"
                        data-id="{{ $user->id }}"
                        data-name="{{ $user->name }}"
                        data-email="{{ $user->email }}"
                        data-roles="{{ $user->roles->pluck('name')->implode(', ') }}"
                        data-verified="{{ $user->email_verified_at ? '1' : '0' }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-3">
                    Tidak ada user ditemukan.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

                </div>

                <div class="p-2">
                    {{ $users->links() }}
                </div>

            </div>
        </div>
    </div>

    {{-- ============ MODAL EDIT USER ============ --}}
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" id="editUserForm">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>

                    <div class="modal-body">
                        {{-- INFO ROLE + STATUS EMAIL --}}
                        <div class="mb-3 small">
                            <div><strong>ID:</strong> <span id="info-user-id"></span></div>
                            <div><strong>Role:</strong> <span id="info-user-roles"></span></div>
                            <div><strong>Status Email:</strong>
                                <span id="info-user-email-status"></span>
                            </div>
                        </div>

                        {{-- Nama --}}
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nama Lengkap</label>
                            <input type="text"
                                id="edit_name"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                required>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="edit_name_error" class="text-danger small mt-1"></div>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email"
                                    id="edit_email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    required>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="edit_email_error" class="text-danger small mt-1"></div>

                            <small class="text-muted d-block mt-1">
                                Jika email diganti untuk <strong>pemohon</strong>, sistem akan mengirim email verifikasi ke alamat baru.
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.btn-edit-user');
            const editForm = document.getElementById('editUserForm');

            const nameInput = document.getElementById('edit_name');
            const emailInput = document.getElementById('edit_email');

            const nameError = document.getElementById('edit_name_error');
            const emailError = document.getElementById('edit_email_error');

            const infoId = document.getElementById('info-user-id');
            const infoRoles = document.getElementById('info-user-roles');
            const infoStatus = document.getElementById('info-user-email-status');

            const updateUrlTemplate = "{{ route('admin.super.users.update', ['user' => '__ID__']) }}";

            function setError(input, errorElement, message) {
                if (message) {
                    errorElement.textContent = message;
                    input.classList.add('is-invalid');
                } else {
                    errorElement.textContent = '';
                    input.classList.remove('is-invalid');
                }
            }

            function validateName() {
                const value = nameInput.value.trim();

                if (!value) {
                    setError(nameInput, nameError, 'Nama wajib diisi.');
                    return false;
                }

                if (value.length < 3) {
                    setError(nameInput, nameError, 'Nama minimal 3 karakter.');
                    return false;
                }

                setError(nameInput, nameError, '');
                return true;
            }

            function validateEmail() {
                const value = emailInput.value.trim();

                if (!value) {
                    setError(emailInput, emailError, 'Email wajib diisi.');
                    return false;
                }

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    setError(emailInput, emailError, 'Format email tidak valid.');
                    return false;
                }

                setError(emailInput, emailError, '');
                return true;
            }

            editButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    const email = this.dataset.email;
                    const roles = this.dataset.roles || '-';
                    const verified = this.dataset.verified === '1';

                    infoId.textContent = id;
                    infoRoles.textContent = roles;

                    infoStatus.innerHTML = verified
                        ? '<span class="badge bg-success"><i class="bi bi-shield-check"></i> Terverifikasi</span>'
                        : '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle"></i> Belum verifikasi</span>';

                    nameInput.value = name;
                    emailInput.value = email;

                    setError(nameInput, nameError, '');
                    setError(emailInput, emailError, '');

                    const actionUrl = updateUrlTemplate.replace('__ID__', id);
                    editForm.setAttribute('action', actionUrl);
                });
            });

            nameInput.addEventListener('input', validateName);
            emailInput.addEventListener('input', validateEmail);

            editForm.addEventListener('submit', function(e) {
                const nameValid = validateName();
                const emailValid = validateEmail();

                if (!nameValid || !emailValid) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endpush
