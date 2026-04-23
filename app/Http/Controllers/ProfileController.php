<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the user's profile page
     */
    public function show()
    {
        $user = auth()->user();
        return view('users.profile', compact('user'));
    }

    /**
     * Update user profile information
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.show')->with('success', 'Password changed successfully!');
    }
}
