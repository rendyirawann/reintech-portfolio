<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\DeveloperAccount;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DeveloperAuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (session()->has('developer_id')) {
            return redirect()->route('developer.dashboard');
        }

        return view('developer.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $developer = DeveloperAccount::where('email', $request->email)->first();

        if (!$developer || !$developer->verifyPassword($request->password)) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->withInput();
        }

        session(['developer_id' => $developer->id, 'developer_name' => $developer->name]);
        $developer->update(['last_login_at' => now()]);

        return redirect()->route('developer.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(['developer_id', 'developer_name']);

        return redirect()->route('developer.login');
    }
}
