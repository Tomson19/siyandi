<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Terima kasih telah mendaftar! Sebelum mulai menggunakan SIYANDI, silakan verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirim. Jika Anda tidak menerima email tersebut, kami dengan senang hati akan mengirimkannya lagi.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda gunakan saat pendaftaran.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div class="flex flex-col items-start">
                <x-primary-button id="resend-button" disabled>
                    {{ __('Kirim Ulang Verifikasi Email') }}
                </x-primary-button>

                <p id="countdown-text" class="mt-2 text-xs text-gray-500">
                    Anda dapat mengirim ulang dalam <span id="countdown-timer">02:00</span>
                </p>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>

    {{-- Script hitung mundur --}}
    <script>
        (function () {
            const RESEND_DELAY = 120; // detik
            let remaining = RESEND_DELAY;

            const btn = document.getElementById('resend-button');
            const countdownText = document.getElementById('countdown-text');
            const countdownTimer = document.getElementById('countdown-timer');

            if (!btn || !countdownText || !countdownTimer) return;

            function formatTime(sec) {
                const m = Math.floor(sec / 60);
                const s = sec % 60;
                const mm = String(m).padStart(2, '0');
                const ss = String(s).padStart(2, '0');
                return `${mm}:${ss}`;
            }

            function updateCountdown() {
                if (remaining <= 0) {
                    btn.removeAttribute('disabled');
                    countdownText.textContent = 'Anda dapat mengirim ulang sekarang.';
                    return;
                }

                btn.setAttribute('disabled', 'disabled');
                countdownTimer.textContent = formatTime(remaining);
                remaining--;
            }

            // Jalankan pertama kali
            updateCountdown();

            // Update setiap 1 detik
            const intervalId = setInterval(() => {
                updateCountdown();
                if (remaining < 0) {
                    clearInterval(intervalId);
                }
            }, 1000);
        })();
    </script>
</x-guest-layout>
