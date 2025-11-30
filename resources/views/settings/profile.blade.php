@extends('layouts.bootstrap')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-10">

                {{-- HEADER --}}
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-success bg-gradient text-white d-flex align-items-center justify-content-center me-3"
                        style="width: 44px; height: 44px;">
                        <i class="bi bi-person-badge fs-4"></i>
                    </div>
                    <div>
                        <h1 class="h5 mb-1 mb-0">Profil Akun</h1>
                        <small class="text-muted">
                            Informasi akun yang digunakan di SIYANDI.
                        </small>
                    </div>
                </div>

                {{-- NOTIF SUKSES --}}
                @if (session('status') === 'profile-updated')
                    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                        Profil berhasil diperbarui.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @elseif (session('status') === 'profile-updated-pending-email')
                    <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                        Profil berhasil diperbarui. Kami telah mengirim link konfirmasi ke email baru Anda.
                        Alamat email untuk login akan berubah setelah Anda mengklik link tersebut.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif


                {{-- CARD RINGKASAN PROFIL (READ-ONLY) --}}
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h2 class="h6 mb-1">{{ $user->name }}</h2>
                                <div class="text-muted small">
                                    @if ($user->hasRole('pemohon'))
                                        <span class="badge bg-success bg-opacity-75 me-1">
                                            <i class="bi bi-person-check me-1"></i> Pemohon
                                        </span>
                                    @else
                                        <span class="badge bg-primary bg-opacity-75 me-1">
                                            <i class="bi bi-shield-lock me-1"></i>
                                            {{ strtoupper($user->roles->pluck('name')->first() ?? 'User') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editProfileModal">
                                <i class="bi bi-pencil-square me-1"></i> Edit Profil
                            </button>
                        </div>

                        <dl class="row mb-0 small">
                            @if ($user->hasRole('pemohon'))
                                <dt class="col-sm-4">NIK</dt>
                                <dd class="col-sm-8 mb-1">
                                    {{ $user->nik ?? '-' }}
                                </dd>

                                <dt class="col-sm-4">No HP / WA</dt>
                                <dd class="col-sm-8 mb-1">
                                    {{ $user->phone ?? '-' }}
                                </dd>
                            @endif

                            <dt class="col-sm-4">Email</dt>
                            <dd class="col-sm-8 mb-1">
                                {{ $user->email }}
                                @if ($user->hasRole('pemohon'))
                                    @if ($user->hasVerifiedEmail())
                                        <span class="badge bg-success bg-opacity-75 ms-1">
                                            <i class="bi bi-shield-check"></i> Terverifikasi
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark ms-1">
                                            <i class="bi bi-exclamation-triangle"></i> Belum verifikasi
                                        </span>
                                    @endif
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                {{-- ================== MODAL EDIT PROFIL ================== --}}
                <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form id="profile-form" method="POST" action="{{ route('settings.profile.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="modal-header">
                                    <h5 class="modal-title" id="editProfileModalLabel">
                                        Edit Profil
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <div class="modal-body">
                                    {{-- Nama --}}
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama Lengkap</label>
                                        <input type="text" id="name" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $user->name) }}" required autocomplete="name">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if ($user->hasRole('pemohon'))
                                        <div class="row">
                                            {{-- NIK --}}
                                            <div class="col-md-6 mb-3">
                                                <label for="nik" class="form-label">NIK</label>
                                                <input type="text" id="nik" name="nik"
                                                    class="form-control @error('nik') is-invalid @enderror"
                                                    value="{{ old('nik', $user->nik) }}" required minlength="16"
                                                    maxlength="16" inputmode="numeric" autocomplete="off">
                                                @error('nik')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            {{-- No HP / WA --}}
                                            <div class="col-md-6 mb-3">
                                                <label for="phone" class="form-label">No HP / WA</label>
                                                <input type="text" id="phone" name="phone"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    value="{{ old('phone', $user->phone) }}" required autocomplete="tel">
                                                @error('phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Email --}}
                                    <div class="mb-2">
                                        <label for="email" class="form-label">Email</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="bi bi-envelope"></i>
                                            </span>
                                            <input type="email" id="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $user->email) }}" required
                                                autocomplete="username">
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror

                                        <small class="text-muted d-block mt-1">
                                            @if ($user->hasRole('pemohon'))
                                                Jika Anda mengganti email, sistem akan mengirim link verifikasi
                                                ke email baru. Pastikan alamat benar.
                                            @else
                                                Email digunakan untuk login dan reset password.
                                            @endif
                                        </small>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                                        Batal
                                    </button>
                                    <button type="submit" class="btn btn-success" id="btn-submit-profile">
                                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ================== MODAL KONFIRMASI SIMPAN ================== --}}
                <div class="modal fade" id="confirmSaveModal" tabindex="-1" aria-labelledby="confirmSaveModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title" id="confirmSaveModalLabel">
                                    Konfirmasi Perubahan
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-1">
                                    Apakah Anda yakin ingin menyimpan perubahan pada data profil?
                                </p>
                                <small class="text-muted">
                                    Periksa kembali nama, email, dan data lain sebelum melanjutkan.
                                </small>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    Batal
                                </button>
                                <button type="button" class="btn btn-success" id="confirmSaveButton">
                                    Ya, simpan perubahan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @if ($user->pending_email)
                    <div class="alert alert-warning small py-2 mb-3">
                        <strong>Perhatian:</strong> Anda memiliki email baru yang menunggu konfirmasi:
                        <strong>{{ $user->pending_email }}</strong>. Silakan cek inbox Gmail tersebut.
                    </div>
                @endif
            </div>

        </div>


    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileForm = document.getElementById('profile-form');
            const confirmModalEl = document.getElementById('confirmSaveModal');
            const confirmModal = new bootstrap.Modal(confirmModalEl);
            const confirmButton = document.getElementById('confirmSaveButton');

            let allowSubmit = false;

            if (profileForm) {
                profileForm.addEventListener('submit', function(e) {
                    if (!allowSubmit) {
                        e.preventDefault();
                        confirmModal.show();
                    }
                });
            }

            if (confirmButton) {
                confirmButton.addEventListener('click', function() {
                    allowSubmit = true;
                    profileForm.submit();
                });
            }
        });
    </script>
@endpush
