<?php

declare(strict_types=1);

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\DeveloperAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Masuk dan keluar panel admin.
 *
 * Formulir login dikirim lewat fetch supaya halaman bisa memutar overlay
 * "memverifikasi -> menyiapkan sesi -> membuka dashboard" tanpa berpindah
 * halaman di tengah jalan. Karena itu setiap jawaban punya dua bentuk: JSON
 * untuk fetch, dan redirect biasa untuk peramban yang JavaScript-nya mati —
 * formulirnya tetap bekerja walau skripnya gagal dimuat.
 */
class DeveloperAuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (session()->has('developer_id')) {
            return redirect()->route('admin.dashboard');
        }

        return view('developer.auth.login');
    }

    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'email'    => 'required|email|max:190',
            'password' => 'required|string|min:6|max:190',
        ]);

        $developer = DeveloperAccount::where('email', $data['email'])->first();

        // Pesan yang sama untuk email tak terdaftar maupun sandi salah, supaya
        // halaman ini tidak bisa dipakai menebak alamat mana yang terdaftar.
        if (! $developer || ! $developer->verifyPassword($data['password'])) {
            $pesan = 'Email atau kata sandi tidak cocok.';

            return $request->expectsJson()
                ? response()->json(['message' => $pesan, 'errors' => ['email' => [$pesan]]], 422)
                : back()->withErrors(['email' => $pesan])->withInput($request->only('email'));
        }

        // ID sesi DIBUAT ULANG setelah login. Tanpa ini, siapa pun yang sempat
        // menanamkan ID sesi ke peramban korban sebelum ia login (session
        // fixation) ikut masuk sebagai korban begitu korban login.
        $request->session()->regenerate();
        $request->session()->put([
            'developer_id'   => $developer->id,
            'developer_name' => $developer->name,
        ]);
        $developer->update(['last_login_at' => now()]);

        $tujuan = route('admin.dashboard');

        return $request->expectsJson()
            ? response()->json(['redirect' => $tujuan])
            : redirect()->to($tujuan . '?welcome=1');
    }

    public function logout(Request $request): RedirectResponse
    {
        // Sesi dan token CSRF dibuang seluruhnya, bukan hanya dua kuncinya —
        // kalau tidak, token lama tetap berlaku di tab yang masih terbuka.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
