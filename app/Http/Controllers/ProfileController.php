<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmNewEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil.
     */
    public function edit(Request $request)
    {
        return view('settings.profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update data profil.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // === Aturan dasar (untuk semua role) ===
        $nameRules  = ['required', 'string', 'max:255'];
        $emailRules = [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($user->id),
        ];

        // Default: admin → nik & phone opsional
        $nikRules   = ['nullable', 'digits:16', Rule::unique('users', 'nik')->ignore($user->id)];
        $phoneRules = ['nullable', 'string', 'max:20'];

        // === Kalau PEMOHON: wajib nik, phone, harus Gmail ===
        if ($user->hasRole('pemohon')) {
            $nikRules[0]   = 'required'; // dari nullable -> required
            $phoneRules[0] = 'required';
            $emailRules[]  = 'regex:/^[A-Za-z0-9._%+-]+@gmail\.com$/i';
        }

        $validated = $request->validate(
            [
                'name'  => $nameRules,
                'nik'   => $nikRules,
                'phone' => $phoneRules,
                'email' => $emailRules,
            ],
            [
                'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
                'email.regex'  => 'Email harus menggunakan alamat Gmail yang valid.',
            ]
        );


        $newEmail     = $validated['email'];
        $emailChanged = $newEmail !== $user->email;

        // Diisi semua role
        $user->name = $validated['name'];

        // Hanya pemohon yang pakai NIK & phone
        if ($user->hasRole('pemohon')) {
            $user->nik   = $validated['nik'];
            $user->phone = $validated['phone'];
        }

        // === PEMOHON: email pakai mekanisme pending_email ===
        if ($user->hasRole('pemohon')) {

            if ($emailChanged) {
                // simpan email baru di pending_email
                $user->pending_email = $newEmail;
                // JANGAN sentuh email/email_verified_at dulu
            }

            $user->save();

            // Kirim email konfirmasi hanya kalau email memang berubah
            if ($emailChanged) {
                $url = URL::temporarySignedRoute(
                    'settings.email.confirm',
                    now()->addMinutes(60),
                    [
                        'id'    => $user->id,
                        'email' => $user->pending_email,
                    ]
                );

                Mail::to($user->pending_email)->send(new ConfirmNewEmail($user, $url));

                return back()->with('status', 'profile-updated-pending-email');
            }

            // kalau email tidak berubah, anggap profil sudah diperbarui biasa
            return back()->with('status', 'profile-updated');
        }

        // === ADMIN / ROLE LAIN: email langsung diubah seperti biasa ===
        $user->email = $newEmail;
        $user->save();

        return back()->with('status', 'profile-updated');
    }


    /**
     * Konfirmasi perubahan email (klik dari link di Gmail).
     */
    public function confirmEmailChange(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = User::findOrFail($id);

        $emailFromLink = $request->query('email');

        // Harus ada pending_email dan sama dengan yang ada di URL
        if (! $user->pending_email || $user->pending_email !== $emailFromLink) {
            abort(404);
        }

        // Double check: pastikan email ini belum dipakai user lain
        $emailAlreadyUsed = User::where('email', $emailFromLink)
            ->where('id', '!=', $user->id)
            ->exists();

        if ($emailAlreadyUsed) {
            // Batalkan pending_email
            $user->pending_email = null;
            $user->save();

            return redirect()
                ->route('login')
                ->with('status', 'email-change-failed');
        }

        // Pindahkan pending_email ke email utama
        $user->email = $user->pending_email;
        $user->pending_email = null;

        // Anggap email ini sekarang sudah terverifikasi
        $user->email_verified_at = now();
        $user->save();

        // AUTO LOGIN user setelah konfirmasi email
        Auth::login($user);

        // Redirect sesuai role masing-masing
        return $this->redirectAfterEmailChange($user);
    }


    protected function redirectAfterEmailChange($user)
    {
        // pesan status yang mau ditampilkan setelah redirect
        $status = ['status' => 'email-change-confirmed'];

        // 1. Pemohon
        if ($user->hasRole('pemohon')) {
            return redirect()
                ->route('pemohon.dashboard')
                ->with($status);
        }

        // 2. Super admin
        if ($user->hasRole('admin_super')) {
            return redirect()
                ->route('admin.super.dashboard')
                ->with($status);
        }

        // 3. Operator
        if ($user->hasRole('admin_operator')) {
            return redirect()
                ->route('admin.operator.dashboard')
                ->with($status);
        }

        // 4. Verifikator
        if ($user->hasRole('admin_verifikator')) {
            return redirect()
                ->route('admin.verifikator.dashboard')
                ->with($status);
        }

        // 5. Keuangan
        if ($user->hasRole('admin_keuangan')) {
            return redirect()
                ->route('admin.keuangan.dashboard')
                ->with($status);
        }

        // 6. Admin kabupaten
        if ($user->hasAnyRole([
            'admin_pekanbaru',
            'admin_kampar',
            'admin_bengkalis',
            'admin_indragiri_hulu',
            'admin_indragiri_hilir',
            'admin_kuantan_singingi',
            'admin_pelalawan',
            'admin_rokan_hilir',
            'admin_rokan_hulu',
            'admin_siak',
            'admin_kepulauan_meranti',
            'admin_dumai',
        ])) {
            return redirect()
                ->route('admin.kabupaten.dashboard')
                ->with($status);
        }

        // 7. Admin UPT Sertifikasi → arahkan ke daftar penangkar
        if ($user->hasRole('admin_upt_sertifikasi')) {
            return redirect()
                ->route('admin.upt_sertifikasi.penangkar.index')
                ->with($status);
        }

        // 8. Fallback kalau role tidak dikenali
        return redirect()
            ->route('home')
            ->with($status);
    }
}
