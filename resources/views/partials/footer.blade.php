<footer class="bg-success text-light pt-5 mt-5">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <h5 class="fw-bold mb-3">Tentang SIYANDI</h5>
        <p class="small">
          SIYANDI adalah sistem informasi yang dirancang untuk memberikan layanan, data, dan informasi kepada masyarakat secara cepat dan transparan.
        </p>
      </div>

      <div class="col-lg-4 col-md-6">
        <h5 class="fw-bold mb-3">Menu Cepat</h5>
        <ul class="list-unstyled small">
          <li class="mb-2">
            <a href="{{ url('/') }}" class="text-light text-decoration-none">
              <i class="bi bi-chevron-right me-1"></i> Home
            </a>
          </li>
          <li class="mb-2">
            <a href="{{ route('profile.struktur') }}" class="text-light text-decoration-none">
              <i class="bi bi-chevron-right me-1"></i> Profil
            </a>
          </li>
          <li class="mb-2">
            <a href="#" class="text-light text-decoration-none">
              <i class="bi bi-chevron-right me-1"></i> Layanan
            </a>
          </li>
          <li class="mb-2">
            <a href="#" class="text-light text-decoration-none">
              <i class="bi bi-chevron-right me-1"></i> Kontak
            </a>
          </li>
        </ul>
      </div>

      <div class="col-lg-4 col-md-12">
        <h5 class="fw-bold mb-3">Kontak</h5>
        <p class="small mb-1">
          <i class="bi bi-geo-alt-fill me-2"></i> Jl. Raya Pekanbaru-Bangkinang KM 28<br>
          Desa Kualu Nenas, Kecamatan Tambang,<br>
          Kabupaten Kampar, Provinsi Riau
        </p>
        <p class="small mb-1">
          <i class="bi bi-telephone me-2"></i> (021) 1234567
        </p>
        <p class="small mb-1">
          <i class="bi bi-envelope-at me-2"></i> info@siyandi.go.id
        </p>

        <div class="mt-2">
          <a href="#" class="text-light me-3 fs-5"><i class="bi bi-facebook"></i></a>
          <a href="#" class="text-light me-3 fs-5"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="text-light me-3 fs-5"><i class="bi bi-instagram"></i></a>
          <a href="#" class="text-light fs-5"><i class="bi bi-youtube"></i></a>
        </div>
      </div>
    </div>

    <hr class="border-light mt-4">

    <div class="text-center pb-3 small">
      &copy; <span id="year"></span> Dwi Gusrima Wijayanti. Hak cipta dilindungi.
    </div>
  </div>
</footer>

{{-- script untuk tahun otomatis --}}
@push('scripts')
<script>
  document.getElementById('year')?.append(new Date().getFullYear());
</script>
@endpush
