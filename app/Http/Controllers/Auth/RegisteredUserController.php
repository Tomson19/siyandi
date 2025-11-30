<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'nik'   => ['required', 'digits:16', 'unique:users,nik'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users,email',
                'regex:/^[A-Za-z0-9._%+-]+@gmail\.com$/i',
            ],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)],
        ]);


        // ✅ BUAT USER BARU
        // NOTE:
        // password TIDAK di-Hash::make karena sudah di-cast 'hashed' di model User
        $user = User::create([
            'name'        => $request->name,
            'nik'         => $request->nik,
            'phone'       => $request->phone,
            'email'       => $request->email,
            'password'    => $request->password,
        ]);

        // ✅ TAMBAHKAN ROLE PEMOHON
        $user->assignRole('pemohon');

        // ✅ TRIGGER EVENT -> kirim email verifikasi
        event(new Registered($user));

        // ✅ LOGIN USER
        Auth::login($user);

        // ✅ ARAHKAN KE HALAMAN "CEK EMAIL UNTUK VERIFIKASI"
        // bukan langsung ke dashboard pemohon, supaya wajib verifikasi dulu
        return redirect()->route('verification.notice');
    }
}
