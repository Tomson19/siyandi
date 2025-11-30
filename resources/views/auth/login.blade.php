<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-50 to-white">
        <div>
            <a href="/" class="flex flex-col items-center space-y-1">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-15 h-20">
                <h1 class="text-xl font-bold text-indigo-600 tracking-wide">Sistem Permohonan Benih</h1>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-lg rounded-xl border border-gray-100">
            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Masuk ke Akun Anda</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        type="password"
                        name="password"
                        required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm text-indigo-600 hover:text-indigo-800" href="{{ route('password.request') }}">
                            {{ __('Lupa kata sandi?') }}
                        </a>
                    @endif
                </div>

                <!-- Tombol Login -->
                <div class="mt-6">
                    <x-primary-button class="w-full justify-center py-2.5 text-base">
                        {{ __('Masuk') }}
                    </x-primary-button>
                </div>

                <!-- Link ke Registrasi -->
                @if (Route::has('register'))
                    <p class="mt-6 text-center text-sm text-gray-600">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-indigo-600 font-semibold hover:underline">
                            Daftar di sini
                        </a>
                    </p>
                @endif
            </form>
        </div>

        <p class="mt-6 text-gray-400 text-xs text-center">
            &copy; {{ date('Y') }} Dinas Perkebunan. Semua hak dilindungi.
        </p>
    </div>
</x-guest-layout>
