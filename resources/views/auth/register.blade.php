<x-guest-layout>
    <form id="register-form" method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        {{-- NIK --}}
        <div class="mt-4">
            <x-input-label for="nik" :value="__('NIK')" />
            <x-text-input id="nik" class="block mt-1 w-full"
                type="text"
                name="nik"
                :value="old('nik')"
                required
                minlength="16"
                maxlength="16"
                inputmode="numeric"
                autocomplete="off" />
            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
            <p id="nik-error" class="mt-1 text-sm text-red-600"></p>
        </div>

        {{-- No HP / WA --}}
        <div class="mt-4">
            <x-input-label for="phone" :value="__('No HP / WA')" />
            <x-text-input id="phone" class="block mt-1 w-full"
                type="text"
                name="phone"
                :value="old('phone')"
                required
                autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        {{-- Email (Gmail) --}}
        <div class="mt-4">
            <x-input-label for="email" :value="__('Gmail')" />
            <x-text-input id="email" class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
                placeholder="contoh: nama@gmail.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <p id="email-error" class="mt-1 text-sm text-red-600"></p>
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <p id="password-error" class="mt-1 text-sm text-red-600"></p>
        </div>

        {{-- Confirm Password --}}
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            <p id="password-confirmation-error" class="mt-1 text-sm text-red-600"></p>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
               href="{{ route('login') }}">
                {{ __('Sudah punya akun?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>

    {{-- VALIDASI REALTIME --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('register-form');

            const nikInput = document.getElementById('nik');
            const nikError = document.getElementById('nik-error');

            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('email-error');

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

            function validateNik() {
                const value = nikInput.value.trim();

                if (value.length === 0) {
                    setError(nikInput, nikError, 'NIK wajib diisi.');
                    return false;
                }

                if (!/^\d+$/.test(value)) {
                    setError(nikInput, nikError, 'NIK hanya boleh berisi angka.');
                    return false;
                }

                if (value.length !== 16) {
                    setError(nikInput, nikError, 'NIK harus terdiri dari 16 digit.');
                    return false;
                }

                setError(nikInput, nikError, '');
                return true;
            }

            function validateEmail() {
                const value = emailInput.value.trim();

                if (value.length === 0) {
                    setError(emailInput, emailError, 'Email wajib diisi.');
                    return false;
                }

                const gmailRegex = /^[A-Za-z0-9._%+-]+@gmail\.com$/i;
                if (!gmailRegex.test(value)) {
                    setError(emailInput, emailError, 'Email harus menggunakan @gmail.com.');
                    return false;
                }

                setError(emailInput, emailError, '');
                return true;
            }

            function validatePassword() {
                const value = passwordInput.value;

                if (value.length === 0) {
                    setError(passwordInput, passwordError, 'Password wajib diisi.');
                    return false;
                }

                if (value.length < 8) {
                    setError(passwordInput, passwordError, 'Password minimal 8 karakter.');
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

            // Event realtime
            nikInput.addEventListener('input', validateNik);
            emailInput.addEventListener('input', validateEmail);
            passwordInput.addEventListener('input', () => {
                validatePassword();
                validatePasswordConfirmation(); // supaya kalau password berubah, konfirmasi ikut dicek
            });
            passwordConfirmationInput.addEventListener('input', validatePasswordConfirmation);

            // Cek semua sebelum submit
            form.addEventListener('submit', function (e) {
                const nikValid = validateNik();
                const emailValid = validateEmail();
                const passValid = validatePassword();
                const passConfValid = validatePasswordConfirmation();

                if (!nikValid || !emailValid || !passValid || !passConfValid) {
                    e.preventDefault(); // stop submit kalau masih ada error
                }
            });
        });
    </script>
</x-guest-layout>
