<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}" id="reset-password-form">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full"
                          type="email"
                          name="email"
                          :value="old('email', $request->email)"
                          required
                          autofocus
                          autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password Baru')" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required
                          autocomplete="new-password" />
            <!-- error dari server -->
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <!-- error dari JS (client-side) -->
            <p id="password-error" class="mt-1 text-sm text-red-600"></p>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation"
                          required
                          autocomplete="new-password" />

            <!-- error dari server -->
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            <!-- error dari JS (client-side) -->
            <p id="password-confirmation-error" class="mt-1 text-sm text-red-600"></p>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Reset Password') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('reset-password-form');

            const passwordInput = document.getElementById('password');
            const passwordError = document.getElementById('password-error');

            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const passwordConfirmationError = document.getElementById('password-confirmation-error');

            function setError(input, errorElement, message) {
                if (message) {
                    errorElement.textContent = message;
                    input.classList.add('border-red-500');
                } else {
                    errorElement.textContent = '';
                    input.classList.remove('border-red-500');
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

            // realtime
            passwordInput.addEventListener('input', () => {
                validatePassword();
                validatePasswordConfirmation(); // kalau password berubah, cek ulang konfirmasi
            });

            passwordConfirmationInput.addEventListener('input', validatePasswordConfirmation);

            // cek sebelum submit
            form.addEventListener('submit', function (e) {
                const passValid = validatePassword();
                const passConfValid = validatePasswordConfirmation();

                if (!passValid || !passConfValid) {
                    e.preventDefault();
                }
            });
        });
    </script>
</x-guest-layout>
