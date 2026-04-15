<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserSession;
use Illuminate\Support\Facades\Auth;

class TrackUserActivity
{
    private int $sessionTimeout = 30; // minutes

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $sessionId = session()->getId();

            // Find or create user session record
            $userSession = UserSession::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->first();

            if ($userSession) {
                // Check if session has expired
                if ($userSession->expires_at && $userSession->expires_at->isPast()) {
                    Auth::logout();
                    $userSession->delete();
                    session()->invalidate();
                    return redirect('/login')->with('message', 'Session expired. Please login again.');
                }

                // Update last activity
                $userSession->update([
                    'last_activity' => now(),
                    'expires_at' => now()->addMinutes($this->sessionTimeout),
                ]);
            } else {
                // Create new session record
                UserSession::create([
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'last_activity' => now(),
                    'expires_at' => now()->addMinutes($this->sessionTimeout),
                ]);
            }
        }

        return $next($request);
    }
}
