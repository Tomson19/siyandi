@extends('layouts.bootstrap')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-10">

                {{-- HEADER --}}
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-primary bg-gradient text-white d-flex align-items-center justify-content-center me-3"
                         style="width: 44px; height: 44px;">
                        <i class="bi bi-key-fill fs-4"></i>
                    </div>
                    <div>
                        <h1 class="h5 mb-1 mb-0">Ubah Password</h1>
                        <small class="text-muted">
                            Ganti password akun Anda secara berkala untuk menjaga keamanan.
                        </small>
                    </div>
                </div>

                {{-- NOTIF SUKSES --}}
                @if (session('status') === 'password-updated')
                    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                        Password berhasil diperbarui.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form method="POST" action="{{ route('password.update') }}" id="password-form">
                            @csrf
                            @method('PUT')

                            {{-- Password sekarang --}}
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password Sekarang</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input
                                        type="password"
                                        id="current_password"
                                        name="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        autocomplete="current-password"
                                        required
                                    >
                                </div>
                                @error('current_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Password baru --}}
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-shield-lock-fill"></i>
                                    </span>
                                    <input
                                        type="password"
                                        id="password"
                                        name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        autocomplete="new-password"
                                        required
                                    >
                                </div>
                                {{-- Error dari server --}}
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                {{-- Error dari JS (client-side) --}}
                                <div id="password-error" class="text-danger small mt-1"></div>

                                <small class="text-muted d-block mt-1">
                                    Minimal 8 karakter. Gunakan kombinasi huruf dan angka agar lebih aman.
                                </small>
                            </div>

                            {{-- Konfirmasi password baru --}}
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-shield-check"></i>
                                    </span>
                                    <input
                                        type="password"
                                        id="password_confirmation"
                                        name="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        autocomplete="new-password"
                                        required
                                    >
                                </div>
                                {{-- Error dari server --}}
                                @error('password_confirmation')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                {{-- Error dari JS (client-side) --}}
                                <div id="password-confirmation-error" class="text-danger small mt-1"></div>
                            </div>

                            <div class="d-flex justify-content-end pt-2">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Simpan Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('password-form');
            const passwordInput = document.getElementById('password');
            const passwordError = document.getElementById('password-error');

            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const passwordConfirmationError = document.getElementById('password-confirmation-error');

            function setError(input, errorElement, message) {
                if (message) {
                    errorElement.textContent = message;
                    input.classList.add('is-invalid');
                } else {
                    errorElement.textContent = '';
                    input.classList.remove('is-invalid');
                }
            }

            function validatePassword() {
                const value = passwordInput.value;

                if (value.length === 0) {
                    setError(passwordInput, passwordError, 'Password baru wajib diisi.');
                    return false;
                }

                if (value.length < 8) {
                    setError(passwordInput, passwordError, 'Password baru minimal 8 karakter.');
                    return false;
                }

                setError(passwordInput, passwordError, '');
                return true;
            }

            function validatePasswordConfirmation() {
                const value = passwordConfirmationInput.value;

                if (value.length === 0) {
                    setError(passwordConfirmationInput, passwordConfirmationError, 'Konfirmasi password wajib diisi.');
                    return false;
                }

                if (value !== passwordInput.value) {
                    setError(passwordConfirmationInput, passwordConfirmationError, 'Konfirmasi password tidak sama.');
                    return false;
                }

                setError(passwordConfirmationInput, passwordConfirmationError, '');
                return true;
            }

            // Realtime events
            passwordInput.addEventListener('input', () => {
                validatePassword();
                validatePasswordConfirmation(); // kalau password berubah, cek ulang konfirmasi
            });

            passwordConfirmationInput.addEventListener('input', validatePasswordConfirmation);

            // Cek saat submit
            form.addEventListener('submit', function (e) {
                const passValid = validatePassword();
                const passConfValid = validatePasswordConfirmation();

                if (!passValid || !passConfValid) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endpush
