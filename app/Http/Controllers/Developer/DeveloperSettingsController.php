<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\DeveloperAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DeveloperSettingsController extends Controller
{
    public function index()
    {
        $developer = DeveloperAccount::find(session('developer_id'));
        return view('developer.settings', compact('developer'));
    }

    public function update(Request $request)
    {
        $developer = DeveloperAccount::findOrFail(session('developer_id'));

        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:developer_accounts,email,' . $developer->id,
            'current_password'      => 'nullable|string',
            'password'              => 'nullable|string|min:6|confirmed',
        ]);

        $developer->name = $request->name;
        $developer->email = $request->email;

        if ($request->filled('password')) {
            if (!$request->filled('current_password') || !Hash::check($request->current_password, $developer->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $developer->password = Hash::make($request->password);
        }

        $developer->save();

        session(['developer_name' => $developer->name]);

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully.');
    }
}
