<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLiveChatController extends Controller
{
     public function index(Request $request)
    {
        $data = $this->buildListData($request);

        return view('admin.live-chat.index', $data);
    }

    public function card(Request $request)
    {
        $data = $this->buildListData($request);

        // ⬅️ partial untuk AJAX
        return view('admin.live-chat._card', $data);
    }

    /**
     * SEMUA logika ambil list konsultasi ada di sini,
     * dipakai index() dan card() supaya hasilnya SELALU sama.
     */
    private function buildListData(Request $request): array
    {
        $filter = $request->query('filter', 'all');

        $query = Consultation::query()
            ->orderByDesc('last_message_at');

        // filter "Belum dibaca"
        if ($filter === 'unread') {
            $query->whereHas('messages', function ($q) {
                $q->where('sender_type', 'guest')
                  ->whereNull('read_at');
            });
        }

        // filter "Selesai" — sesuaikan value status di DB
        if ($filter === 'done') {
            $query->whereIn('status', ['closed', 'selesai', 'done']);
        }

        // ⬅️ INI PENTING: dua-duanya pakai take(50)
        $consultations = $query
            ->take(100)
            ->get();

        $unreadThreadsCount = Consultation::whereHas('messages', function ($q) {
            $q->where('sender_type', 'guest')
              ->whereNull('read_at');
        })->count();

        return compact('consultations', 'filter', 'unreadThreadsCount');
    }


    /**
     * Halaman detail chat (view saja, pesan di-load via AJAX)
     */
    public function show(Consultation $consultation)
    {
        return view('admin.live-chat.show', compact('consultation'));
    }

    /**
     * JSON: ambil semua pesan untuk 1 consultation
     * + tandai pesan USER sebagai sudah dibaca admin (read_at)
     */
    public function messages(Request $request, Consultation $consultation)
    {
        // kalau mau pakai after_id lagi, bisa aktifkan block validasi + filter,
        // tapi script JS kita sekarang ambil semua pesan tiap polling.

        $messages = $consultation->messages()
            ->orderBy('id')
            ->get();

        // 🔹 tandai semua pesan guest yang belum dibaca sebagai "read"
        $consultation->messages()
            ->where('sender_type', 'guest')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    /**
     * JSON: kirim balasan dari VERIFIKATOR (admin)
     */
    public function reply(Request $request, Consultation $consultation)
    {
        $data = $request->validate([
            'message' => 'required|string',
        ]);

        $message = $consultation->messages()->create([
            'sender_type'      => 'verifikator',
            'sender_id'        => Auth::id(),
            'message'          => $data['message'],
            // pastikan mulai dari BELUM dibaca user
            'read_by_guest_at' => null,
        ]);

        $consultation->update([
            'last_message_at' => now(),
        ]);

        return response()->json($message);
    }
}
