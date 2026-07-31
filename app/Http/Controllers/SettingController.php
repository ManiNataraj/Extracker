<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return view('settings.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'currency_symbol' => 'required|string|max:10',
            'monthly_budget_limit' => 'nullable|numeric|min:0',
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Profile & Settings updated successfully!');
    }

    public function toggleDarkMode(Request $request)
    {
        $user = $request->user();
        $user->update(['dark_mode' => !$user->dark_mode]);

        return response()->json(['success' => true, 'dark_mode' => $user->dark_mode]);
    }
}
