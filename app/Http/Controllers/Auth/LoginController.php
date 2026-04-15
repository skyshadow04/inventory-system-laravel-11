<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect(Auth::user()->is_manager ? '/superadmin' : '/users');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $user = Auth::user();

            // Check if user is verified by super admin
            if (!$user->is_verified) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => ['Your account has not been verified by the super admin yet. Please wait for approval.'],
                ]);
            }

            $request->session()->regenerate();

            // Create user session record
            UserSession::create([
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'last_activity' => now(),
                'expires_at' => now()->addMinutes(30), // 30-minute timeout
            ]);

            // Redirect based on user role
            $user = Auth::user();
            if ($user->is_superadmin) {
                $redirectPath = '/superadmin';
            } elseif ($user->is_manager) {
                $redirectPath = '/manager'; // Assuming manager dashboard
            } elseif ($user->is_resource_officer) {
                $redirectPath = '/resource-officer';
            } else {
                $redirectPath = '/users';
            }

            return redirect()->intended($redirectPath);
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}