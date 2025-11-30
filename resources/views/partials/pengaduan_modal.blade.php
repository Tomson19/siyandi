{{-- Modal Pengaduan --}}
<div class="modal fade" id="pengaduanModal" tabindex="-1" aria-labelledby="pengaduanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form id="pengaduanForm" method="POST" action="{{ route('pengaduan.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="pengaduanModalLabel">Form Pengaduan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    {{-- FORM --}}
                    <div id="pengaduan-form-wrapper">
                        <p class="text-muted small mb-3">
                            Silakan isi form berikut untuk menyampaikan pengaduan Anda. Data Anda akan dijaga kerahasiaannya.
                        </p>

                        <div class="row g-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label small">
                                        Nama Lengkap <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                           name="nama"
                                           id="pengaduan_nama"
                                           class="form-control form-control-sm"
                                           required>
                                    <div class="text-danger small mt-1 d-none" id="error-nama"></div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small">NIK</label>
                                    <input type="text"
                                           name="nik"
                                           id="pengaduan_nik"
                                           class="form-control form-control-sm"
                                           maxlength="16"
                                           inputmode="numeric"
                                           pattern="\d*"
                                           placeholder="16 digit">
                                    <div class="text-danger small mt-1 d-none" id="error-nik"></div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label small">Alamat</label>
                                    <input type="text"
                                           name="alamat"
                                           id="pengaduan_alamat"
                                           class="form-control form-control-sm"
                                           placeholder="Alamat lengkap">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small">No HP / WA</label>
                                    <input type="text"
                                           name="no_hp"
                                           id="pengaduan_no_hp"
                                           class="form-control form-control-sm"
                                           placeholder="Contoh: 08xxxxxxxxxx">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label small">Lampiran Gambar (opsional)</label>
                                    <input type="file"
                                           name="gambar"
                                           id="pengaduan_gambar"
                                           class="form-control form-control-sm"
                                           accept="image/jpeg,image/jpg,image/png">
                                    <div class="text-danger small mt-1 d-none" id="error-gambar"></div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label small">
                                    Isi Pengaduan <span class="text-danger">*</span>
                                </label>
                                <textarea name="pengaduan"
                                          id="pengaduan_isi"
                                          rows="4"
                                          class="form-control form-control-sm"
                                          required
                                          placeholder="Jelaskan pengaduan Anda secara singkat, jelas, dan lengkap."></textarea>
                                <div class="text-danger small mt-1 d-none" id="error-pengaduan"></div>
                            </div>
                        </div>
                    </div>

                    {{-- SUCCESS STATE --}}
                    <div id="pengaduan-success" class="text-center py-4 d-none">
                        <div class="display-4 text-success mb-2">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <h5 class="fw-bold mb-2">Terima Kasih!</h5>
                        <p class="text-muted mb-3">
                            Pengaduan Anda berhasil dikirim. Terima kasih atas partisipasi dan kepercayaannya.
                        </p>
                        <button type="button" class="btn btn-sm btn-primary" id="pengaduanSuccessOkBtn">
                            OK
                        </button>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">
                        Tutup
                    </button>
                    <button type="submit"
                            class="btn btn-sm btn-primary"
                            id="pengaduanSubmitBtn">
                        Kirim Pengaduan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form        = document.getElementById('pengaduanForm');
    const modalEl     = document.getElementById('pengaduanModal');
    const modal       = modalEl ? bootstrap.Modal.getOrCreateInstance(modalEl) : null;

    const formWrap    = document.getElementById('pengaduan-form-wrapper');
    const successBox  = document.getElementById('pengaduan-success');
    const okBtn       = document.getElementById('pengaduanSuccessOkBtn');
    const submitBtn   = document.getElementById('pengaduanSubmitBtn');

    // error elements
    const errNama      = document.getElementById('error-nama');
    const errNik       = document.getElementById('error-nik');
    const errPengaduan = document.getElementById('error-pengaduan');
    const errGambar    = document.getElementById('error-gambar');

    function resetErrors() {
        [errNama, errNik, errPengaduan, errGambar].forEach(el => {
            if (!el) return;
            el.textContent = '';
            el.classList.add('d-none');
        });
    }

    function validateFront() {
        resetErrors();
        let valid = true;

        const nama = document.getElementById('pengaduan_nama').value.trim();
        const nik  = document.getElementById('pengaduan_nik').value.trim();
        const isi  = document.getElementById('pengaduan_isi').value.trim();

        if (!nama) {
            errNama.textContent = 'Nama wajib diisi.';
            errNama.classList.remove('d-none');
            valid = false;
        }

        if (nik && nik.length !== 16) {
            errNik.textContent = 'NIK harus 16 digit bila diisi.';
            errNik.classList.remove('d-none');
            valid = false;
        }

        if (!isi) {
            errPengaduan.textContent = 'Isi pengaduan wajib diisi.';
            errPengaduan.classList.remove('d-none');
            valid = false;
        }

        return valid;
    }

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!validateFront()) return;

        const formData  = new FormData(form);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        submitBtn.disabled = true;
        submitBtn.innerText = 'Mengirim...';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(async (res) => {
            if (res.status === 422) {
                // tampilkan kembali tombol & tulisannya
                submitBtn.disabled = false;
                submitBtn.innerText = 'Kirim Pengaduan';

                const data   = await res.json();
                const errors = data.errors || {};

                resetErrors();

                if (errors.nama) {
                    errNama.textContent = errors.nama[0];
                    errNama.classList.remove('d-none');
                }
                if (errors.nik) {
                    errNik.textContent = errors.nik[0];
                    errNik.classList.remove('d-none');
                }
                if (errors.pengaduan) {
                    errPengaduan.textContent = errors.pengaduan[0];
                    errPengaduan.classList.remove('d-none');
                }
                if (errors.gambar) {
                    errGambar.textContent = errors.gambar[0];
                    errGambar.classList.remove('d-none');
                }

                return;
            }

            if (!res.ok) {
                console.error('Gagal kirim pengaduan', res.status);
                submitBtn.disabled = false;
                submitBtn.innerText = 'Kirim Pengaduan';
                return;
            }

            // ==== SUKSES ====
            formWrap.classList.add('d-none');
            successBox.classList.remove('d-none');

            // sembunyikan tombol submit di footer
            submitBtn.classList.add('d-none');
        })
        .catch((err) => {
            console.error(err);
            submitBtn.disabled = false;
            submitBtn.innerText = 'Kirim Pengaduan';
        });
    });

    // Tombol OK di layar sukses
    okBtn.addEventListener('click', function () {
        if (modal) modal.hide();
    });

    // Reset saat modal ditutup
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            form.reset();
            resetErrors();

            successBox.classList.add('d-none');
            formWrap.classList.remove('d-none');

            // tampilkan kembali tombol submit & reset state
            submitBtn.classList.remove('d-none');
            submitBtn.disabled = false;
            submitBtn.innerText = 'Kirim Pengaduan';
        });
    }
});
</script>
@endpush
