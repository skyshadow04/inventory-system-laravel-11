<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->is_superadmin) {
                return redirect('/superadmin');
            } elseif ($user->is_manager) {
                return redirect('/manager');
            } else {
                return redirect('/users');
            }
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_manager' => false,
            'is_verified' => false, // New users are unverified by default
        ]);

        // Don't auto-login; require super admin approval
        return redirect('/login')->with('success', 'Registration successful! Your account is pending approval from the super admin. You will receive an email once your account is approved.');
    }
}