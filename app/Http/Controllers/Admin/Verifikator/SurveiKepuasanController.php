<?php

namespace App\Http\Controllers\Admin\Verifikator;

use App\Http\Controllers\Controller;
use App\Models\SurveiKepuasan;
use Illuminate\Http\Request;

class SurveiKepuasanController extends Controller
{
    /**
     * Public: simpan survei (tanpa login, via AJAX atau form biasa).
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'q1_tampilan'    => ['required', 'string'],
                'q2_fitur'       => ['required', 'string'],
                'q3_informasi'   => ['nullable', 'string', 'max:1000'],
                'q4_sukai'       => ['nullable', 'string', 'max:1000'],
                'q5_kinerja'     => ['required', 'string'],
                'q6_rekomendasi' => ['nullable', 'string', 'max:1000'],
            ],
            [
                'q1_tampilan.required' => 'Silakan pilih rating tampilan situs.',
                'q2_fitur.required'    => 'Silakan pilih rating fitur situs.',
                'q5_kinerja.required'  => 'Silakan pilih rating kinerja situs.',
            ]
        );

        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();

        SurveiKepuasan::create($validated);

        // Kalau request dari JS (fetch) -> balas JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jawaban survei berhasil disimpan.',
            ]);
        }

        // Fallback: redirect biasa
        return back()->with('status', 'survei-saved');
    }

    /**
     * Admin verifikator: lihat hasil survei.
     */
    public function index(Request $request)
    {
        $rating = $request->query('rating');  // filter rating (q1/q2/q5)
        $search = $request->query('q');       // cari di komentar
        $from   = $request->query('from');    // tanggal dari
        $to     = $request->query('to');      // tanggal sampai

        $query = SurveiKepuasan::query();

        if ($rating) {
            // filter kalau salah satu rating sama dengan nilai yang dipilih
            $query->where(function ($q) use ($rating) {
                $q->where('q1_tampilan', $rating)
                  ->orWhere('q2_fitur', $rating)
                  ->orWhere('q5_kinerja', $rating);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('q3_informasi', 'like', "%{$search}%")
                  ->orWhere('q4_sukai', 'like', "%{$search}%")
                  ->orWhere('q6_rekomendasi', 'like', "%{$search}%");
            });
        }

        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        $surveiList = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // hitung ringkasan sederhana
        $total   = SurveiKepuasan::count();
        $ratingOptions = [
            'sangat_puas' => 'Sangat Puas',
            'puas'        => 'Puas',
            'cukup'       => 'Cukup',
            'kurang_puas' => 'Kurang Puas',
            'tidak_puas'  => 'Tidak Puas',
        ];

        return view('admin.verifikator.survei_kepuasan.index', compact(
            'surveiList',
            'rating',
            'search',
            'from',
            'to',
            'total',
            'ratingOptions',
        ));
    }
}
