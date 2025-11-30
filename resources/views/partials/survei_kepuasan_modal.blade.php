{{-- Modal Survei Kepuasan --}}
<div class="modal fade" id="surveiKepuasanModal" tabindex="-1" aria-labelledby="surveiKepuasanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="surveiKepuasanForm" method="POST" action="{{ route('survei_kepuasan.store') }}">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="surveiKepuasanLabel">Survei Kepuasan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">

                    {{-- STEP CONTAINER --}}
                    <div id="survei-steps">
                        {{-- Step indicator kecil --}}
                        <div class="mb-3 text-center small text-muted">
                            <span id="survei-step-indicator">Langkah 1 dari 6</span>
                        </div>

                        {{-- STEP 1 --}}
                        <div class="survei-step" data-step="1">
                            <h6 class="fw-bold mb-2">1. Bagaimana tampilan situs web?</h6>
                            <p class="text-muted small">
                                Pilih rating yang menggambarkan penilaian Anda terhadap tampilan situs.
                            </p>
                            <select name="q1_tampilan" id="q1_tampilan" class="form-select form-select-sm" required>
                                <option value="">Pilih rating</option>
                                <option value="sangat_puas">Sangat puas</option>
                                <option value="puas">Puas</option>
                                <option value="cukup">Cukup</option>
                                <option value="kurang_puas">Kurang puas</option>
                                <option value="tidak_puas">Tidak puas</option>
                            </select>
                            <div class="text-danger small mt-1 d-none" id="error-q1"></div>
                        </div>

                        {{-- STEP 2 --}}
                        <div class="survei-step d-none" data-step="2">
                            <h6 class="fw-bold mb-2">2. Bagaimana fitur pada web?</h6>
                            <p class="text-muted small">
                                Nilai kemudahan penggunaan dan kelengkapan fitur yang tersedia.
                            </p>
                            <select name="q2_fitur" id="q2_fitur" class="form-select form-select-sm" required>
                                <option value="">Pilih rating</option>
                                <option value="sangat_puas">Sangat puas</option>
                                <option value="puas">Puas</option>
                                <option value="cukup">Cukup</option>
                                <option value="kurang_puas">Kurang puas</option>
                                <option value="tidak_puas">Tidak puas</option>
                            </select>
                            <div class="text-danger small mt-1 d-none" id="error-q2"></div>
                        </div>

                        {{-- STEP 3 --}}
                        <div class="survei-step d-none" data-step="3">
                            <h6 class="fw-bold mb-2">3. Apakah Anda menemukan informasi yang Anda cari?</h6>
                            <p class="text-muted small">
                                Tuliskan jawaban atau pengalaman Anda ketika mencari informasi di situs ini.
                            </p>
                            <textarea name="q3_informasi"
                                      id="q3_informasi"
                                      rows="3"
                                      class="form-control form-control-sm"
                                      placeholder="Contoh: Ya, saya menemukan informasi ... atau Tidak, karena ..."></textarea>
                        </div>

                        {{-- STEP 4 --}}
                        <div class="survei-step d-none" data-step="4">
                            <h6 class="fw-bold mb-2">4. Apa yang paling Anda sukai tentang penggunaan situs web ini?</h6>
                            <p class="text-muted small">
                                Ceritakan bagian yang menurut Anda paling membantu / menyenangkan.
                            </p>
                            <textarea name="q4_sukai"
                                      id="q4_sukai"
                                      rows="3"
                                      class="form-control form-control-sm"
                                      placeholder="Contoh: Tampilan sederhana, menu jelas, informasi lengkap, dsb."></textarea>
                        </div>

                        {{-- STEP 5 --}}
                        <div class="survei-step d-none" data-step="5">
                            <h6 class="fw-bold mb-2">5. Bagaimana Anda menilai kinerja situs web ini?</h6>
                            <p class="text-muted small">
                                Nilai kecepatan, stabilitas, dan respon situs saat digunakan.
                            </p>
                            <select name="q5_kinerja" id="q5_kinerja" class="form-select form-select-sm" required>
                                <option value="">Pilih rating</option>
                                <option value="sangat_puas">Sangat puas</option>
                                <option value="puas">Puas</option>
                                <option value="cukup">Cukup</option>
                                <option value="kurang_puas">Kurang puas</option>
                                <option value="tidak_puas">Tidak puas</option>
                            </select>
                            <div class="text-danger small mt-1 d-none" id="error-q5"></div>
                        </div>

                        {{-- STEP 6 --}}
                        <div class="survei-step d-none" data-step="6">
                            <h6 class="fw-bold mb-2">6. Apa yang Anda rekomendasikan untuk perbaikan pengembangan situs web ini?</h6>
                            <p class="text-muted small">
                                Saran Anda sangat berarti untuk pengembangan layanan ke depan.
                            </p>
                            <textarea name="q6_rekomendasi"
                                      id="q6_rekomendasi"
                                      rows="3"
                                      class="form-control form-control-sm"
                                      placeholder="Contoh: Tambahkan fitur ..., perbaiki tampilan ..., dll."></textarea>
                        </div>
                    </div>

                    {{-- SUCCESS STATE --}}
                    <div id="survei-success" class="text-center py-4 d-none">
                        <div class="display-4 text-success mb-2">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Terima Kasih!</h5>
                        <p class="text-muted mb-3">
                            Jawaban survei berhasil disimpan. Terima kasih atas partisipasi dan masukannya.
                        </p>
                        <button type="button" class="btn btn-sm btn-primary" id="surveiSuccessOkBtn">
                            OK
                        </button>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            id="surveiPrevBtn">
                        Kembali
                    </button>
                    <button type="button"
                            class="btn btn-sm btn-primary"
                            id="surveiNextBtn">
                        Selanjutnya
                    </button>
                    <button type="submit"
                            class="btn btn-sm btn-success d-none"
                            id="surveiSubmitBtn">
                        Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form         = document.getElementById('surveiKepuasanForm');
    const steps        = document.querySelectorAll('.survei-step');
    const indicator    = document.getElementById('survei-step-indicator');
    const prevBtn      = document.getElementById('surveiPrevBtn');
    const nextBtn      = document.getElementById('surveiNextBtn');
    const submitBtn    = document.getElementById('surveiSubmitBtn');
    const successBox   = document.getElementById('survei-success');
    const stepsBox     = document.getElementById('survei-steps');
    const successOkBtn = document.getElementById('surveiSuccessOkBtn');

    const modalEl  = document.getElementById('surveiKepuasanModal');
    const modal    = modalEl ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;

    let currentStep = 1;
    const maxStep   = 6;

    function showStep(step) {
        steps.forEach(s => {
            const sStep = parseInt(s.getAttribute('data-step'));
            s.classList.toggle('d-none', sStep !== step);
        });

        indicator.textContent = `Langkah ${step} dari ${maxStep}`;

        // Tombol prev/next/submit
        if (step === 1) {
            prevBtn.classList.add('disabled');
        } else {
            prevBtn.classList.remove('disabled');
        }

        if (step === maxStep) {
            nextBtn.classList.add('d-none');
            submitBtn.classList.remove('d-none');
        } else {
            nextBtn.classList.remove('d-none');
            submitBtn.classList.add('d-none');
        }
    }

    function resetErrors() {
        ['error-q1', 'error-q2', 'error-q5'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = '';
                el.classList.add('d-none');
            }
        });
    }

    function validateCurrentStep() {
        resetErrors();
        let valid = true;

        if (currentStep === 1) {
            const val = document.getElementById('q1_tampilan').value;
            if (!val) {
                const err = document.getElementById('error-q1');
                err.textContent = 'Silakan pilih rating tampilan situs.';
                err.classList.remove('d-none');
                valid = false;
            }
        }

        if (currentStep === 2) {
            const val = document.getElementById('q2_fitur').value;
            if (!val) {
                const err = document.getElementById('error-q2');
                err.textContent = 'Silakan pilih rating fitur pada web.';
                err.classList.remove('d-none');
                valid = false;
            }
        }

        if (currentStep === 5) {
            const val = document.getElementById('q5_kinerja').value;
            if (!val) {
                const err = document.getElementById('error-q5');
                err.textContent = 'Silakan pilih rating kinerja situs.';
                err.classList.remove('d-none');
                valid = false;
            }
        }

        return valid;
    }

    prevBtn.addEventListener('click', function () {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });

    nextBtn.addEventListener('click', function () {
        if (!validateCurrentStep()) return;
        if (currentStep < maxStep) {
            currentStep++;
            showStep(currentStep);
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!validateCurrentStep()) return;

        const formData = new FormData(form);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async (res) => {
            if (!res.ok) {
                // kalau validasi backend gagal, bisa tambahin handling di sini
                return;
            }

            // tampilkan success box
            stepsBox.classList.add('d-none');
            successBox.classList.remove('d-none');
            prevBtn.classList.add('d-none');
            nextBtn.classList.add('d-none');
            submitBtn.classList.add('d-none');
        })
        .catch((err) => {
            console.error(err);
        });
    });

    // Kalau OK di success diklik: tutup modal & reset
    successOkBtn.addEventListener('click', function () {
        if (modal) {
            modal.hide();
        }
    });

    // Reset form setiap modal ditutup
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            form.reset();
            successBox.classList.add('d-none');
            stepsBox.classList.remove('d-none');
            prevBtn.classList.remove('d-none');
            nextBtn.classList.remove('d-none');
            currentStep = 1;
            resetErrors();
            showStep(currentStep);
        });

        // saat pertama kali buka
        modalEl.addEventListener('shown.bs.modal', function () {
            currentStep = 1;
            resetErrors();
            showStep(currentStep);
        });
    }

    // initial
    showStep(currentStep);
});
</script>
@endpush
