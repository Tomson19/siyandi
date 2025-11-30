@extends('layouts.halamanutama')

@section('content')
    <div class="container my-4">

        {{-- JUDUL --}}
        <div class="mb-3 text-center">
            <h2 class="fw-bold mb-1">Data Program & Kegiatan</h2>
            <p class="text-muted mb-0">
                Menampilkan data program & kegiatan untuk tahun
                <strong>{{ $tahun }}</strong>.
            </p>
        </div>

        {{-- KARTU UTAMA --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                {{-- FILTER --}}
                <form method="GET" action="{{ route('program_kegiatan.public') }}" class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small">Tahun</label>
                            <select name="tahun" class="form-select form-select-sm">
                                @foreach($listTahun as $th)
                                    <option value="{{ $th }}" {{ (string)$tahun === (string)$th ? 'selected' : '' }}>
                                        {{ $th }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label small">Komoditas (Jenis Tanaman)</label>
                            <select name="jenis_tanaman_id" class="form-select form-select-sm">
                                <option value="">-- Semua Komoditas --</option>
                                @foreach($jenisTanamanList as $jt)
                                    <option value="{{ $jt->id }}" {{ (string)$jenisTanamanId === (string)$jt->id ? 'selected' : '' }}>
                                        {{ $jt->nama_tanaman }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 d-flex justify-content-end gap-2">
                            <div>
                                <button type="submit" class="btn btn-sm btn-primary mt-3 mt-md-0">
                                    <i class="bi bi-funnel me-1"></i> Filter
                                </button>
                            </div>
                            <div>
                                <a href="{{ route('program_kegiatan.public') }}" class="btn btn-sm btn-outline-secondary mt-3 mt-md-0">
                                    Reset (Tahun {{ $currentYear }})
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- INFO KECIL DI ATAS TABEL --}}
                <div class="mb-2 small text-muted">
                    Menampilkan data untuk tahun <strong>{{ $tahun }}</strong>
                    @if($jenisTanamanId)
                        dan komoditas:
                        <strong>
                            {{ optional($jenisTanamanList->firstWhere('id', $jenisTanamanId))->nama_tanaman }}
                        </strong>
                    @else
                        (semua komoditas)
                    @endif
                </div>

                {{-- TABEL --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-sm mb-0 align-middle">
                        <thead class="table-light text-center small">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nama Program</th>
                                <th>Nama Kegiatan</th>
                                <th>Komoditas</th>
                                <th>Jumlah Produksi</th>
                                <th>Kebutuhan Benih (Batang)</th>
                                <th>Jenis Benih</th>
                                <th>Bidang</th>
                                <th style="width: 80px;">Tahun</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            @forelse($programs as $i => $row)
                                <tr>
                                    <td class="text-center">{{ $i + 1 }}</td>
                                    <td class="text-center">
                                        <strong>{{ $row->nama_program }}</strong>
                                    </td>
                                    <td class="text-center">{{ $row->nama_kegiatan }}</td>
                                    <td class="text-center">{{ $row->jenisTanaman->nama_tanaman ?? '-' }}</td>

                                    <td class="text-center">
                                        {{ $row->jumlah_produksi !== null ? number_format($row->jumlah_produksi, 0, ',', '.') : '-' }}
                                    </td>

                                    <td class="text-center">
                                        {{ $row->kebutuhan_benih !== null ? number_format($row->kebutuhan_benih, 0, ',', '.') : '-' }}
                                    </td>

                                    <td class="text-center">{{ $row->jenis_benih ?? '-' }}</td>
                                    <td class="text-center">{{ $row->bidang ?? '-' }}</td>
                                    <td class="text-center">{{ $row->tahun ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-3">
                                        Belum ada data program & kegiatan untuk tahun {{ $tahun }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
@endsection
