<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\ConsultationMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LiveChatController extends Controller
{
    /**
     * User mulai chat (nama + pesan pertama)
     */
    public function start(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $consultation = Consultation::create([
            'public_token'    => (string) Str::uuid(),
            'guest_name'      => $data['name'],
            'guest_contact'   => null, // kalau nanti mau ditambah no WA/HP
            'status'          => 'open', // kalau kamu pakai status
            'last_message_at' => now(),
        ]);

        $message = $consultation->messages()->create([
            'sender_type' => 'guest',
            'sender_id'   => null,
            'message'     => $data['message'],
        ]);

        return response()->json([
            'token'   => $consultation->public_token,
            'message' => $message,
        ]);
    }

    /**
     * Ambil pesan untuk user (guest)
     * sekaligus tandai pesan VERIFIKATOR sebagai sudah dibaca user (read_by_guest_at)
     */
    public function messages(Request $request)
    {
        $request->validate([
            'token'    => 'required|string',
            'after_id' => 'nullable|integer',
        ]);

        // ⬅️ PENTING: pakai public_token (bukan token)
        $consultation = Consultation::where('public_token', $request->token)->firstOrFail();

        $query = $consultation->messages()->orderBy('id');

        if ($request->filled('after_id')) {
            $query->where('id', '>', $request->after_id);
        }

        $messages = $query->get();

        // ✅ tandai semua pesan dari verifikator sebagai sudah dibaca user
        $consultation->messages()
            ->where('sender_type', 'verifikator')
            ->whereNull('read_by_guest_at')
            ->update(['read_by_guest_at' => now()]);

        return response()->json($messages);
    }

    /**
     * Kirim pesan lanjutan dari guest
     */
    public function send(Request $request)
    {
        $data = $request->validate([
            'token'   => 'required|string',
            'message' => 'required|string',
        ]);

        // lagi-lagi pakai public_token biar konsisten
        $consultation = Consultation::where('public_token', $data['token'])->firstOrFail();

        $message = $consultation->messages()->create([
            'sender_type' => 'guest',
            'sender_id'   => null,
            'message'     => $data['message'],
        ]);

        $consultation->update([
            'last_message_at' => now(),
        ]);

        return response()->json($message);
    }

    /**
     * Status verifikator (online / terakhir dilihat)
     */
    public function status()
    {
        $verifikator = User::role('admin_verifikator')
            ->whereNotNull('last_seen_at')
            ->orderByDesc('last_seen_at')
            ->first();

        if (!$verifikator) {
            return response()->json([
                'online'          => false,
                'last_seen_raw'   => null,
                'last_seen_human' => null,
            ]);
        }

        $lastSeen = $verifikator->last_seen_at;
        $online   = $lastSeen->gt(now()->subMinutes(5)); // <= 5 menit dianggap online

        return response()->json([
            'online'          => $online,
            'last_seen_raw'   => $lastSeen->toIso8601String(),
            'last_seen_human' => $lastSeen->diffForHumans(),
        ]);
    }
}
